<?php


namespace ConferenceTools\Attendance\Form;


use Zend\Form\Element\Csrf;
use Zend\Form\Element\DateTime;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\GreaterThan;

class TicketForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'code',
            'options' => [
                'label' => 'Event code',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Event name',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'description',
            'options' => [
                'label' => 'Event description',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'quantity',
            'options' => [
                'label' => 'Ticket quantity',
            ],
        ]);
        $this->add([
            'type' => DateTime::class,
            'name' => 'from',
            'options' => [
                'label' => 'On sale from',
            ],
        ]);
        $this->add([
            'type' => DateTime::class,
            'name' => 'until',
            'options' => [
                'label' => 'On sale until',
            ],
        ]);

        $this->add([
            'type' => Radio::class,
            'name' => 'grossOrNet',
            'options' => [
                'value_options' => [
                    'gross' => 'Gross price',
                    'net' => 'Net price'
                ],
                'label' => '',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'price',
            'options' => [
                'label' => 'Price in pence',
            ],
        ]);
        $this->add(new Csrf('security'));
        $this->add(new Submit('create', ['label' => 'Create']));
    }

    public function getInputFilterSpecification()
    {
        return [
            'code' => [
                'allow_empty' => false,
                'required' => true,
            ],
            'name' => [
                'allow_empty' => false,
                'required' => true,
            ],
            'from' => [
                'allow_empty' => true,
            ],
            'until' => [
                'allow_empty' => true,
            ],
            'quantity' => [
                'allow_empty' => false,
                'required' => true,
                'validators' => [
                    ['name' => Digits::class],
                    ['name' => GreaterThan::class, ['options' => ['min' => 0]]]
                ]
            ],
            'price' => [
                'allow_empty' => false,
                'required' => true,
                'validators' => [
                    ['name' => Digits::class],
                    ['name' => GreaterThan::class, ['options' => ['min' => 0]]]
                ]
            ],
        ];
    }
}