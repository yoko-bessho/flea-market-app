<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class GetItemListTest extends TestCase
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


// 商品一覧取得
    public function test_get_items_success()
    {
        $items = $this->createTestItems(3, null, false, false);

        $response = $this->get('/');
        $response->assertStatus(200);

        $viewItems = $response->viewData('recommendedItems');

        $this->assertEquals(
            $items->pluck('id')->sort()->values()->toArray(),
            $viewItems->pluck('id')->sort()->values()->toArray()
        );
    }


    public function test_get_items_soldOut_label()
    {
        $items = Item::create([
            'user_id' => $this->user->id,
            'title' => '腕時計',
            'price' => '15000',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ時計',
            'image_path' => 'item_images/Armani_Mens_Clock.jpg',
            'condition' => ItemCondition::GOOD,
            'is_sold' => true,
        ]);

        $items->categories()->attach($this->category->id);

        $response = $this->get('/?tab=recommended');
        $response->assertStatus(200);

        $response->assertSee('sold out');
    }


    public function test_get_items_authUser_view()
    {
        $this->actingAs($this->user);

        $mylistItems = $this->createTestItems(2, $this->user, false, false);

        $otherUser = User::factory()->create();
        $otherItems = $this->createTestItems(3, $otherUser);

        $response = $this->get('/?tab=recommended');
        $response->assertStatus(200);

        $viewItems = $response->viewData('recommendedItems');

        $this->assertEmpty(
            $viewItems->whereIn('id', $mylistItems->pluck('id'))->toArray()
        );
        foreach ($otherItems as $item) {
            $this->assertTrue($viewItems->contains('id', $item->id));
        }
    }
}