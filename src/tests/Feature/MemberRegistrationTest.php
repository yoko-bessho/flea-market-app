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


class MemberRegistrationTest extends TestCase
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


    public function test_register_user_success_redirects_to_indexPage()
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

        $response->assertRedirect(route('items.index'));
    }

}