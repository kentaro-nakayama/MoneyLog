<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ホーム画面の集計(月の収支・週ごとの内訳)が正しいかを確かめるテスト。
 *
 * 2026年10月のカレンダー(週は月曜始まり):
 *   9/28〜10/4, 10/5〜10/11, 10/12〜10/18, 10/19〜10/25, 10/26〜11/1 の5週
 */
class HomeTotalsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // 10月の取引
        $this->make('収入', 200000, '2026-10-01');
        $this->make('支出', 1200, '2026-10-02');
        $this->make('支出', 3000, '2026-10-20');

        // 10月の外だが、10月を含む週に入る取引
        $this->make('支出', 500, '2026-09-30');
        $this->make('収入', 1000, '2026-11-01');
    }

    private function make(string $type, int $amount, string $date, ?User $user = null): Transaction
    {
        return Transaction::factory()->for($user ?? $this->user)->create([
            'type' => $type,
            'amount' => $amount,
            'date' => $date,
        ]);
    }

    public function test_monthly_total_counts_only_transactions_in_that_month(): void
    {
        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertOk();
        $response->assertViewHas('monthlyTotal', function (array $total) {
            return (int) $total['income'] === 200000
                && (int) $total['expense'] === 4200
                && (int) $total['balance'] === 195800;
        });
    }

    public function test_monthly_total_ignores_other_users_transactions(): void
    {
        $other = User::factory()->create();
        $this->make('支出', 99999, '2026-10-15', $other);

        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertViewHas('monthlyTotal', fn (array $total) => (int) $total['expense'] === 4200);
    }

    public function test_weekly_breakdown_splits_the_month_into_monday_start_weeks(): void
    {
        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertViewHas('weeklyBreakdown', function (array $weeks) {
            $labels = array_map(fn ($w) => $w['start'] . '〜' . $w['end'], $weeks);

            return $labels === ['9/28〜10/4', '10/5〜10/11', '10/12〜10/18', '10/19〜10/25', '10/26〜11/1'];
        });
    }

    public function test_weekly_breakdown_includes_days_outside_the_month_in_boundary_weeks(): void
    {
        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertViewHas('weeklyBreakdown', function (array $weeks) {
            $first = $weeks[0];   // 9/28〜10/4
            $fourth = $weeks[3];  // 10/19〜10/25
            $last = $weeks[4];    // 10/26〜11/1

            return (int) $first['income'] === 200000
                && (int) $first['expense'] === 1700   // 9/30 の 500 + 10/2 の 1200
                && (int) $fourth['expense'] === 3000
                && (int) $last['income'] === 1000      // 11/1 の取引
                && (int) $last['balance'] === 1000;
        });
    }

    public function test_daily_transactions_are_grouped_by_date_newest_first(): void
    {
        $response = $this->actingAs($this->user)->get('/?year=2026&month=10');

        $response->assertViewHas('dailyTransactions', function (array $days) {
            return array_keys($days) === ['2026-10-20', '2026-10-02', '2026-10-01'];
        });
    }

    public function test_month_navigation_crosses_the_year_boundary(): void
    {
        $response = $this->actingAs($this->user)->get('/?year=2026&month=12');

        $response->assertViewHas('nextYear', 2027);
        $response->assertViewHas('nextMonth', 1);
        $response->assertViewHas('prevYear', 2026);
        $response->assertViewHas('prevMonth', 11);
    }
}
