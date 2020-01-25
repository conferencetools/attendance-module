<?php

namespace ConferenceTools\Attendance\Form\Sponsor;

use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class QuestionForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'handle',
            'options' => [
                'label' => 'Handle',
                'help-block' => 'A machine usable short description of this question. Avoid spaces, keep to lowercase eg free-trial or recruitment'
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'question',
            'options' => [
                'label' => 'Question',
                'help-block' => 'This is the question displayed to the delegate. eg Would you like a free trial?'
            ],
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'create',
            'options' => [
                'label' => 'Create',
            ],
            'attributes' => [
                'class'=> 'btn-primary',
            ]
        ]);
    }
}