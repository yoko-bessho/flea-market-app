<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class GetMyListTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $this->category = Category::create([
            'name' => 'ファッション',
        ]);
    }

    protected function createTestItems(
        int $count = 3,
        ?User $user = null,
        bool $withComments = true,
        bool $withLikes = true
    ) {
        $user ??= $this->user;
        $items = collect();

        for ($i = 0; $i < $count; $i++) {
            $item = Item::create([
                'user_id' => $user->id,
                'title' => '商品名' . $i . ($user->id === $this->user->id ? '' : '_他人の出品'),
                'price' => 1000 * $i,
                'brand' => 'ブランド' . $i,
                'description' => '商品の説明' . $i,
                'image_path' => 'item_images/sample.jpg',
                'condition' => ItemCondition::GOOD,
                'is_sold' => false,
            ]);

            $item->categories()->attach($this->category->id);

            if ($withComments) {
                $item->commentingUsers()->attach($this->user->id, ['text' => 'コメント' . $i]);
            }
            if ($withLikes) {
                $item->likes()->attach($this->user->id);
            }

            $items->push($item);
        }
            return $items;
    }


// マイリスト一覧取得
    public function test_get_mylist_items_success()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items->each(function ($item) {
            $item->likes()->attach($this->user->id);
        });

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $viewItems = $response->viewData('mylistItems');

        $this->assertEquals(
            $items->pluck('id')->sort()->values()->toArray(),
            $viewItems->pluck('id')->sort()->values()->toArray()
        );
    }


    public function test_get_mylist_items_soldOut_label()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items->each(function ($item) {
            $item->likes()->attach($this->user->id);
        });

        $items[0]->update(['is_sold' => true]);
        $items[1]->update(['is_sold' => true]);
        $items[2]->update(['is_sold' => false]);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $this->assertStringContainsString('sold out', $response->getContent());

        $this->assertSame(
            2, substr_count($response->getContent(), 'sold out')
        );
    }

    public function test_guest_cannot_see_mylist_items()
    {
        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items[0]->update(['is_sold' => true]);
        $items[1]->update(['is_sold' => true]);
        $items[2]->update(['is_sold' => false]);


        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $response->assertDontSee($items[0]->title);
        $response->assertDontSee($items[1]->title);
        $response->assertDontSee($items[2]->title);
    }

}