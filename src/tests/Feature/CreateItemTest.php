<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;
use Illuminate\Http\UploadedFile;

class CreateItemTest extends TestCase
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

    public function test_create_item_success()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('itemCreate'));
        $response->assertStatus(200);

        $itemData = [
            'category_id' => [$this->category->id],
            'condition' => ItemCondition::GOOD,
            'title' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品です。',
            'price' => 1000,
            'image' => UploadedFile::fake()->image('test.png'),
        ];

        $response = $this->post(route('itemStore'), $itemData);

        $response->assertRedirect(route('mypage', ['page' => 'sell']));

        $this->assertDatabaseHas('items', [
            'user_id' => $this->user->id,
            'title' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品です。',
            'price' => 1000,
            'condition' => ItemCondition::GOOD,
        ]);

        $item = Item::where('title', 'テスト商品')->first();
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $this->category->id,
        ]);
    }
}
