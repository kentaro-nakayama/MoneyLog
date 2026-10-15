<?php

namespace Tests\Feature;

use Tests\TestCase;

class CssCacheBustingTest extends TestCase
{
    public function test_css_urls_include_file_modified_time_as_version(): void
    {
        $view = $this->view('common.head');

        foreach (['css/reset.css', 'css/style_cat.css'] as $path) {
            $version = filemtime(public_path($path));

            $view->assertSee(asset($path).'?v='.$version, false);
        }
    }

    public function test_css_url_changes_when_css_file_is_updated(): void
    {
        $file = public_path('css/style_cat.css');
        $originalMtime = filemtime($file);

        try {
            $before = (string) $this->view('common.head');

            touch($file, $originalMtime + 60);
            clearstatcache(true, $file);

            $after = (string) $this->view('common.head');

            $this->assertStringContainsString('style_cat.css?v='.$originalMtime, $before);
            $this->assertStringContainsString('style_cat.css?v='.($originalMtime + 60), $after);
        } finally {
            touch($file, $originalMtime);
            clearstatcache(true, $file);
        }
    }
}
