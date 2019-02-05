<?php


namespace ConferenceTools\Attendance\Form;


use Zend\Form\Element\Csrf;
use Zend\Form\Element\DateTime;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\GreaterThan;

class DiscountCodeForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'code',
            'options' => [
                'label' => 'Discount code',
            ],
        ]);

        $this->add(new Csrf('security'));
        $this->add(new Submit('create', ['label' => 'Create']));
    }
}