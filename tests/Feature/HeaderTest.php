<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HeaderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインが必要な画面。どれもヘッダーにユーザー名とログアウトが出る。
     *
     * @return array<string, array{string}>
     */
    public static function pagesBehindLogin(): array
    {
        return [
            'ホーム' => ['/'],
            '登録フォーム' => ['/register_form'],
            '編集フォーム' => ['/edit_form/%d'],
        ];
    }

    #[DataProvider('pagesBehindLogin')]
    public function test_header_shows_the_user_name_and_logout_button(string $uri): void
    {
        $user = User::factory()->create(['name' => 'ネコ太郎']);
        $transaction = Transaction::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(sprintf($uri, $transaction->id));

        $response->assertOk();
        $response->assertSee('ネコ太郎');
        $response->assertSee('ログアウト');
        $response->assertSee('action="' . url('/logout') . '"', false);
    }

    /**
     * ヘッダーのタイトルがホーム画面へのリンクになっていること。
     */
    #[DataProvider('pagesBehindLogin')]
    public function test_the_title_links_to_the_home_page(string $uri): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(sprintf($uri, $transaction->id));

        $response->assertOk();
        $response->assertSee('<a href="' . route('home') . '" class="home-link">', false);
        $response->assertSee('MoneyLog');
    }

    /**
     * 未ログインの画面には、押してもログイン画面に戻されるだけのリンクを出さない。
     */
    public function test_login_and_signup_pages_have_no_home_link(): void
    {
        $this->get('/login')->assertDontSee('home-link');
        $this->get('/signup')->assertDontSee('home-link');
    }

    /**
     * ヘッダーから戻れるようになったので、画面ごとの「ホーム」ボタンは置かない。
     */
    public function test_pages_behind_login_have_no_separate_back_to_home_button(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->for($user)->create();

        $this->actingAs($user)->get('/register_form')->assertDontSee('back-to-home');
        $this->actingAs($user)->get("/edit_form/{$transaction->id}")->assertDontSee('back-to-home');
    }

    public function test_login_and_signup_pages_have_no_logout_button(): void
    {
        $this->get('/login')->assertDontSee('ログアウト');
        $this->get('/signup')->assertDontSee('ログアウト');
    }

    public function test_user_name_is_escaped_in_the_header(): void
    {
        $user = User::factory()->create(['name' => '<script>alert(1)</script>']);

        $response = $this->actingAs($user)->get('/');

        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
