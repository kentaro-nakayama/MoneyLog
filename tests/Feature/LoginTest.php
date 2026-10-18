<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'correct-horse-battery';

    private function makeUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'taro@example.com',
            'password' => Hash::make(self::PASSWORD),
        ], $overrides));
    }

    public function test_user_can_log_in_with_correct_credentials(): void
    {
        $user = $this->makeUser();

        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_a_wrong_password(): void
    {
        $this->makeUser();

        $response = $this->from('/login')->post('/login', [
            'email' => 'taro@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_for_an_email_that_does_not_exist(): void
    {
        $this->makeUser();

        $response = $this->from('/login')->post('/login', [
            'email' => 'nobody@example.com',
            'password' => self::PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_the_error_message_does_not_reveal_which_field_was_wrong(): void
    {
        $this->makeUser();

        $response = $this->from('/login')->post('/login', [
            'email' => 'taro@example.com',
            'password' => 'wrong-password',
        ]);

        // 「このメールアドレスは存在しない」と分かってしまうと、
        // 登録済みのメールアドレスを総当たりで調べられてしまう
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません．',
        ]);
    }

    public function test_email_and_password_are_required(): void
    {
        $response = $this->from('/login')->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_email_must_look_like_an_email_address(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'not-an-email',
            'password' => self::PASSWORD,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_returns_to_the_page_they_originally_asked_for(): void
    {
        $this->makeUser();

        // ゲストのまま登録フォームを開こうとすると、ログイン画面に飛ばされる
        $this->get('/register_form')->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect('/register_form');
    }

    public function test_remember_me_issues_a_recaller_cookie(): void
    {
        // まだ remember_token を持っていないユーザー。
        // Laravel は token が空のときだけ新しく発行するので、この状態から試す
        $user = $this->makeUser(['remember_token' => null]);

        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => self::PASSWORD,
            'remember' => '1',
        ]);

        $response->assertCookie(Auth::guard()->getRecallerName());
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_no_recaller_cookie_when_remember_me_is_unchecked(): void
    {
        $this->makeUser();

        $response = $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => self::PASSWORD,
        ]);

        $response->assertCookieMissing(Auth::guard()->getRecallerName());
    }

    public function test_login_is_blocked_after_five_failed_attempts(): void
    {
        $this->makeUser();

        // throttle:5,1 なので、1分間に5回までは試せる
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')
                ->post('/login', ['email' => 'taro@example.com', 'password' => 'wrong-password'])
                ->assertRedirect('/login');
        }

        $this->from('/login')
            ->post('/login', ['email' => 'taro@example.com', 'password' => 'wrong-password'])
            ->assertStatus(429);
    }

    public function test_throttling_blocks_even_the_correct_password(): void
    {
        $this->makeUser();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post('/login', ['email' => 'taro@example.com', 'password' => 'wrong-password']);
        }

        // 回数を使い切ったあとは、正しいパスワードでも通さない
        $this->post('/login', [
            'email' => 'taro@example.com',
            'password' => self::PASSWORD,
        ])->assertStatus(429);

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_guest_cannot_reach_the_logout_route(): void
    {
        $this->post('/logout')->assertRedirect('/login');
    }

    public function test_home_is_not_reachable_after_logging_out(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->get('/')->assertOk();
        $this->post('/logout');

        $this->get('/')->assertRedirect('/login');
    }
}
