<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;
use Carbon\Factory;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Illuminate\Support\Facades\Hash;


class MypageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'ログイン者',
            'email' => 'test@example.com',
            'profile_image' => 'profile_images/sample.jpg'
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
                'is_sold' => true,
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


    public function test_user_profile_displays_correct_information()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();

        $sellItem = $this->createTestItems(1, null, false, false)->first();

        $buyItem = $this->createTestItems(1, $otherUser, false, false)->first();
        $this->user->purchases()->create([
          'item_id' => $buyItem->id,
          'buyer_id' => $sellItem->id,
          'shipping_postal_code' => '123-4567',
          'shipping_address' => '東京都新宿区テスト1-2-3',
          'payment_method' => 'card',
          'payment_status' => 'success',
        ]);

        $otherUser->purchases()->create([
            'item_id' => $buyItem->id,
            'buyer_id' => $otherUser->id,
            'shipping_postal_code' => '987-6543',
            'shipping_address' => '大阪府大阪市テスト4-5-6',
            'payment_method' => 'card',
            'payment_status' => 'success',
        ]);

        
        $response = $this->get(route('mypage', ['page' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->profile_image);
        $response->assertSee($sellItem->title);

        $response = $this->get(route('mypage', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($buyItem->title);


}



}