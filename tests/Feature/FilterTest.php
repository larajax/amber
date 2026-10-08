<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_date_filter_form_posts_the_raw_date_inputs(): void
    {
        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'filter::onLoadFilterForm',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('users', [
            'scopeName' => 'created_at',
        ]);

        $resp->assertOk();

        // The date widget only applies when the raw inputs are posted
        $content = $resp->getContent();
        $this->assertStringContainsString('Filter[valueRaw]', $content);
        $this->assertStringContainsString('Filter[afterRaw]', $content);
        $this->assertStringContainsString('Filter[beforeRaw]', $content);
    }

    public function test_the_date_filter_applies_an_after_condition(): void
    {
        User::factory()->create(['email' => 'old@example.com', 'created_at' => '2020-01-01 10:00:00']);
        User::factory()->create(['email' => 'new@example.com', 'created_at' => '2025-06-01 10:00:00']);

        $resp = $this->withHeaders([
            'X-AJAX-HANDLER' => 'filter::onFilterUpdate',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post('users', [
            'scopeName' => 'created_at',
            'Filter' => [
                'condition' => 'after',
                'value' => '2024-01-01',
                'valueRaw' => '2024-01-01',
            ],
        ]);

        $resp->assertOk();
        $resp->assertSee('new@example.com');
        $resp->assertDontSee('old@example.com');
    }
}
