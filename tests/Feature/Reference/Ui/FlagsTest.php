<?php

namespace Tests\Feature\Reference\Ui;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.flags'))->assertOk()->getContent();

        foreach (['flags-countries', 'flags-sizing', 'flags-special'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
            $this->assertStringContainsString('id="'.$example.'"', $html);
        }

        $this->assertStringContainsString('<span class="flag-au"></span>', $html);
        $this->assertStringContainsString('<span class="flag-gb-sct"></span>', $html);
    }

    public function test_every_flag_class_points_to_a_flag_image(): void
    {
        $folder = base_path('vendor/larajax/ui/resources/assets/elements/flag');
        preg_match_all("/\.flag-([a-z-]+) \{ background-image: url\('\.\/(flag-icon\/flags\/4x3\/[a-z-]+\.svg)'\); \}/", file_get_contents("{$folder}/flag.css"), $matches, PREG_SET_ORDER);

        $this->assertGreaterThan(250, count($matches));

        foreach ($matches as [, $code, $path]) {
            $this->assertFileExists("{$folder}/{$path}", "flag-{$code} has no image.");
        }
    }
}
