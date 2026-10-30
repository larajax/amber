<?php

namespace App\Controllers\Reference\FormsController;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Larajax\Ui\Widgets\Form;

/**
 * StandaloneForms builds example forms that Save leaves out, such as a preview or a form on a new record.
 */
trait StandaloneForms
{
    /**
     * makeStandaloneForms builds one form per example YAML file on the given record, passing the extra form options to each, where an arrayName keeps their inputs apart from the saved examples.
     *
     * @param  array<int, string>  $names
     * @param  array<string, mixed>  $options
     * @return array<string, Form>
     */
    protected function makeStandaloneForms(string $folder, array $names, Model $model, array $options = []): array
    {
        $forms = [];

        foreach ($names as $name) {
            $forms[$name] = Form::make(...[
                'alias' => 'standalone'.Str::studly($name),
                'model' => $model,
                'fields' => "~/resources/ui/reference/{$folder}/{$name}.yaml",
                ...$options,
            ]);
        }

        return $forms;
    }
}
