<?php

namespace App\Controllers\KitchenSinkController;

use App\Models\Sample;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Traits\FormModelSaver;
use Larajax\Ui\Widgets\Form;

/**
 * SampleForm builds the showcase form and handles saving, deleting and the form's own AJAX handlers.
 */
trait SampleForm
{
    use FormModelSaver;

    /**
     * @var Form|null formWidget is kept on the instance so the automatic handler routing reaches its AJAX handlers.
     */
    public ?Form $formWidget = null;

    /**
     * onStore persists a new sample, as the AJAX handler for the create form.
     */
    public function onStore(): AjaxResponse
    {
        $sample = new Sample;

        $this->formWidget = $this->makeFormWidget($sample, 'create');

        $this->saveForm($sample);

        return ajax()
            ->flash('success', 'Sample created.')
            ->redirect(route('kitchen-sink.edit', $sample));
    }

    /**
     * onUpdate persists changes to a sample, as the AJAX handler for the edit form.
     */
    public function onUpdate(Sample $sample): AjaxResponse
    {
        $this->formWidget = $this->makeFormWidget($sample, 'update');

        $this->saveForm($sample);

        return ajax()->flash('success', 'Sample updated.');
    }

    /**
     * onDestroy deletes a sample, as the AJAX handler for the edit form.
     */
    public function onDestroy(Sample $sample): AjaxResponse
    {
        $sample->delete();

        return ajax()
            ->flash('success', 'Sample deleted.')
            ->redirect(route('kitchen-sink.index'));
    }

    /**
     * onChangeHeadline is the headline field's change handler, refreshing the headline stats partial from the posted values.
     */
    public function onChangeHeadline(): AjaxResponse
    {
        $this->formWidget->setFormValues();

        $statsField = $this->formWidget->getField('_headline_stats');

        return ajax()->update([
            '#'.$statsField->getId('group') => $this->formWidget->renderField($statsField, ['useContainer' => false]),
        ]);
    }

    /**
     * onSearchTimezones returns the timezones matching the typed term, in the format the AJAX dropdown expects.
     *
     * @return array{results: array<int, array{id: string, text: string}>}
     */
    public function onSearchTimezones(): array
    {
        $term = strtolower(trim((string) input('term')));

        $results = collect(timezone_identifiers_list())
            ->filter(fn (string $zone) => $term === '' || str_contains(strtolower($zone), $term))
            ->take(20)
            ->map(fn (string $zone) => ['id' => $zone, 'text' => str_replace('_', ' ', $zone)])
            ->values()
            ->all();

        return ['results' => $results];
    }

    /**
     * makeFormWidget builds the showcase form for a given context.
     */
    protected function makeFormWidget(Sample $sample, string $context): Form
    {
        return Form::make(
            context: $context,
            model: $sample,
            fields: '~/resources/ui/sample/fields.yaml',
        );
    }

    /**
     * saveForm fills the sample and its relations from the form data and saves it, committing deferred file uploads with the session key.
     */
    protected function saveForm(Sample $sample): void
    {
        $this->performSaveOnModel(
            $sample,
            $this->formWidget->getSaveData(),
            $this->formWidget->getSessionKey()
        );
    }
}
