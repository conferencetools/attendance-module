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
use Zend\Validator\EmailAddress;
use Zend\Validator\GreaterThan;
use Zend\Validator\NotEmpty;

class NumberOfDelegates extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'delegateType',
            'options' => [
                'label' => 'Delegate Type',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'delegates',
            'options' => [
                'label' => 'Number of Delegates',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'email',
            'options' => [
                'label' => 'Email',
            ],
        ]);

        $this->add(new Submit('continue', ['label' => 'Continue']));
    }

    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'allow_empty' => false,
                'required' => true,
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => EmailAddress::class],
                ]
            ],
            'delegateType' => [
                'allow_empty' => false,
                'required' => true,
                'validators' => [
                    ['name' => NotEmpty::class],
                ]
            ],
            'delegates' => [
                'allow_empty' => true,
                'required' => true,
                'filters' => [
                    ['name' => \Zend\Filter\Digits::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => GreaterThan::class, 'options' => ['min' => 0]]
                ]
            ]
        ];
    }
}