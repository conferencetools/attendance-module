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
            'type' => Radio::class,
            'name' => 'eventId',
            'options' => [
                'label' => 'Event',
                'value_options' => $this->getOption('eventOptions')
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Ticket name',
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'description',
            'options' => [
                'label' => 'Ticket description',
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
            'eventId' => [
                'allow_empty' => false,
                'required' => true,
            ],
            'name' => [
                'allow_empty' => false,
                'required' => true,
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