<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class LikeTest extends TestCase
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



    public function test_user_can_like_item_and_like_count_increases()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();

        $beforeCount = $item->likes()->count();
        $this->assertEquals(0, $beforeCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response = $this->post(route('item.like', $item));

        $item->refresh();

        $afterCount = $item->likes()->count();

        $this->assertEquals($beforeCount + 1, $afterCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertSee((string)$afterCount);
    }


    public function test_like_icom_change_color()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();

        $item->likes()->attach($this->user->id);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('class="button-like button-unlike"', false);
    }


    public function test_user_can_unlike_item_and_count_decreases()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();
        
        $this->user->mylistItems()->attach($item->id);

        $this->assertDatabaseHas('likes', [
        'user_id' => $this->user->id,
        'item_id' => $item->id,
        ]);
        $beforeCount = $item->likes()->count();

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response = $this->post("/item/{$item->id}/like");
        $response->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $afterCount = $item->likes()->count();

        $this->assertEquals($beforeCount - 1, $afterCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee((string)$afterCount);
    }

}