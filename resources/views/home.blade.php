<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Reset CSS -->
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <!-- BootStrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- css -->
    <link rel="stylesheet" href="{{ asset('css/style_cat.css') }}">
    <title>MoneyLog</title>
</head>
<body class="home">
    <header>
        <i class="bi bi-wallet2 fs-1"></i>
        <h1>MoneyLog</h1>
    </header>
    <main>
        <div class="inner-wrap">
            <!-- 期間移動 -->
            <div class="switch-term">
                <div class="switch-btn">
                    <a href="{{ route('home',['year' => $prevYear, 'month' => $prevMonth]) }}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </div>
                <div class="term">
                    {{ $year }}年{{ $month }}月
                </div>
                <div class="switch-btn">
                    <a href="{{ route('home',['year' => $nextYear, 'month' => $nextMonth]) }}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- 今月の収支カード -->
            <div class="card">
                <!-- 今月の収支 -->
                <div class="balance">
                    <p>今月の収支</p>
                        @if ($monthlyTotal['balance'] >= 0)
                        <p class="balance-plus">+¥{{ number_format(abs($monthlyTotal['balance'])) }}</p>
                        @else
                        <p class="balance-minus">-¥{{ number_format(abs($monthlyTotal['balance'])) }}</p>
                        @endif
                    </p>
                </div>
                <!-- 収支/収支グループ -->
                <div class="income-expense-area">
                    <div class="income-expense">
                        <p>収入</p>
                        <p class="income-text">+¥{{ number_format($monthlyTotal['income']) }}</p>
                    </div>
                    <div class="border"></div>
                    <div class="income-expense">
                        <p>支出</p>
                        <p class="expense-text">-¥{{ number_format($monthlyTotal['expense']) }}</p>
                    </div>
                </div>
            </div>
            <!-- 週別の収支カード -->
            <div class="card">
                <p class="card-title">週別収支</p>
                <ul class="weekly-list">
                    @foreach ($weeklyBreakdown as $week)
                    <li>
                        <span>{{ $week['start'] }} ~ {{ $week['end'] }}</span>
                        @if ($week['balance'] >= 0)
                        <span class="income-text">+¥{{ number_format($week['balance']) }}</span>
                        @else
                        <span class="expense-text">-¥{{ number_format(abs($week['balance'])) }}</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            <!-- 日別収支リスト -->
            @php
                // カテゴリ名 => アイコンクラス名 の対応表。
                $categoryIcons = [
                    '食費' => 'bi-cup-hot',
                    '日用品' => 'bi-basket2',
                    '交通費' => 'bi-train-front',
                    '趣味' => 'bi-controller',
                    '光熱費' => 'bi-lightning-charge',
                    'その他' => 'bi-three-dots',
                ];
            @endphp
            <ul class="daily-list">
                @foreach ($dailyTransactions as $date => $transactions)
                    <!-- 日別収支カード -->
                    <li class="day-group">
                        <div class="day-header">
                            <span class="day-date">{{ date('n/j', strtotime($date)) }}</span>
                        </div>
                        <ul class="transaction-list">
                            @foreach ($transactions as $transaction)
                                <!-- 収支詳細 -->
                                <li class="transaction-item">
                                    <a href="{{ url('/edit_form/' . $transaction->id) }}">
                                        <div class="transaction-icon transaction-icon-{{ $transaction->type === '支出' ? 'expense' : 'income' }}">
                                            {{-- $categoryIcons[カテゴリ名] で対応表からアイコンを探す。
                                                見つからない場合（カテゴリが未設定など）は ?? の右側 'bi-wallet2' を使う --}}
                                            <i class="bi {{ $categoryIcons[$transaction->category] ?? 'bi-wallet2' }}"></i>
                                        </div>
                                        <div class="transaction-info">
                                            <p class="transaction-category">{{ $transaction->category }}</p>
                                            <p class="transaction-memo">{{ $transaction->memo }}</p>
                                        </div>
                                        <div class="transaction-amount {{ $transaction->type === '支出' ? 'expense-text' : 'income-text' }}">
                                            {{ $transaction->type === '支出' ? '-' : '+' }}¥{{ number_format($transaction->amount) }}
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
            <!-- 収支新規登録ボタン -->
            <a href="{{ url('/register_form') }}" class="add-btn">
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>
    </main>
</body>
</html>