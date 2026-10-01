<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function showHome(Request $request) {
        // URLに ?year=2026&month=9 のようなパラメータが付いていればその年月を使う。
        // 付いていなければ「今日」の年月を使う（Carbon::now()で今日の日付が取れる）。
        $year = $request->query('year', Carbon::now()->year);
        $month = $request->query('month', Carbon::now()->month);

        // 「今週の収支」は閲覧中の年月に関係なく、常に「本当に今日を含む週」を使う
        $today = Carbon::now();

        // 表示中の年月から「前の月」「次の月」を計算する（期間移動の矢印リンク用）
        $currentMonth = Carbon::create($year, $month, 1);
        $prevMonth = $currentMonth->copy()->subMonth(); // 1ヶ月前
        $nextMonth = $currentMonth->copy()->addMonth(); // 1ヶ月後

        // 上で作った4つの関数を呼び出して、ビューに渡すデータを準備する
        // $this は現在の HomeController インスタンスを指し、その private メソッドを呼び出す。
        $monthlyTotal = $this->calculateMonthlyTotal($year, $month);
        $weeklyTotal = $this->calculateWeeklyTotal($today->year, $today->month, $today->day);
        $weeklyBreakdown = $this->calculateWeeklyBreakdownInMonth($year, $month);
        $dailyTransactions = $this->getDailyTransactions($year, $month);

        return view('home', [
            'year' => $year,
            'month' => $month,
            'prevYear' => $prevMonth->year,
            'prevMonth' => $prevMonth->month,
            'nextYear' => $nextMonth->year,
            'nextMonth' => $nextMonth->month,
            'monthlyTotal' => $monthlyTotal,
            'weeklyTotal' => $weeklyTotal,
            'weeklyBreakdown' => $weeklyBreakdown,
            'dailyTransactions' => $dailyTransactions,
        ]);
    }

    //////////////////////////////////////
    // 集計用関数
    //////////////////////////////////////

    /**
     * 月別に収支の合計を計算する関数（今月の収支）
     * @param $year
     * @param $month
     * @return array
     * $year, $month で指定した「1ヶ月分」の収入合計・支出合計・差引収支を返す。
     */
    private function calculateMonthlyTotal($year, $month)
    {
        // Carbon::create(年, 月, 日) で「その年月日」を表すCarbonインスタンス（日付オブジェクト）を作れる
        $firstDayOfMonth = Carbon::create($year, $month, 1);

        // copy() を挟まずに endOfMonth() を呼ぶと $firstDayOfMonth 自体が書き換わってしまうので、
        // 必ずcopy()してから月末日を計算する（Carbonのインスタンスは「参照渡し」に近い動きをするため注意）
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        // toDateString() で 'Y-m-d' 形式の文字列に変換して、DBの date カラムと比較できるようにする
        $startDate = $firstDayOfMonth->toDateString();
        $endDate = $lastDayOfMonth->toDateString();

        // 指定期間の「収入」だけを合計する
        $income = Transaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', '収入')
            ->sum('amount');

        // 指定期間の「支出」だけを合計する
        $expense = Transaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', '支出')
            ->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ];
    }

    /**
     * 週別に収支の合計を計算する関数（今週の収支）
     *
     * $year, $month, $day で指定した日を含む「1週間分」の収入合計・支出合計・差引収支を返す。
     */
    private function calculateWeeklyTotal($year, $month, $day)
    {
        // 基準になる日付を作る
        $baseDate = Carbon::create($year, $month, $day);

        // startOfWeek() はその週の月曜日、endOfWeek() はその週の日曜日を返してくれる便利メソッド
        $weekStart = $baseDate->copy()->startOfWeek();
        $weekEnd = $baseDate->copy()->endOfWeek();

        $startDate = $weekStart->toDateString();
        $endDate = $weekEnd->toDateString();

        $income = Transaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', '収入')
            ->sum('amount');

        $expense = Transaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', '支出')
            ->sum('amount');

        return [
            'start' => $weekStart->format('n/j'),
            'end' => $weekEnd->format('n/j'),
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
        ];
    }

    /**
     * その月の週別収支をそれぞれ計算する関数
     *
     * $year, $month で指定した月を「週ごと」に区切って、各週の収入・支出・差引収支をリストで返す。
     * 例: 9/1〜9/7, 9/8〜9/14, ... のように月をまたぐ週も含めてすべて計算する。
     */
    private function calculateWeeklyBreakdownInMonth($year, $month)
    {
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        // 1日を含む週の月曜日から数え始める
        $weekStart = $firstDayOfMonth->copy()->startOfWeek();

        // 結果を入れる空の配列を用意しておく
        $weeklyBreakdown = [];

        // 週の開始日が「その月の最終日」を超えるまで、1週間ずつずらしながら繰り返す
        while ($weekStart->lte($lastDayOfMonth)) {
            $weekEnd = $weekStart->copy()->endOfWeek();

            $startDate = $weekStart->toDateString();
            $endDate = $weekEnd->toDateString();

            $income = Transaction::whereBetween('date', [$startDate, $endDate])
                ->where('type', '収入')
                ->sum('amount');

            $expense = Transaction::whereBetween('date', [$startDate, $endDate])
                ->where('type', '支出')
                ->sum('amount');

            // この週の結果を配列に追加する
            $weeklyBreakdown[] = [
                'start' => $weekStart->format('n/j'),
                'end' => $weekEnd->format('n/j'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ];

            // addWeek() で1週間先に進める（copy()しないと$weekStart自体が変わってしまうので注意）
            $weekStart = $weekStart->copy()->addWeek();
        }

    // $weeklyBreakdownの内容
    // [
    //     [
    //         'start' => '8/31',
    //         'end' => '9/6',
    //         'income' => 300000,
    //         'expense' => 120000,
    //         'balance' => 180000,
    //     ],
    //     [
    //         'start' => '9/7',
    //         'end' => '9/13',
    //         'income' => 50000,
    //         'expense' => 30000,
    //         'balance' => 20000,
    //     ],
    // ]
    
        return $weeklyBreakdown;
    }

    /**
     * 日別の収支を取得する関数
     *
     * $year, $month で指定した月の取引を全部取得し、日付ごとにグループ分けして返す。
     * 戻り値の形: ['2026-09-29' => [取引1, 取引2, ...], '2026-09-25' => [...], ...]
     */
    private function getDailyTransactions($year, $month)
    {
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();

        $startDate = $firstDayOfMonth->toDateString();
        $endDate = $lastDayOfMonth->toDateString();

        // 新しい日付が上に来るように、日付の新しい順に並び替えて取得する
        $transactions = Transaction::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 日付ごとにグループ分けする
        $dailyTransactions = [];
        foreach ($transactions as $transaction) {
            // date カラムには casts() を付けていないので、そのまま 'Y-m-d' 形式の文字列として使える
            $dateKey = $transaction->date;

            // まだこの日付のキーが無ければ、空の配列を作っておく
            if (!isset($dailyTransactions[$dateKey])) {
                $dailyTransactions[$dateKey] = [];
            }

            // その日付の配列に、取引データを追加する
            $dailyTransactions[$dateKey][] = $transaction;
        }

        // 戻り値の内容:
        // [
        //     '2026-09-29' => [取引1, 取引2, ...],
        //     '2026-09-25' => [取引3, ...],
        // ]
        return $dailyTransactions;
    }
}
