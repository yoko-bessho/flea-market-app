<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Enums\ItemCondition;
use App\Services\StripeSessionWrapper;


class ChangeShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->category = Category::create([
            'name' => 'ファッション',
        ]);
    }

    protected function createTestItems(
        int $count = 3,
        ?User $user = null,
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
            $items->push($item);
        }
            return $items;
    }




    public function test_Applied_to_purchase_screen_and_register_shippingAddress()
    {
        $otherUser = User::factory()->create();
        $item = $this->createTestItems(1, $otherUser)->first();

        $this->actingAs($this->user);

        $this->get(route('purchase.address.edit',  $item->id))
            ->assertStatus(200);

        $response = $this->post(route('address.update', $this->user->id), [
            'postal_code' => '123-4567',
            'address' => '東京都',
        ]);

        $this->user->refresh();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'postal_code' => '123-4567',
            'address' => '東京都',
        ]);

        $response = $this->get(route('purchase.address.edit', $item->id));

        $response->assertStatus(200);
        $response->assertSee('東京都');




        $mockSession = new class {
            public $id = 'test_session_id';
            public $url = 'https://example.com/checkout';
        };

        $mockwrapper = $this->createMock(StripeSessionWrapper::class);
        $mockwrapper->expects($this->once())
            ->method('create')
            ->willReturn($mockSession);

        $this->app->instance(StripeSessionWrapper::class, $mockwrapper);

        $response = $this->post(route('checkout'), [
        'item_id' => $item->id,
        'payment_method' => 'card',
        ]);

        $response->assertRedirect('https://example.com/checkout');

        $this->assertDatabaseHas('purchases', [
        'item_id' => $item->id,
        'buyer_id' => $this->user->id,
        'shipping_postal_code' => '123-4567',
        'shipping_address' => '東京都',
        ]);
    }

}
