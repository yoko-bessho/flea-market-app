<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;

use App\Services\StripeSessionWrapper;


class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
        ]);

        $this->seller = User::factory()->create();

        $this->item = Item::create([
            'user_id' => $this->seller->id,
            'title' => '商品名',
            'price' => 1000,
            'brand' => 'ブランド',
            'description' => '商品の説明',
            'image_path' => 'item_images/sample.jpg',
            'condition' => ItemCondition::GOOD,
            'is_sold' => false,
        ]);
    }

    public function test_user_can_checkout_item()
    {
        $this->actingAs($this->buyer);

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
            'item_id' => $this->item->id,
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('https://example.com/checkout');

        $this->assertDatabaseHas('purchases', [
            'item_id' => $this->item->id,
            'buyer_id' => $this->buyer->id,
            'checkout_session_id' => 'test_session_id',
            'payment_status' => 'pending',
            'shipping_postal_code' => $this->buyer->postal_code,
            'shipping_address' => $this->buyer->address,
            'payment_method' => 'card',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'is_sold' => false,
        ]);


        $payload = json_encode([
            'id' => 'evt_test',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'test_session_id',
                    'metadata' => [
                        'item_id' => $this->item->id,
                    ],
                ],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/api/stripe/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Stripe-Signature' => 'dummy_signature',
            ],
            $payload
        );

        $response->assertNoContent();

        $this->assertDatabaseHas('purchases', [
            'checkout_session_id' => 'test_session_id',
            'payment_status' => 'success',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'is_sold' => true,
        ]);

        $response = $this->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('sold out');


        $this->buyer->refresh();
        $purchases = $this->buyer->purchases()->with('item')->get();
        $purchasedItem = $purchases->first()->item;

        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee($purchasedItem->title);

    }
}

