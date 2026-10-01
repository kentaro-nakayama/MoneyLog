<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Heroku等のプロキシ配下では、Laravelに届くリクエストが実際にはHTTPとして見えてしまい、
        // asset()やurl()が生成するリンクが http:// になってしまう。
        // ローカル開発(http)はそのままにしつつ、本番では強制的にHTTPSのURLを生成させる。
        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
