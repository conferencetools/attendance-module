<?php


namespace ConferenceTools\Attendance\Form;


use Zend\Form\Element\Csrf;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class DelegateForm extends Form
{
    public function init()
    {
        $this->add(['type' => Fieldset\DelegateInformation::class, 'name' => 'delegate']);
        $this->add(new Csrf('security'));
        $this->add(new Submit('continue', ['label' => 'Update']));
    }
}