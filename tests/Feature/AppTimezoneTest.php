<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppTimezoneTest extends TestCase
{
    private ?string $originalTimezone = null;

    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml で決めた値（UTC）を覚えておき、テストのあとに必ず戻す。
        // 消してしまうと、次に .env が読まれたときに開発用の値で上書きされるため。
        $this->originalTimezone = $_ENV['APP_TIMEZONE'] ?? null;
    }

    protected function tearDown(): void
    {
        $this->setAppTimezoneEnv($this->originalTimezone);

        parent::tearDown();
    }

    public function test_uses_timezone_from_app_timezone_env(): void
    {
        $this->setAppTimezoneEnv('Asia/Tokyo');
        $this->refreshApplication();

        $this->assertSame('Asia/Tokyo', config('app.timezone'));
        $this->assertSame('Asia/Tokyo', date_default_timezone_get());
        $this->assertSame('+09:00', now()->format('P'));
    }

    public function test_can_switch_timezone_per_environment(): void
    {
        $this->setAppTimezoneEnv('UTC');
        $this->refreshApplication();

        $this->assertSame('UTC', config('app.timezone'));
        $this->assertSame('+00:00', now()->format('P'));
    }

    private function setAppTimezoneEnv(?string $timezone): void
    {
        if ($timezone === null) {
            putenv('APP_TIMEZONE');
            unset($_ENV['APP_TIMEZONE'], $_SERVER['APP_TIMEZONE']);

            return;
        }

        putenv("APP_TIMEZONE={$timezone}");
        $_ENV['APP_TIMEZONE'] = $timezone;
        $_SERVER['APP_TIMEZONE'] = $timezone;
    }
}
