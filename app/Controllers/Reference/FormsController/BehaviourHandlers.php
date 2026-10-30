<?php

namespace App\Controllers\Reference\FormsController;

use Larajax\Classes\AjaxResponse;

/**
 * BehaviourHandlers holds the AJAX handlers of the Behaviour page.
 */
trait BehaviourHandlers
{
    /**
     * onChangeHeadline is the headline field's change handler, refreshing the headline stats partial from the posted values without saving them.
     */
    public function onChangeHeadline(): AjaxResponse
    {
        $form = $this->exampleForms['change-handler'];

        $form->setFormValues();

        $statsField = $form->getField('_headline_stats');

        return ajax()->update([
            '#'.$statsField->getId('group') => $form->renderField($statsField, ['useContainer' => false]),
        ]);
    }
}
