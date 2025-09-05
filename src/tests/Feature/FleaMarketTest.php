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


class FleaMarketTest extends TestCase
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

    /**
     * A basic feature test example.
     *
     * @return void
     */

// 会員登録画面
    public function test_register_userName_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください。',
        ]);
    }

    public function test_register_userEmail_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください。',
        ]);
    }

    public function test_register_userPassword_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください。',
        ]);
    }

    public function test_register_userPassword_too_short_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください。',
        ]);
    }

    public function test_register_userPasswordConfirmation_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '1234567',
            'password_confirmation' => '1234568',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません。',
        ]);
    }

    public function test_register_user_success_redirects_to_profilePage()
    {
        app()->setLocale('ja');

        $email = $this->faker->unique()->safeEmail();

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users',[
            'name'=>'testName',
            'email'=> $email,
        ]);

        $user = User::where('email', $email)->first();
        $this->assertTrue(Hash::check('password', $user->password));

        $response->assertRedirect(route('setProfile'));
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


// 商品一覧取得
    public function test_get_items_success()
    {
        // $user = User::factory()->create();
        // $category = Category::create(['name' => 'ファッション']);
        // $items = Item::create([
        //     'user_id' => $user->id,
        //     'title' => '腕時計',
        //     'price' => '15000',
        //     'brand' => 'Rolax',
        //     'description' => 'スタイリッシュなデザインのメンズ時計',
        //     'image_path' => 'item_images/Armani_Mens_Clock.jpg',
        //     'is_sold' => false,
        //     'categories' => $category->id,
        //     'condition' => ItemCondition::GOOD,
        // ]);
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