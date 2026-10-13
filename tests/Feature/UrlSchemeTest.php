<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UrlSchemeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // asset() と同じ仕組みで作られるURLを、そのまま返すだけのテスト用ルート
        Route::get('/_test/generated-url', fn () => asset('css/reset.css'));
    }

    public function test_generates_http_url_when_accessed_over_plain_http_without_proxy(): void
    {
        $response = $this->get('http://192.168.64.2/_test/generated-url');

        $response->assertOk();
        $this->assertSame('http://192.168.64.2/css/reset.css', $response->getContent());
    }

    public function test_generates_https_url_when_proxy_reports_https(): void
    {
        $response = $this->withHeaders(['X-Forwarded-Proto' => 'https'])
            ->get('http://moneylog.example/_test/generated-url');

        $response->assertOk();
        $this->assertSame('https://moneylog.example/css/reset.css', $response->getContent());
    }

    public function test_generates_http_url_when_proxy_reports_http(): void
    {
        $response = $this->withHeaders(['X-Forwarded-Proto' => 'http'])
            ->get('http://moneylog.example/_test/generated-url');

        $response->assertOk();
        $this->assertSame('http://moneylog.example/css/reset.css', $response->getContent());
    }
}
