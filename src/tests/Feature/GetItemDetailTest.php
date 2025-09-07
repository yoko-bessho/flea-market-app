<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class GetItemDetailTest extends TestCase
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



    public function test_item_detail_page_displays_item_info()
    {
        $otherUser = User::factory()->create();
        $item = $this->createTestItems(1, $otherUser, true, true)->first();

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee($item->title);
        $response->assertSee($item->price);
        $response->assertSee($item->brand);
        $response->assertSee($item->description);
        $response->assertSee(ItemCondition::label($item->condition));

        $response->assertSee($this->category->name);
        $response->assertSee($item->likes()->count());
        $response->assertSee($item->commentingUsers()->count());

        $item->commentingUsers->each(function ($user) use ($response) {
        $pivotText = $user->pivot->text;
        $response->assertSee($user->name);
        $response->assertSee($pivotText);
        });
    }



    public function test_item_detail_page_displays_multiple_categoryies()
    {
        $otherUser = User::factory()->create();

        $category1 = Category::create(['name' => '子供']);
        $category2 = Category::create(['name' => '家電']);
        $category3 = Category::create(['name' => 'スポーツ']);

        $item = $this->createTestItems(1, $otherUser, true, true)->first();

        $item->categories()->attach([$category1->id, $category2->id, $category3->id]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee($category3->name);
    }

}