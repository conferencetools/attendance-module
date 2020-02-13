<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\Submit;
use Zend\Form\Form;

class ConfirmationForm extends Form
{
    public function init()
    {
        $this->setAttribute('class', 'form-inline');
        $this->add([
            'type' => Submit::class,
            'name' => 'confirm',
            'options' => [
                'label' => 'Yes',
            ],
            'attributes' => [
                'class'=> 'btn-primary mr-3',
            ]
        ]);
        $this->add([
            'type' => Submit::class,
            'name' => 'cancel',
            'options' => [
                'label' => 'No',
            ],
            'attributes' => [
                'class'=> 'btn-outline-danger',
            ]
        ]);
    }
}