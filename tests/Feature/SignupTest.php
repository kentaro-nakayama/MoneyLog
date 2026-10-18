<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SignupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しく埋まっているフォームの内容。各テストで一部だけ差し替えて使う。
     *
     * @return array<string, string>
     */
    private function validForm(array $overrides = []): array
    {
        return array_merge([
            'name' => 'ネコ太郎',
            'email' => 'neko@example.com',
            'password' => 'correct-horse',
            'password_confirmation' => 'correct-horse',
        ], $overrides);
    }

    public function test_signup_creates_the_user_and_logs_them_in(): void
    {
        $response = $this->post('/signup', $this->validForm());

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'name' => 'ネコ太郎',
            'email' => 'neko@example.com',
        ]);
        $this->assertAuthenticatedAs(User::where('email', 'neko@example.com')->first());
    }

    public function test_password_is_stored_hashed_not_in_plain_text(): void
    {
        $this->post('/signup', $this->validForm());

        $user = User::where('email', 'neko@example.com')->first();

        $this->assertNotSame('correct-horse', $user->password);
        $this->assertTrue(Hash::check('correct-horse', $user->password));
    }

    public function test_a_new_user_starts_with_no_transactions(): void
    {
        $this->post('/signup', $this->validForm());

        $user = User::where('email', 'neko@example.com')->first();

        $this->assertSame(0, $user->transactions()->count());
        $this->actingAs($user)->get('/')->assertOk();
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'neko@example.com']);

        $response = $this->from('/signup')->post('/signup', $this->validForm());

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'neko@example.com')->count());
    }

    /**
     * 入力が不正なパターン。どれも登録されず、その項目にエラーが付く。
     *
     * @return array<string, array{array<string, mixed>, string}>
     */
    public static function invalidForms(): array
    {
        return [
            'ユーザー名が空' => [['name' => ''], 'name'],
            'ユーザー名が256文字' => [['name' => str_repeat('あ', 256)], 'name'],
            'メールアドレスが空' => [['email' => ''], 'email'],
            'メールアドレスの形式が不正' => [['email' => 'not-an-email'], 'email'],
            'パスワードが空' => [['password' => '', 'password_confirmation' => ''], 'password'],
            'パスワードが7文字' => [['password' => '1234567', 'password_confirmation' => '1234567'], 'password'],
            'パスワード(確認)が一致しない' => [['password_confirmation' => 'different-one'], 'password'],
            'パスワード(確認)が空' => [['password_confirmation' => ''], 'password'],
        ];
    }

    #[DataProvider('invalidForms')]
    public function test_signup_is_rejected_when_the_form_is_invalid(array $overrides, string $expectedErrorField): void
    {
        $response = $this->from('/signup')->post('/signup', $this->validForm($overrides));

        $response->assertRedirect('/signup');
        $response->assertSessionHasErrors($expectedErrorField);
        $this->assertGuest();
        $this->assertSame(0, User::count());
    }

    public function test_a_password_of_exactly_eight_characters_is_accepted(): void
    {
        // 7文字は弾かれ、8文字は通る（境界値）
        $response = $this->post('/signup', $this->validForm([
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]));

        $response->assertRedirect('/');
        $this->assertSame(1, User::count());
    }

    public function test_a_name_of_exactly_255_characters_is_accepted(): void
    {
        $response = $this->post('/signup', $this->validForm([
            'name' => str_repeat('あ', 255),
        ]));

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['name' => str_repeat('あ', 255)]);
    }

    public function test_signup_is_blocked_after_five_attempts(): void
    {
        // throttle:5,1
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/signup')->post('/signup', $this->validForm(['email' => '']));
        }

        $this->from('/signup')
            ->post('/signup', $this->validForm())
            ->assertStatus(429);

        $this->assertSame(0, User::count());
    }

    public function test_logged_in_user_cannot_open_the_signup_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/signup')->assertRedirect('/');
    }
}
