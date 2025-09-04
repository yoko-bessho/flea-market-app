<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class FleaMarketTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}