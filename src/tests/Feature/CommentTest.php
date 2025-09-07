<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;


class CommentTest extends TestCase
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



    public function test_logged_in_user_can_post_comment()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->post(route('item.comment', $item->id), [
            'text' => 'テストコメント',
        ]);

        $response->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'text'    => 'テストコメント',
        ]);
    }


    public function test_guest_cannot_post_comment()
    {
        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->post(route('item.comment', $item->id), [
            'text' => 'ゲストのコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'text'    => 'ゲストのコメント',
        ]);
    }


    public function test_comment_validation_error_when_empty()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->from(route('item.detail', $item->id))
                        ->post(route('item.comment', $item->id), [
                            'text' => '',
                        ]);

        $response->assertRedirect(route('item.detail', $item->id));

        $response->assertSessionHasErrors([
            'text' => 'コメントを入力してください。',
        ]);
    }


    public function test_comment_validation_error_when_too_long()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $longText = str_repeat('あ', 256);

        $response = $this->from(route('item.detail', $item->id))
                        ->post(route('item.comment', $item->id), [
                            'text' => $longText,
                        ]);

        $response->assertRedirect(route('item.detail', $item->id));

        $response->assertSessionHasErrors([
            'text' => 'コメントは255文字以内で入力してください',
        ]);
    }
}