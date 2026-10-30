<?php

namespace App\Classes;

use App\Models\Sample;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Larajax\Classes\AjaxResponse;
use Larajax\Ui\Traits\FormModelSaver;
use Larajax\Ui\Widgets\Form;

/**
 * ReferenceControllerBase serves the Reference pages, where each example on a form page is its own form on one shared record and the Save button stores them together.
 */
abstract class ReferenceControllerBase extends ControllerBase
{
    use FormModelSaver;

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
     * onSaveExamples stores the posted values of every editable example form on the page, committing deferred uploads with the shared session key.
     */
    public function onSaveExamples(): AjaxResponse
    {
        $data = [];
        $sessionKey = null;

        foreach ($this->exampleForms as $form) {
            $sessionKey ??= $form->getSessionKey();

            if ($form->previewMode) {
                continue;
            }

            $data = array_replace_recursive($data, $form->getSaveData());
        }

        $this->performSaveOnModel($this->exampleModel, $data, $sessionKey);

        return ajax()->flash('success', 'Saved.');
    }

    /**
     * referenceSample returns the record the form pages edit, which is the first sample, or a new one when none exists.
     */
    protected function referenceSample(): Sample
    {
        return Sample::query()->orderBy('id')->first()
            ?? new Sample(['name' => 'Reference sample']);
    }
}
