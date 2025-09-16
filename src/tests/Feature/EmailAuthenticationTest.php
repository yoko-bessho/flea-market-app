<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * メール認証機能: 会員登録後、認証メールが送信される
     *
     * 1. 会員登録をする
     * 2. 認証メールを送信する
     */
    public function a_verification_email_is_sent_upon_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * @test
     * メール認証機能: 認証リンクをクリックすると認証が完了する
     *
     * 1. メール認証導線画面を表示する (このテストではURL直接アクセスで代替)
     * 2. 「認証はこちらから」ボタンを押下 (このテストでは認証URLを直接生成してアクセス)
     * 3. メール認証サイトを表示する (認証後のリダイレクト先を確認)
     */
    public function user_can_verify_their_email_by_clicking_the_verification_link()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }
}
