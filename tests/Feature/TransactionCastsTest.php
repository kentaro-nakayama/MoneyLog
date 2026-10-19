<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Transaction モデルの casts() が効いているかを確かめるテスト。
 *
 * date は Carbon、amount は整数として扱えること、
 * またそれによって画面の表示が壊れていないことを確認する。
 */
class TransactionCastsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * フォームから送られてくる値はすべて文字列なので、
     * 「フォーム経由で保存した直後」の状態を再現するための共通処理。
     */
    private function createFromFormValues(array $overrides = [])
    {
        return $this->user->transactions()->create(array_merge([
            'type' => '支出',
            'amount' => '1500',
            'category' => '食費',
            'date' => '2026-10-18',
            'memo' => 'スーパーで食材',
        ], $overrides));
    }

    public function test_date_is_a_carbon_instance_so_date_calculations_can_be_used(): void
    {
        $transaction = $this->createFromFormValues();

        $this->assertInstanceOf(CarbonInterface::class, $transaction->date);
        $this->assertSame('2026-10-18', $transaction->date->toDateString());

        // Carbon なので、そのまま日付の計算や書式変換ができる
        $this->assertSame('10/19', $transaction->date->copy()->addDay()->format('n/j'));
        $this->assertSame('2026-10-12', $transaction->date->copy()->startOfWeek()->toDateString());
    }

    public function test_date_is_still_a_carbon_instance_after_reading_from_the_database(): void
    {
        $transaction = $this->createFromFormValues()->fresh();

        $this->assertInstanceOf(CarbonInterface::class, $transaction->date);
        $this->assertSame('2026-10-18', $transaction->date->toDateString());
    }

    public function test_date_has_no_time_part(): void
    {
        $transaction = $this->createFromFormValues();

        // date 型なので、時刻は 00:00:00 に切り落とされる
        $this->assertSame('2026-10-18 00:00:00', $transaction->date->format('Y-m-d H:i:s'));
    }

    public function test_amount_is_an_integer_even_when_the_form_sends_a_string(): void
    {
        $transaction = $this->createFromFormValues(['amount' => '1500']);

        $this->assertSame(1500, $transaction->amount);
        $this->assertSame(1500, $transaction->fresh()->amount);
    }

    public function test_edit_form_prefills_the_date_input_in_the_format_the_input_expects(): void
    {
        $transaction = $this->createFromFormValues();

        $response = $this->actingAs($this->user)->get("/edit_form/{$transaction->id}");

        $response->assertOk();
        // <input type="date"> は 'Y-m-d' でないと初期値として読み込まれない
        $response->assertSee('value="2026-10-18"', false);
        $response->assertDontSee('2026-10-18 00:00:00');
    }

    public function test_home_page_still_groups_transactions_by_date_string(): void
    {
        $this->createFromFormValues(['date' => '2026-10-18']);
        $this->createFromFormValues(['date' => '2026-10-02', 'memo' => 'カフェ']);

        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertOk();
        $response->assertViewHas('dailyTransactions', function (array $days) {
            return array_keys($days) === ['2026-10-18', '2026-10-02'];
        });
        $response->assertSee('スーパーで食材');
        $response->assertSee('カフェ');
    }
}
