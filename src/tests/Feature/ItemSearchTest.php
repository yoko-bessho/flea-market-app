<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class ItemSearchTest extends TestCase
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



    public function test_search_items_by_partial_name()
    {
        $items = $this->createTestItems(3, null, false, false);

        $items[0]->update(['title' => 'HDD']);
        $items[1]->update(['title' => '玉ねぎ３束']);
        $items[2]->update(['title' => '腕時計']);

        $response = $this->get(route('items.index', ['keyword' => '時計']));
        $response->assertStatus(200);

        $response->assertDontSee($items[0]->title);
        $response->assertDontSee($items[1]->title);
        $response->assertSee($items[2]->title);
    }



    public function test_search_keyword_is_preserved_in_mylist_tab()
    {
        $this->actingAs($this->user);
        
        $otherUser = User::factory()->create();

        $mylistItem = $this->createTestItems(1, $otherUser, false, false)->first();
        $mylistItem->update(['title' => '腕時計']);
        $mylistItem->likes()->attach($this->user->id);

        $otherItem = $this->createTestItems(1, $otherUser, false, false)->first();
        $otherItem->update(['title' => 'スニーカー']);

        $response = $this->get(route('items.index', [
            'keyword' => '時計',
            'tab' => 'recommended',
        ]));
        $response->assertStatus(200);
        $response->assertSee('value="時計"', false);

        $response->assertSee($mylistItem->title);
        $response->assertDontSee($otherItem->title);

        $response = $this->get(route('items.index', [
            'keyword' => '時計',
            'tab' => 'mylist',
        ]));

        $response->assertStatus(200);
        $response->assertSee('value="時計"', false);

        $response->assertSee($mylistItem->title);
        $response->assertDontSee($otherItem->title);
    }
}