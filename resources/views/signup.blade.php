@extends('common.head')

@section('content')

    <body class="home">
        @include('common.header')
        <main>
            <div class="inner-wrap">
                <div class="card auth-card">
                    <h2><i class="bi bi-box-arrow-in-right"></i> サインアップ</h2>
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
                    <form action="{{ route('signup.store') }}" method="POST">
                        @csrf
                        <div>
                            <label class="input-title" for="name">ユーザー名</label>
                            <input class="auth-input" type="text" name="name" id="name" placeholder="cat2438" required value="{{ old('name') }}">
                        </div>
                        <div>
                            <label class="input-title" for="email">メールアドレス</label>
                            <input class="auth-input" type="email" name="email" id="email" placeholder="cat2438@example.com" value="{{ old('email') }}"
                                required>
                        </div>
                        <div>
                            <label class="input-title" for="password">パスワード</label>
                            <input class="auth-input" type="password" name="password" id="password" required placeholder="8文字以上で入力してください">
                        </div>
                        <div>
                            <label class="input-title" for="password_confirmation">パスワード(確認)</label>
                            <input class="auth-input" type="password" name="password_confirmation" id="password_confirmation" required
                                autocomplete="new-password" placeholder="8文字以上で入力してください">
                        </div>

                        <button class="btn-large" type="submit">サインアップ</button>
                    </form>
                    <div class="auth-link">
                        すでにアカウントをお持ちの方は <a href="{{ route('login') }}">ログイン</a>
                    </div>
                </div>
            </div>
        </main>
    </body>
@endsection