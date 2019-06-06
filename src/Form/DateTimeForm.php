<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\DateTime;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class DateTimeForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => DateTime::class,
            'name' => 'datetime',
            'options' => [
                'label' => $this->getOption('fieldLabel'),
            ],
            'attributes' => [
                'class'=> 'datetimepicker-input',
                'id' => "dtpicker",
                'data-toggle' => "datetimepicker",
                'data-target' => "#dtpicker",
                'autocomplete' => 'off',
            ],
        ]);
        $this->add([
            'type' => Submit::class,
            'name' => 'create',
            'options' => [
                'label' => $this->getOption('submitLabel'),
            ],
            'attributes' => [
                'class'=> 'btn-primary',
            ]
        ]);
    }
}