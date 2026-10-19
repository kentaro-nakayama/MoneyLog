<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * 収支の登録で、おかしな値が保存されないことを確かめるテスト。
 */
class TransactionValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validInput(array $overrides = []): array
    {
        return array_merge([
            'type' => '支出',
            'amount' => 1500,
            'category' => '食費',
            'date' => '2026-10-10',
            'memo' => '夕食',
        ], $overrides);
    }

    public function test_valid_input_is_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/register', $this->validInput())->assertRedirect('/');

        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_category_and_memo_can_be_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/register', $this->validInput(['category' => null, 'memo' => null]))
            ->assertRedirect('/');

        $this->assertDatabaseCount('transactions', 1);
    }

    // invalidInputProvider() の各行を1つずつ渡して、同じテストを何度も実行する
    #[DataProvider('invalidInputProvider')]
    public function test_invalid_input_is_rejected(string $field, mixed $value): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/register_form')
            ->post('/register', $this->validInput([$field => $value]))
            ->assertRedirect('/register_form')
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('transactions', 0);
    }

    public static function invalidInputProvider(): array
    {
        return [
            '種別が収入・支出以外' => ['type', '貯金'],
            '種別が空' => ['type', ''],
            '金額が0' => ['amount', 0],
            '金額がマイナス' => ['amount', -100],
            '金額が小数' => ['amount', 10.5],
            '金額が文字' => ['amount', 'abc'],
            'カテゴリが選択肢にない' => ['category', '旅行'],
            '日付が不正' => ['date', '2026-13-40'],
            '日付が空' => ['date', ''],
        ];
    }
}
