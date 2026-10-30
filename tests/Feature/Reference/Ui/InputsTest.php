<?php

namespace Tests\Feature\Reference\Ui;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class InputsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts an AJAX handler request to the Inputs page.
     *
     * @param  array<string, mixed>  $data
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.ui.inputs'), $data);
    }

    public function test_the_page_renders_every_example_beside_its_source(): void
    {
        $html = $this->get(route('reference.ui.inputs'))->assertOk()->getContent();

        foreach (['custom-select', 'custom-select-ajax', 'checkbox-tristate', 'checkbox-range', 'input-hotkey'] as $example) {
            $source = e(trim(file_get_contents(resource_path("views/reference/ui/examples/{$example}.blade.php"))));
            $this->assertStringContainsString('<pre><code>'.$source.'</code></pre>', $html, "{$example} is not shown.");
        }

        foreach (['custom-select', 'custom-select-ajax', 'checkbox', 'checkbox-range', 'input-hotkey'] as $section) {
            $this->assertStringContainsString('id="'.$section.'"', $html);
        }
    }

    public function test_the_page_renders_each_input_control(): void
    {
        $html = $this->get(route('reference.ui.inputs'))->assertOk()->getContent();

        $this->assertStringContainsString('class="form-control custom-select select-no-search"', $html);
        $this->assertStringContainsString('<optgroup label="Business">', $html);
        $this->assertStringContainsString('data-handler="onGetCountryOptions"', $html);
        $this->assertStringContainsString('data-minimum-input-length="1"', $html);
        $this->assertStringContainsString('<div class="form-field is-indeterminate">', $html);
        $this->assertStringContainsString('data-checked="1"', $html);
        $this->assertStringContainsString("jax.checkboxRangeRegisterClick(ev, 'li', 'input[type=checkbox]')", $html);
        $this->assertStringContainsString('data-hotkey="alt+shift+k"', $html);
        $this->assertStringContainsString('href="'.route('reference.ui.buttons').'" class="btn-link" data-hotkey="alt+shift+b"', $html);
    }

    public function test_on_get_country_options_filters_by_the_term(): void
    {
        $this->ajax('onGetCountryOptions', ['term' => 'ger'])
            ->assertOk()
            ->assertJsonPath('results', [['id' => 'de', 'text' => 'Germany']]);
    }

    public function test_on_get_country_options_lists_every_country_without_a_term(): void
    {
        $results = $this->ajax('onGetCountryOptions')->assertOk()->json('results');

        $this->assertCount(16, $results);
        $this->assertSame(['id' => 'ar', 'text' => 'Argentina'], $results[0]);
    }

    public function test_the_hotkey_button_handler_flashes_a_message(): void
    {
        $response = $this->ajax('onRefreshData')->assertOk();

        $this->assertSame('The data was refreshed.', collect($response->json('__ajax.ops'))->firstWhere('op', 'flash')['text']);
    }
}
