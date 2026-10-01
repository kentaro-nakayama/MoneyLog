@extends('common.head')

@section('content')
<body class="home">
    <header>
        <i class="bi bi-wallet2 fs-1"></i>
        <h1>MoneyLog</h1>
    </header>
    <main>
        <div class="inner-wrap">
            <div class="back-to-home">
                <a href="{{ url('/') }}"><i class="bi bi-chevron-left"></i><span>ホーム</span></a>
            </div>
            <div class="card">
                <h2>
                    <i class="bi bi-plus-circle"></i>
                    新規登録
                </h2>
                <hr>
                <form action="{{ url('/register') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- 収入/支出選択ボタン -->
                    <div class="income-expense-btn-area">
                        <input type="radio" id="type-income" name="type" value="収入" class="type-radio">
                        <label for="type-income" class="type-btn type-income-btn">収入</label>
                        <input type="radio" id="type-expense" name="type" value="支出" class="type-radio" checked>
                        <label for="type-expense" class="type-btn type-expense-btn">支出</label>
                    </div>
                    <!-- 金額入力欄 -->
                    <div>
                        <p class="input-title">金額(必須)</p>
                        <div class="amount-input-area">
                            <span class="currency-symbol">¥</span>
                            <input type="number" id="amount" name="amount" class="amount-input" placeholder="0" required>
                        </div>
                    </div>
                    <!-- カテゴリー選択 -->
                    <div class="category-btn-area">
                        <p class="input-title">カテゴリー(任意)</p>
                        <div>
                            <input type="radio" id="category-food" name="category" value="食費" class="category-radio">
                            <label for="category-food" class="category-btn">食費</label>
                            <input type="radio" id="category-daily" name="category" value="日用品" class="category-radio">
                            <label for="category-daily" class="category-btn">日用品</label>
                            <input type="radio" id="category-transport" name="category" value="交通費" class="category-radio">
                            <label for="category-transport" class="category-btn">交通費</label>
                            <input type="radio" id="category-hobby" name="category" value="趣味" class="category-radio">
                            <label for="category-hobby" class="category-btn">趣味</label>
                            <input type="radio" id="category-utility" name="category" value="光熱費" class="category-radio">
                            <label for="category-utility" class="category-btn">光熱費</label>
                            <input type="radio" id="category-other" name="category" value="その他" class="category-radio">
                            <label for="category-other" class="category-btn">その他</label>
                        </div>
                    </div>
                    <!-- 日付 -->
                    <div>
                        <p class="input-title">日付(必須)</p>
                        <input type="date" name="date" class="date-input" required>
                    </div>
                    <!-- メモ -->
                    <div>
                        <p class="input-title">メモ(任意)</p>
                        <textarea name="memo" class="memo-input"></textarea>
                    </div>
                    <button type="submit" class="btn-large">登録する</button>
                </form>
            </div>
        </div>
    </main>
</body>
@endsection
