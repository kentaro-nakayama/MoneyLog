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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>MoneyLog</title>
</head>
<body class="home">
    <header>
        <i class="bi bi-wallet2 fs-1"></i>
        <h1>MoneyLog</h1>
    </header>
    <main>
        <div class="inner-wrap">
            <!-- 週別・月別切り替えボタン -->
            <div class="switch-btn-area">
                <ul class="switch-btn-list">
                    <li>
                        <a href="" class="switch-btn btn-selected">週別</a>
                    </li>
                    <li>
                        <a href="" class="switch-btn">月別</a>
                    </li>
                </ul>
            </div>

            <!-- 期間移動 -->
            <div class="switch-term">
                <div class="switch-btn">
                    <a href="">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </div>
                <div class="term">
                    2026年9月
                </div>
                <div class="switch-btn">
                    <a href="">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- 今月の収支カード -->
            <div class="card">
                <!-- 今月の収支 -->
                <div class="balance">
                    <p>今月の収支</p>
                    <p class="balance-text">+¥142000</p>
                </div>
                <!-- 収支/収支グループ -->
                <div class="income-expense-area">
                    <div class="income-expense">
                        <p>収入</p>
                        <p class="income-text">¥250000</p>
                    </div>
                    <div class="border"></div>
                    <div class="income-expense">
                        <p>支出</p>
                        <p class="expense-text">¥150000</p>
                    </div>
                </div>
            </div>
            <!-- 週別の収支カード -->
            <div class="card">
                <p class="card-title">週別収支</p>
                <ul class="weekly-list">
                    <li>
                        <span>9/1 ~ 9/7</span>
                        <span class="income-text">+¥3000</span>
                    </li>
                    <li>
                        <span>9/8 ~ 9/15</span>
                        <span class="expense-text">-¥1000</span>
                    </li>
                    <li>
                        <span>9/16 ~ 9/23</span>
                        <span class="income-text">+¥10000</span>
                    </li>
                    <li>
                        <span>9/24 ~ 10/1</span>
                        <span class="expense-text">-¥5000</span>
                    </li>
                </ul>
            </div>
            <!-- 日別収支リスト -->
            <ul class="daily-list">
                <!-- 日別収支カード -->
                <li class="day-group">
                    <div class="day-header">
                        <span class="day-date">9/1</span>
                    </div>
                    <ul class="transaction-list">
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-cup-hot"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">食費</p>
                                    <p class="transaction-memo">コンビニで買い物</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-train-front"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">交通費</p>
                                    <p class="transaction-memo">友達とおでかけ</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- 日別収支カード -->
                <li class="day-group">
                    <div class="day-header">
                        <span class="day-date">9/5</span>
                    </div>
                    <ul class="transaction-list">
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-cup-hot"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">食費</p>
                                    <p class="transaction-memo">コンビニで買い物</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-train-front"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">交通費</p>
                                    <p class="transaction-memo">友達とおでかけ</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- 日別収支カード -->
                <li class="day-group">
                    <div class="day-header">
                        <span class="day-date">9/10</span>
                    </div>
                    <ul class="transaction-list">
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-cup-hot"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">食費</p>
                                    <p class="transaction-memo">コンビニで買い物</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                        <!-- 収支詳細 -->
                        <li class="transaction-item">
                            <a href="">
                                <div class="transaction-icon transaction-icon-expense">
                                    <i class="bi bi-train-front"></i>
                                </div>
                                <div class="transaction-info">
                                    <p class="transaction-category">交通費</p>
                                    <p class="transaction-memo">友達とおでかけ</p>
                                </div>
                                <div class="transaction-amount expense-text">
                                    -¥1200
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- 収支新規登録ボタン -->
            <a href="" class="add-btn">
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>
    </main>
</body>
</html>