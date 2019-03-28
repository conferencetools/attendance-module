<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\Submit;
use Zend\Form\Form;

class ConfirmationForm extends Form
{
    public function init()
    {
        $this->add(new Submit('confirm', ['label' => 'Yes']));
        $this->add(new Submit('cancel', ['label' => 'No']));
    }
}