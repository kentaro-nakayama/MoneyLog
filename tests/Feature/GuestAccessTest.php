<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 保護されたルートの一覧。ゲストはどれもログイン画面に飛ばされる。
     *
     * @return array<string, array{string, string}>
     */
    public static function protectedRoutes(): array
    {
        return [
            'ホーム' => ['get', '/'],
            '登録フォーム' => ['get', '/register_form'],
            '登録' => ['post', '/register'],
            '編集フォーム' => ['get', '/edit_form/1'],
            '編集' => ['post', '/edit/1'],
            '削除' => ['post', '/delete/1'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('protectedRoutes')]
    public function test_guest_is_redirected_to_the_login_page(string $method, string $uri): void
    {
        $this->$method($uri)->assertRedirect('/login');
    }

    public function test_logged_in_user_can_open_the_home_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_guest_can_open_the_login_and_signup_pages(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/signup')->assertOk();
    }

    public function test_logged_in_user_is_sent_away_from_the_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect('/');
    }
}
