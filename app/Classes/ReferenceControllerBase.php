<?php

namespace App\Classes;

use App\Models\Sample;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Classes\FormField;
use Larajax\Ui\Widgets\Form;

/**
 * ReferenceControllerBase serves the Reference pages, where each example on a form page is its own form on one shared record and the Save button stores them together.
 */
abstract class ReferenceControllerBase extends ControllerBase
{
    /**
     * @var array<string, Form> exampleForms built for the current page, keyed by example name.
     */
    public array $exampleForms = [];

    /**
     * @var Model|null exampleModel is the record the example forms edit.
     */
    protected ?Model $exampleModel = null;

    /**
     * @var string exampleFolder is the folder under resources/ui/reference holding the example YAML files.
     */
    protected string $exampleFolder = '';

    // #region docs-validation-rules
    /**
     * @var array<string, mixed> exampleRules validate the sample on every save of a reference page.
     */
    protected array $exampleRules = [
        'name' => 'required',
        'slug' => 'nullable|alpha_dash',
        'email' => 'nullable|email',
        'price' => 'nullable|numeric|min:0',
        'quantity' => 'nullable|integer|between:0,999',
        'discount_percent' => 'nullable|integer|between:1,90',
    ];
    // #endregion docs-validation-rules

    /**
     * makeExampleForms builds one form per example YAML file, all editing the same record with one session key.
     *
     * @param  array<int, string>  $names
     * @return array<string, Form>
     */
    protected function makeExampleForms(string $folder, array $names, ?Model $model = null): array
    {
        $this->exampleFolder = $folder;
        $this->exampleModel = $model ?? $this->referenceSample();

        $sessionKey = null;

        foreach ($names as $name) {
            $form = Form::make(
                alias: 'example'.Str::studly($name),
                model: $this->exampleModel,
                fields: "~/resources/ui/reference/{$folder}/{$name}.yaml",
                context: $this->exampleModel->exists ? 'update' : 'create',
                sessionKey: $sessionKey,
            );

            $sessionKey ??= $form->getSessionKey();

            $this->exampleForms[$name] = $form;
        }

        return $this->exampleForms;
    }

    /**
     * exampleVars returns the view variables the reference._form_example partial reads.
     *
     * @return array{exampleForms: array<string, Form>, exampleFolder: string, sessionKey: string|null}
     */
    protected function exampleVars(): array
    {
        $firstForm = reset($this->exampleForms) ?: null;

        return [
            'exampleForms' => $this->exampleForms,
            'exampleFolder' => $this->exampleFolder,
            'sessionKey' => $firstForm?->getSessionKey(),
        ];
    }

    /**
     * onSaveExamples validates and stores the posted values of every editable example form on the page, which also attaches the uploads held against the shared session key.
     */
    public function onSaveExamples(): AjaxResponse
    {
        $data = [];

        foreach ($this->exampleForms as $form) {
            if ($form->previewMode) {
                continue;
            }

            $data = array_replace_recursive($data, $form->getSaveData());
        }

        $this->validateExample($this->exampleModel, $data);

        DB::transaction(fn () => $this->saveExample($this->exampleModel, $data));

        return ajax()->flash('success', 'Saved.');
    }

    /**
     * validateExample checks the rules against the stored values with the posted ones on top, since each page posts only the fields of its own examples.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validateExample(Model $model, array $data): void
    {
        Validator::make(array_merge($model->getAttributes(), $data), $this->exampleRules)->validate();
    }

    // #region docs-save-example
    /**
     * saveExample fills the record from the posted values and saves it, where a belongs-to field maps onto its foreign key and a belongs-to-many field syncs once the record exists.
     *
     * @param  array<string, mixed>  $data
     */
    protected function saveExample(Model $model, array $data): void
    {
        $relatedIds = [];

        foreach ($data as $name => $value) {
            // Widgets that store their own values, such as file uploads and repeaters bound to a relation, post no value
            if ($value === FormField::NO_SAVE_DATA) {
                unset($data[$name]);

                continue;
            }

            if (!app('model.inspector')->hasRelation($model, $name)) {
                continue;
            }

            $relation = $model->{$name}();

            if ($relation instanceof BelongsTo) {
                $relation->associate($value ?: null);
            }
            elseif ($relation instanceof BelongsToMany) {
                $relatedIds[$name] = $value ?: [];
            }

            unset($data[$name]);
        }

        $model->fill($data)->save();

        foreach ($relatedIds as $name => $ids) {
            $model->{$name}()->sync($ids);
        }
    }
    // #endregion docs-save-example

    /**
     * referenceSample returns the record the form pages edit, which is the first sample, or a new one when none exists.
     */
    protected function referenceSample(): Sample
    {
        return Sample::query()->orderBy('id')->first()
            ?? new Sample(['name' => 'Reference sample']);
    }
}
