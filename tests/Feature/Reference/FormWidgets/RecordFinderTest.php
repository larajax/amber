<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Category;
use App\Models\Sample;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class RecordFinderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts one AJAX handler request to the record finder page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.record-finder'), $data);
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
     * opsHtml joins the markup of every operation in an AJAX response.
     */
    protected function opsHtml(TestResponse $response): string
    {
        return collect($response->json('__ajax.ops'))->pluck('html')->implode('');
    }

    /**
     * makeFinderForm builds a form holding one record finder on the reviewer relation.
     */
    protected function makeFinderForm(Sample $sample, array $config = [], array $formConfig = []): Form
    {
        $form = Form::createIn(new FormWidgetsController, $formConfig + [
            'model' => $sample,
            'fields' => [
                'reviewer' => $config + [
                    'label' => 'Reviewer',
                    'type' => 'recordfinder',
                    'list' => ['columns' => ['name' => ['label' => 'Name']]],
                    'nameFrom' => 'name',
                    'descriptionFrom' => 'email',
                ],
            ],
        ]);

        $form->bindToController();

        return $form;
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.record-finder'))->assertOk()->getContent();

        foreach (['default', 'scopes', 'popup', 'tree', 'without-relation', 'read-only', 'preview'] as $example) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/record-finder/{$example}.yaml", $html);
        }

        $this->assertSame(6, substr_count($html, 'data-control="recordfinder"'));
        $this->assertStringContainsString('There is no record selected.', $html);
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_a_selected_record_renders_its_name_and_description(): void
    {
        $reviewer = User::factory()->create(['name' => 'Vera Reviewer', 'email' => 'vera@example.com']);
        Sample::factory()->create(['reviewer_id' => $reviewer->id]);

        $html = $this->section($this->get(route('reference.form-widgets.record-finder'))->getContent(), 'default');

        $this->assertStringContainsString('class="field-recordfinder is-single', $html);
        $this->assertStringContainsString('<span class="recordname">Vera Reviewer</span>', $html);
        $this->assertStringContainsString('<span class="description"> - vera@example.com</span>', $html);
        $this->assertMatchesRegularExpression('/name="reviewer"[^>]*value="'.$reviewer->id.'"/', $html);
        $this->assertStringContainsString('exampleDefaultReviewer::onRefresh', $html);
        $this->assertStringContainsString('exampleDefaultReviewer::onFindRecord', $html);
        $this->assertStringContainsString('exampleDefaultReviewer::onClearRecord', $html);
        $this->assertStringContainsString('Replace', $html);
    }

    public function test_an_empty_field_offers_select_without_clear(): void
    {
        Sample::factory()->create();

        $html = $this->section($this->get(route('reference.form-widgets.record-finder'))->getContent(), 'default');

        $this->assertStringContainsString('Select', $html);
        $this->assertStringNotContainsString('onClearRecord', $html);
        $this->assertStringNotContainsString('class="record-item"', $html);
    }

    public function test_the_find_popup_lists_records_that_pick_themselves_into_the_field(): void
    {
        Sample::factory()->create();
        $vera = User::factory()->create(['name' => 'Vera Verified']);
        User::factory()->count(5)->create();

        $response = $this->ajax('exampleDefaultReviewer::onFindRecord', ['recordfinder_flag' => 1]);

        $response->assertOk();
        $html = $response->json('result');
        $this->assertStringContainsString('Pick a reviewer', $html);
        $this->assertStringContainsString('class="recordfinder-popup"', $html);
        $this->assertStringContainsString('data-request-parent-form="#RecordFinder-exampleDefaultReviewer-reviewer"', $html);
        $this->assertMatchesRegularExpression('/<label class="form-control form-adorn is-searchable recordfinder-search">\s*<span class="form-adorn-icon" aria-hidden="true">\s*<i class="ph ph-magnifying-glass" aria-hidden="true"><\/i>\s*<\/span>\s*<input[^>]*name="exampleDefaultReviewerSearch\[term\]"[^>]*class="form-ghost"/', $html);
        $this->assertStringContainsString("jax.fetchControl(document.getElementById(&#039;RecordFinder-exampleDefaultReviewer-reviewer&#039;), &#039;recordfinder&#039;).updateRecord(this, &#039;".$vera->id."&#039;)", $html);
        $this->assertSame(5, substr_count($html, 'updateRecord(this'));
    }

    public function test_the_owner_finder_lists_only_verified_users(): void
    {
        Sample::factory()->create();
        User::factory()->create(['name' => 'Vera Verified']);
        User::factory()->unverified()->create(['name' => 'Una Unverified']);

        $response = $this->ajax('exampleScopesUser::onFindRecord', ['recordfinder_flag' => 1]);

        $response->assertOk();
        $this->assertStringContainsString('Pick an owner', $response->json('result'));
        $this->assertStringContainsString('Vera Verified', $response->json('result'));
        $this->assertStringNotContainsString('Una Unverified', $response->json('result'));
    }

    public function test_the_owner_finder_searches_the_start_of_a_name_or_email(): void
    {
        Sample::factory()->create();
        User::factory()->create(['name' => 'Gemma Stone', 'email' => 'gemma@example.com']);
        User::factory()->create(['name' => 'Angela Moss', 'email' => 'angela@example.com']);

        $response = $this->ajax('exampleScopesUserSearch::onSubmit', [
            'recordfinder_flag' => 1,
            'exampleScopesUserSearch' => ['term' => 'ge'],
        ]);

        $response->assertOk();
        $html = $this->opsHtml($response);
        $this->assertStringContainsString('Gemma Stone', $html);
        $this->assertStringNotContainsString('Angela Moss', $html);
    }

    public function test_the_popup_list_has_a_filter_a_sort_order_and_a_setup_button(): void
    {
        Sample::factory()->create();
        UserGroup::factory()->create(['name' => 'Support', 'code' => 'support']);
        UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);

        $response = $this->ajax('examplePopupPrimaryGroup::onFindRecord', ['recordfinder_flag' => 1]);

        $response->assertOk();
        $html = $response->json('result');
        $this->assertStringContainsString('recordfinder-filter', $html);
        $this->assertStringContainsString('Hide the guest group', $html);
        $this->assertStringContainsString('examplePopupPrimaryGroupList::onLoadSetup', $html);
        $this->assertMatchesRegularExpression('/Editors.*Support/s', $html);
    }

    public function test_the_popup_size_and_stacked_layout_render_on_the_field(): void
    {
        $category = Category::factory()->create(['name' => 'Guides', 'description' => 'How to build things.']);
        Sample::factory()->create(['primary_category_id' => $category->id]);

        $html = $this->get(route('reference.form-widgets.record-finder'))->getContent();

        $this->assertStringContainsString('data-size="large"', $this->section($html, 'popup'));

        $tree = $this->section($html, 'tree');
        $this->assertStringContainsString('class="field-recordfinder is-multi', $tree);
        $this->assertStringContainsString('<h4 class="recordname">Guides</h4>', $tree);
        $this->assertStringContainsString('<p class="description">How to build things.</p>', $tree);
    }

    public function test_a_tree_model_lists_as_a_tree(): void
    {
        Sample::factory()->create();
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        Category::factory()->childOf($guides)->create(['name' => 'Forms', 'slug' => 'forms']);

        $response = $this->ajax('exampleTreePrimaryCategory::onFindRecord', ['recordfinder_flag' => 1]);

        $response->assertOk();
        $this->assertStringContainsString('data-control="liststructurewidget"', $response->json('result'));
        $this->assertStringContainsString('Forms', $response->json('result'));
    }

    public function test_without_a_relation_the_record_is_read_by_its_key_column(): void
    {
        UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        Sample::factory()->create(['group_code' => 'editors']);

        $html = $this->section($this->get(route('reference.form-widgets.record-finder'))->getContent(), 'without-relation');

        $this->assertStringContainsString('<span class="recordname">Editors</span>', $html);
        $this->assertMatchesRegularExpression('/name="group_code"[^>]*value="editors"/', $html);
    }

    public function test_without_a_relation_the_popup_picks_by_key_and_applies_the_conditions(): void
    {
        Sample::factory()->create();
        UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        UserGroup::factory()->create(['name' => 'Guest', 'code' => 'guest']);

        $response = $this->ajax('exampleWithoutRelationGroupCode::onFindRecord', ['recordfinder_flag' => 1]);

        $response->assertOk();
        $html = $response->json('result');
        $this->assertStringContainsString('updateRecord(this, &#039;editors&#039;)', $html);
        $this->assertStringNotContainsString('updateRecord(this, &#039;guest&#039;)', $html);
    }

    public function test_refreshing_with_a_picked_key_shows_that_record(): void
    {
        Sample::factory()->create();
        $reviewer = User::factory()->create(['name' => 'Vera Reviewer']);

        $response = $this->ajax('exampleDefaultReviewer::onRefresh', ['reviewer' => $reviewer->id]);

        $response->assertOk();
        $html = $this->opsHtml($response);
        $this->assertStringContainsString('<span class="recordname">Vera Reviewer</span>', $html);
        $this->assertMatchesRegularExpression('/value="'.$reviewer->id.'"/', $html);
    }

    public function test_clearing_the_record_empties_the_field(): void
    {
        $reviewer = User::factory()->create(['name' => 'Vera Reviewer']);
        Sample::factory()->create(['reviewer_id' => $reviewer->id]);

        $response = $this->ajax('exampleDefaultReviewer::onClearRecord');

        $response->assertOk();
        $html = $this->opsHtml($response);
        $this->assertStringNotContainsString('Vera Reviewer', $html);
        $this->assertStringContainsString('Select', $html);
    }

    public function test_an_empty_key_saves_as_null(): void
    {
        $widget = $this->makeFinderForm(Sample::factory()->create())->getFormWidget('reviewer');

        $this->assertNull($widget->getSaveValue(''));
        $this->assertSame('5', $widget->getSaveValue('5'));
    }

    public function test_the_default_popup_title_is_find_record(): void
    {
        $this->app->instance('request', Request::create('/reference', 'POST', ['recordfinder_flag' => 1]));
        Facade::clearResolvedInstance('request');

        $widget = $this->makeFinderForm(Sample::factory()->create())->getFormWidget('reviewer');

        $this->assertStringContainsString('Find Record', (string) $widget->onFindRecord());
    }

    public function test_locked_fields_show_the_record_without_buttons(): void
    {
        $owner = User::factory()->create(['name' => 'Olive Owner']);
        $reviewer = User::factory()->create(['name' => 'Vera Reviewer']);
        Sample::factory()->create(['user_id' => $owner->id, 'reviewer_id' => $reviewer->id]);

        $html = $this->get(route('reference.form-widgets.record-finder'))->getContent();

        $readOnly = $this->section($html, 'read-only');
        $this->assertStringContainsString('is-preview', $readOnly);
        $this->assertStringContainsString('<span class="recordname">Vera Reviewer</span>', $readOnly);
        $this->assertMatchesRegularExpression('/name="_reviewer_readonly"[^>]*value="'.$reviewer->id.'"/', $readOnly);
        $this->assertStringNotContainsString('onFindRecord', $readOnly);

        $preview = $this->section($html, 'preview');
        $this->assertStringContainsString('is-preview', $preview);
        $this->assertStringContainsString('<span class="recordname">Olive Owner</span>', $preview);
        $this->assertStringNotContainsString('onFindRecord', $preview);
    }

    public function test_preview_mode_without_a_record_says_so(): void
    {
        $html = (string) $this->makeFinderForm(Sample::factory()->create(), [], ['previewMode' => true])->render();

        $this->assertStringContainsString('There is no record selected.', $html);
    }

    public function test_saving_the_page_stores_every_example(): void
    {
        [$owner, $reviewer] = User::factory()->count(2)->create();
        $group = UserGroup::factory()->create(['code' => 'editors']);
        $category = Category::factory()->create();
        $sample = Sample::factory()->create();

        $this->ajax('onSaveExamples', [
            'reviewer' => (string) $reviewer->id,
            'user' => (string) $owner->id,
            'primary_group' => (string) $group->id,
            'primary_category' => (string) $category->id,
            'group_code' => 'editors',
            '_reviewer_readonly' => (string) $owner->id,
            'owner_preview' => (string) $reviewer->id,
        ])->assertOk();

        $sample->refresh();
        $this->assertSame($reviewer->id, $sample->reviewer_id);
        $this->assertSame($owner->id, $sample->user_id);
        $this->assertSame($group->id, $sample->primary_group_id);
        $this->assertSame($category->id, $sample->primary_category_id);
        $this->assertSame('editors', $sample->group_code);
    }

    public function test_saving_an_emptied_finder_clears_the_relation(): void
    {
        $reviewer = User::factory()->create();
        $sample = Sample::factory()->create(['reviewer_id' => $reviewer->id, 'group_code' => 'editors']);

        $this->ajax('onSaveExamples', ['reviewer' => '', 'group_code' => ''])->assertOk();

        $sample->refresh();
        $this->assertNull($sample->reviewer_id);
        $this->assertNull($sample->group_code);
    }
}
