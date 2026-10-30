<?php

namespace Tests\Feature\Reference\FormWidgets;

use App\Controllers\Reference\FormWidgetsController;
use App\Models\Category;
use App\Models\Sample;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Larajax\Ui\Widgets\Form;
use Tests\TestCase;

class RelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ajax posts one AJAX handler request to the relation page.
     */
    protected function ajax(string $handler, array $data = []): TestResponse
    {
        return $this->withHeaders([
            'X-AJAX-HANDLER' => $handler,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('reference.form-widgets.relation'), $data);
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
     * makeCategoryTree creates a root category with a child and a grandchild.
     *
     * @return array<int, Category>
     */
    protected function makeCategoryTree(): array
    {
        $guides = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
        $forms = Category::factory()->childOf($guides)->create(['name' => 'Forms', 'slug' => 'forms']);
        $validation = Category::factory()->childOf($forms)->create(['name' => 'Validation', 'slug' => 'validation']);

        return [$guides, $forms, $validation];
    }

    /**
     * renderRelationField renders a form holding one relation field without a scope for the given model.
     */
    protected function renderRelationField(Model $model, string $relationName): string
    {
        $form = Form::createIn(new FormWidgetsController, [
            'model' => $model,
            'fields' => [
                $relationName => ['label' => 'Relation', 'type' => 'relation'],
            ],
        ]);

        $form->bindToController();

        return (string) $form->render();
    }

    public function test_the_page_renders_every_example_beside_its_yaml(): void
    {
        Sample::factory()->create();

        $html = $this->get(route('reference.form-widgets.relation'))->assertOk()->getContent();

        foreach (['dropdown', 'checkbox-list', 'options-query', 'tree', 'tree-checkbox-list', 'quick-create', 'read-only', 'disabled', 'quick-create-form'] as $file) {
            $this->assertStringContainsString("resources/ui/reference/form-widgets/relation/{$file}.yaml", $html);
        }

        $this->assertSame(8, substr_count($html, 'class="relation-widget"'));
        $this->assertStringContainsString('data-request="onSaveExamples"', $html);
    }

    public function test_a_belongs_to_relation_renders_a_sorted_dropdown_with_an_empty_option(): void
    {
        $zed = User::factory()->create(['name' => 'Zed Owner']);
        User::factory()->create(['name' => 'Amy Other']);
        Sample::factory()->create(['user_id' => $zed->id]);

        $dropdown = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'dropdown');

        $this->assertMatchesRegularExpression('/<select[^>]*name="user"/', $dropdown);
        $this->assertMatchesRegularExpression('/value=""\s*>No owner<\/option>.*>Amy Other<\/option>.*>Zed Owner<\/option>/s', $dropdown);
        $this->assertMatchesRegularExpression('/selected="selected"\s+value="'.$zed->id.'"\s*>Zed Owner<\/option>/', $dropdown);
    }

    public function test_a_belongs_to_many_relation_renders_a_checkbox_list_without_the_guest_group(): void
    {
        $editors = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        UserGroup::factory()->create(['name' => 'Support', 'code' => 'support']);
        UserGroup::factory()->create(['name' => 'Guest', 'code' => 'guest']);
        Sample::factory()->create()->groups()->attach($editors);

        $list = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'checkbox-list');

        $this->assertMatchesRegularExpression('/name="groups\[\]"\s+value="'.$editors->id.'"\s+checked="checked"/', $list);
        $this->assertMatchesRegularExpression('/<label[^>]*>\s*Support\s*<\/label>/', $list);
        $this->assertDoesNotMatchRegularExpression('/<label[^>]*>\s*Guest\s*<\/label>/', $list);
    }

    public function test_a_checkbox_list_with_no_options_shows_the_empty_option(): void
    {
        Sample::factory()->create();

        $list = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'checkbox-list');

        $this->assertStringContainsString('There are no groups to pick from.', $list);
    }

    public function test_the_options_query_uses_the_select_conditions_and_exclusion(): void
    {
        $owner = User::factory()->create(['name' => 'Olive Owner', 'email' => 'olive@example.com']);
        User::factory()->create(['name' => 'Vera Verified', 'email' => 'vera@example.com']);
        User::factory()->unverified()->create(['name' => 'Una Unverified']);
        Sample::factory()->create(['user_id' => $owner->id]);

        $query = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'options-query');

        $this->assertStringContainsString('>Vera Verified (vera@example.com)</option>', $query);
        $this->assertStringNotContainsString('Una Unverified', $query);
        $this->assertStringNotContainsString('Olive Owner', $query);
    }

    public function test_a_tree_relation_indents_the_dropdown_and_nests_the_checkboxes(): void
    {
        [, $forms, $validation] = $this->makeCategoryTree();
        $sample = Sample::factory()->create(['primary_category_id' => $forms->id]);
        $sample->categories()->attach($validation);

        $html = $this->get(route('reference.form-widgets.relation'))->getContent();

        $tree = $this->section($html, 'tree');
        $this->assertMatchesRegularExpression('/>Guides<\/option>\s*<option[^>]*selected="selected"\s+value="'.$forms->id.'"\s*>&nbsp;&nbsp;&nbsp;Forms<\/option>\s*<option[^>]*>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Validation<\/option>/', $tree);

        $checkboxes = $this->section($html, 'tree-checkbox-list');
        $this->assertSame(2, substr_count($checkboxes, '<ul class="checkboxlist-group">'));
        $this->assertMatchesRegularExpression('/value="'.$validation->id.'"\s+checked="checked"/', $checkboxes);
    }

    public function test_the_relation_dropdown_offers_a_create_new_option(): void
    {
        Sample::factory()->create();

        $quickCreate = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'quick-create');

        $this->assertStringContainsString('data-control="relation-quick-create"', $quickCreate);
        $this->assertStringContainsString('exampleQuickCreatePrimaryGroup::onLoadQuickCreateForm', $quickCreate);
        $this->assertStringContainsString('data-popup-size="small"', $quickCreate);
        $this->assertStringContainsString('- Create New Primary Group -', $quickCreate);
    }

    public function test_the_quick_create_form_renders_the_related_model_form_in_its_own_context(): void
    {
        Sample::factory()->create();

        $response = $this->ajax('exampleQuickCreatePrimaryGroup::onLoadQuickCreateForm', ['_relation_quick_create' => 1]);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('New Primary Group', $content);
        $this->assertStringContainsString('QuickCreate[name]', $content);
        $this->assertStringContainsString('exampleQuickCreatePrimaryGroup::onQuickCreateRecord', $content);
        $this->assertStringNotContainsString('QuickCreate[users]', $content);
    }

    public function test_quick_creating_a_record_creates_and_selects_it(): void
    {
        Sample::factory()->create();

        $response = $this->ajax('exampleQuickCreatePrimaryGroup::onQuickCreateRecord', [
            '_relation_quick_create' => 1,
            'QuickCreate' => ['name' => 'Moderators', 'code' => 'moderators'],
        ]);

        $response->assertOk();
        $group = UserGroup::where('code', 'moderators')->first();
        $this->assertNotNull($group);
        $this->assertSame('Moderators', $group->name);

        $html = $response->json('__ajax.ops.0.html');
        $this->assertStringContainsString('data-quick-created', $html);
        $this->assertMatchesRegularExpression('/<option[^>]*selected="selected"[^>]*value="'.$group->id.'"/', $html);
    }

    public function test_saving_with_the_create_new_option_selected_stores_no_group(): void
    {
        $group = UserGroup::factory()->create(['code' => 'editors']);
        $sample = Sample::factory()->create(['primary_group_id' => $group->id]);

        $this->ajax('onSaveExamples', ['primary_group' => '__quick_create__'])->assertOk();

        $this->assertNull($sample->refresh()->primary_group_id);
    }

    public function test_locked_fields_show_their_values(): void
    {
        $owner = User::factory()->create(['name' => 'Olive Owner']);
        $editors = UserGroup::factory()->create(['name' => 'Editors', 'code' => 'editors']);
        Sample::factory()->create(['user_id' => $owner->id])->groups()->attach($editors);

        $html = $this->get(route('reference.form-widgets.relation'))->getContent();

        $readOnly = $this->section($html, 'read-only');
        $this->assertMatchesRegularExpression('/<div class="form-control" readonly>\s*Olive Owner\s*<\/div>/', $readOnly);
        $this->assertMatchesRegularExpression('/name="_owner_readonly"\s+value="'.$owner->id.'"/', $readOnly);
        $this->assertStringNotContainsString('<select', $readOnly);

        $disabled = $this->section($html, 'disabled');
        $this->assertStringContainsString('control-disabled', $disabled);
        $this->assertMatchesRegularExpression('/value="'.$editors->id.'"\s+checked="checked"\s+disabled/', $disabled);
    }

    public function test_saving_the_page_stores_every_relation(): void
    {
        [$guides, $forms, $validation] = $this->makeCategoryTree();
        [$owner, $reviewer] = User::factory()->count(2)->create();
        [$editors, $support] = UserGroup::factory()->count(2)->create();
        $sample = Sample::factory()->create();

        $this->ajax('onSaveExamples', [
            'user' => (string) $owner->id,
            'groups' => [(string) $editors->id, (string) $support->id],
            'reviewer' => (string) $reviewer->id,
            'primary_category' => (string) $forms->id,
            'categories' => [(string) $guides->id, (string) $validation->id],
            'primary_group' => (string) $support->id,
            '_owner_readonly' => (string) $reviewer->id,
            'groups_disabled' => [(string) $support->id],
        ])->assertOk();

        $sample->refresh();
        $this->assertSame($owner->id, $sample->user_id);
        $this->assertSame($reviewer->id, $sample->reviewer_id);
        $this->assertSame($forms->id, $sample->primary_category_id);
        $this->assertSame($support->id, $sample->primary_group_id);
        $this->assertEqualsCanonicalizing([$editors->id, $support->id], $sample->groups()->pluck('user_groups.id')->all());
        $this->assertEqualsCanonicalizing([$guides->id, $validation->id], $sample->categories()->pluck('categories.id')->all());
    }

    public function test_option_names_like_a_language_file_render_as_given(): void
    {
        // A translated option named like a language file would otherwise be the whole file array
        $category = Category::factory()->create(['name' => 'validation', 'slug' => 'validation']);
        Sample::factory()->create(['primary_category_id' => $category->id]);

        $tree = $this->section($this->get(route('reference.form-widgets.relation'))->getContent(), 'tree');

        $this->assertMatchesRegularExpression('/value="'.$category->id.'"\s*>validation<\/option>/', $tree);
    }

    public function test_a_relation_to_the_same_model_leaves_out_the_record_being_edited(): void
    {
        [$guides, $forms] = $this->makeCategoryTree();

        $html = $this->renderRelationField($forms->refresh(), 'parent');
        $this->assertStringContainsString('value="'.$guides->id.'"', $html);
        $this->assertStringNotContainsString('<option value="'.$forms->id.'"', $html);

        // A new record has nothing to leave out yet
        $html = $this->renderRelationField(new Category, 'parent');
        $this->assertMatchesRegularExpression('/value="'.$forms->id.'"\s*>/', $html);
    }

    public function test_a_relation_field_renders_for_a_plain_eloquent_model(): void
    {
        $group = UserGroup::factory()->create(['name' => 'Unscoped Group', 'code' => 'unscoped']);

        $html = $this->renderRelationField(User::factory()->create(), 'groups');

        $this->assertStringContainsString('Unscoped Group', $html);
        $this->assertStringContainsString('value="'.$group->id.'"', $html);
    }

    public function test_saving_empty_values_clears_the_relations(): void
    {
        $owner = User::factory()->create();
        $editors = UserGroup::factory()->create();
        $sample = Sample::factory()->create(['user_id' => $owner->id]);
        $sample->groups()->attach($editors);

        $this->ajax('onSaveExamples', ['user' => '', 'groups' => ''])->assertOk();

        $sample->refresh();
        $this->assertNull($sample->user_id);
        $this->assertSame(0, $sample->groups()->count());
    }
}
