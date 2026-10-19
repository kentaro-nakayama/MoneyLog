<header>
    {{-- ログイン中はタイトルをホームへのリンクにする。
         未ログインのときはホームに入れない（ログイン画面に戻されるだけ）ので、リンクにしない --}}
    @auth
        <a href="{{ route('home') }}" class="home-link">
            <i class="bi bi-wallet2 fs-1"></i>
            <h1>MoneyLog</h1>
        </a>
    @else
        <i class="bi bi-wallet2 fs-1"></i>
        <h1>MoneyLog</h1>
    @endauth
    {{-- ログイン中だけ、誰で入っているかとログアウトを出す --}}
    @auth
        <div class="header-user">
            <span class="user-name">
                <i class="bi bi-person-circle"></i><span class="user-name-text">{{ Auth::user()->name }}</span>
            </span>
            <form class="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>ログアウト
                </button>
            </form>
        </div>
    @endauth
</header>
