<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Sample;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class TagListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts one AJAX handler request to the tag list page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.tag-list'), $data);
    }

    /**
     * section returns the page markup of one example section.
     */
    protected function section(string $html, string $id): string
    {
        preg_match('/<section class="ui-section" id="'.preg_quote($id, '/').'">(.*?)<\/section>/s', $html, $match);

        return $match[1] ?? '';
    }

    /**
     * makeTagForm builds a form holding one tag list on the keywords string column.
     */
    protected function makeTagForm(Sample $sample, array $config = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => $sample,
            'fields' => ['keywords' => $config + ['label' => 'Keywords', 'type' => 'taglist']],
        ]);

        $form->bindToController();

        return $form;
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.tag-list'))->assertOk()->getContent();

        foreach (['array', 'string', 'options', 'use-key', 'relation', 'relation-custom', 'read-only', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/tag-list/{$example}.yaml", $html);
        }

        $this->assertSame(6, substr_count($html, 'select-hide-selected'));
        $this->assertSame(2, substr_count($html, 'taglist--preview'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_array_mode_renders_custom_tags(): void
    {
        Sample::factory()->create(['tags' => ['lighting', 'bestseller']]);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'array');

        $this->assertStringContainsString('data-token-separators=","', $html);
        $this->assertStringContainsString('data-placeholder="Type a tag and press Enter"', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="lighting"\s*>lighting<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="bestseller"\s*>bestseller<\/option>/', $html);
    }

    public function test_string_mode_splits_the_value_on_the_separator(): void
    {
        Sample::factory()->create(['keywords' => 'lamp desk']);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'string');

        $this->assertStringContainsString('data-token-separators=" "', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="lamp"\s*>lamp<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="desk"\s*>desk<\/option>/', $html);
    }

    public function test_string_mode_joins_with_a_comma_by_default(): void
    {
        $form = $this->makeTagForm(Sample::factory()->create(['keywords' => 'red,green']), ['customTags' => true]);

        $html = (string) $form->render();
        $widget = $form->getFormWidget('keywords');

        $this->assertSame('string', $widget->mode);
        $this->assertStringContainsString('data-token-separators=","', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="red"\s*>red<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="green"\s*>green<\/option>/', $html);
        $this->assertSame('red,blue', $widget->getSaveValue(['red', 'blue']));
    }

    public function test_the_mode_follows_the_attribute(): void
    {
        $castForm = Form::createIn(new FormWidgetsController, [
            'model' => User::factory()->create(['links' => ['alpha']]),
            'fields' => ['links' => ['type' => 'taglist', 'options' => ['alpha' => 'Alpha', 'gamma' => 'Gamma']]],
        ]);
        $castForm->bindToController();

        $this->assertSame('array', $castForm->getFormWidget('links')->mode);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="alpha"\s*>Alpha<\/option>/', (string) $castForm->render());
        $this->assertSame(['gamma'], $castForm->getFormWidget('links')->getSaveValue(['gamma']));

        $form = Form::createIn(new FormWidgetsController, [
            'model' => Sample::factory()->create(),
            'fields' => [
                'topics' => ['type' => 'taglist'],
                'groups' => ['type' => 'taglist'],
                'keywords' => ['type' => 'taglist'],
            ],
        ]);
        $form->bindToController();

        $this->assertSame('array', $form->getFormWidget('topics')->mode);
        $this->assertSame('relation', $form->getFormWidget('groups')->mode);
        $this->assertSame('string', $form->getFormWidget('keywords')->mode);
    }

    public function test_fixed_options_render_their_keys_and_the_limit(): void
    {
        Sample::factory()->create(['topics' => ['design']]);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'options');

        $this->assertStringContainsString('data-maximum-selection-length="3"', $html);
        $this->assertStringNotContainsString('data-token-separators', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="design"\s*>Design<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="research"\s*>Research<\/option>/', $html);
    }

    public function test_use_key_false_renders_the_labels_as_values(): void
    {
        Sample::factory()->create(['languages' => ['Japanese']]);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'use-key');

        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="Japanese"\s*>Japanese<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="English"\s*>English<\/option>/', $html);
    }

    public function test_a_relation_renders_its_records_as_options(): void
    {
        $editors = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        $support = UserGroup::factory()->create(['name' => 'Support', 'code' => 'support']);
        Sample::factory()->create()->groups()->attach($editors);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'relation');

        $this->assertMatchesRegularExpression('/<select[^>]*name="groups\[\]"[^>]*class="form-control custom-select\s+select-hide-selected"[^>]*multiple/', $html);
        $this->assertStringContainsString('data-placeholder="Add groups"', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="'.$editors->id.'"\s*>Editors<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="'.$support->id.'"\s*>Support<\/option>/', $html);
    }

    public function test_relation_mode_with_custom_tags_uses_the_names_as_values(): void
    {
        $lighting = Tag::factory()->create(['name' => 'lighting']);
        Tag::factory()->create(['name' => 'furniture']);
        Sample::factory()->create()->labels()->attach($lighting);

        $html = $this->section($this->get(route('reference.form-widgets.tag-list'))->getContent(), 'relation-custom');

        $this->assertStringContainsString('data-token-separators=","', $html);
        $this->assertMatchesRegularExpression('/<option\s+selected="selected"\s+value="lighting"\s*>lighting<\/option>/', $html);
        $this->assertMatchesRegularExpression('/<option\s+value="furniture"\s*>furniture<\/option>/', $html);
    }

    public function test_locked_fields_list_the_selected_labels(): void
    {
        Sample::factory()->create(['tags' => ['lighting', 'bestseller'], 'topics' => ['design']]);

        $html = $this->get(route('reference.form-widgets.tag-list'))->getContent();

        $readOnly = $this->section($html, 'read-only');
        $this->assertStringContainsString('<ul class="form-control taglist--preview" readonly="readonly">', $readOnly);
        $this->assertStringContainsString('<li class="taglist__item">lighting</li>', $readOnly);
        $this->assertMatchesRegularExpression('/name="_tags_readonly\[\]"\s+value="bestseller"/', $readOnly);

        $preview = $this->section($html, 'preview');
        $this->assertStringContainsString('<ul class="form-control taglist--preview" disabled="disabled">', $preview);
        $this->assertStringContainsString('<li class="taglist__item">Design</li>', $preview);
        $this->assertStringNotContainsString('<li class="taglist__item">Research</li>', $preview);
        $this->assertStringNotContainsString('<select', $preview);
    }

    public function test_saving_the_page_stores_every_mode(): void
    {
        $editors = UserGroup::factory()->create(['code' => 'editors']);
        $lighting = Tag::factory()->create(['name' => 'lighting']);
        $sample = Sample::factory()->create();

        $this->ajax('onSaveExamples', [
            'tags' => ['clock', 'wall'],
            'keywords' => ['clock', 'brass'],
            'topics' => ['design', 'research'],
            'languages' => ['French'],
            'groups' => [(string) $editors->id],
            'labels' => ['lighting', 'brass'],
            '_tags_readonly' => ['dropped'],
            'topics_preview' => ['support'],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame(['clock', 'wall'], $sample->tags);
        $this->assertSame('clock brass', $sample->keywords);
        $this->assertSame(['design', 'research'], $sample->topics);
        $this->assertSame(['French'], $sample->languages);
        $this->assertSame([$editors->id], $sample->groups()->pluck('user_groups.id')->all());

        $brass = Tag::where('name', 'brass')->first();
        $this->assertNotNull($brass);
        $this->assertEqualsCanonicalizing([$lighting->id, $brass->id], $sample->labels()->pluck('tags.id')->all());
    }

    public function test_saving_empty_values_clears_the_tags_and_the_relation(): void
    {
        $sample = Sample::factory()->create(['tags' => ['old'], 'keywords' => 'old']);
        $sample->groups()->attach(UserGroup::factory()->create());

        $this->ajax('onSaveExamples', ['tags' => '', 'keywords' => '', 'groups' => ''])->assertOk();

        $sample->refresh();
        $this->assertEmpty($sample->tags);
        $this->assertEmpty($sample->keywords);
        $this->assertSame(0, $sample->groups()->count());
    }
}
