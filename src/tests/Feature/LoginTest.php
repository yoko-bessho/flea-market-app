<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;


class LoginTest extends TestCase
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


// ログイン機能
    public function test_login_userEmail_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください。',
        ]);
    }


    public function test_login_userPassword_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください。',
        ]);
    }


    public function test_login_userEmail_wrong_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'notexit' . time() . '@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。',
        ]);
    }


    public function test_login_userPassword_wrong_validationMessage()
    {
        app()->setLocale('ja');

        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。',
        ]);
    }


    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($user);
    }



// ログアウト機能
    public function test_logout_success()
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

}