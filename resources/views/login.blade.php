@extends('common.head')

@section('content')

    <body class="home">
        @include('common.header')
        <main>
            <div class="inner-wrap">
                <div class="card">
                    <h2><i class="bi bi-box-arrow-in-right"></i> ログイン</h2>
                    <hr>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('login.store') }}" method="POST">
                        @csrf
                        <div>
                            <label class="input-title" for="email">メールアドレス</label>
                            <input class="auth-input" type="email" name="email" id="email" placeholder="メールアドレスを入力" required
                                value="{{ old('email') }}" autocomplete="email">
                        </div>
                        <div>
                            <label class="input-title" for="password">パスワード</label>
                            <input class="auth-input" type="password" name="password" id="password" placeholder="パスワードを入力"
                                required autocomplete="current-password">
                        </div>
                        <div class="remember-row">
                            <input type="checkbox" name="remember" id="remember" value="1">
                            <label class="input-title" for="remember">ログイン状態を保持</label>
                        </div>
                        <button class="btn-large" type="submit">ログイン</button>
                    </form>
                    <div class="auth-link">
                        アカウントをお持ちでない方は <a href="{{ route('signup') }}">新規登録</a>
                    </div>
                </div>
            </div>
        </main>
    </body>
@endsection