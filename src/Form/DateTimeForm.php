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
        ]);
        $this->add(new Submit('update', ['label' => 'Put on sale']));
    }
}