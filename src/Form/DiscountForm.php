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

class DiscountForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Discount name',
            ],
        ]);

        $this->add([
            'type' => DateTime::class,
            'name' => 'from',
            'options' => [
                'label' => 'Usable from',
            ],
        ]);
        $this->add([
            'type' => DateTime::class,
            'name' => 'until',
            'options' => [
                'label' => 'Usable until',
            ],
        ]);

        $this->add([
            'type' => Radio::class,
            'name' => 'type',
            'options' => [
                'value_options' => [
                    'percentage' => 'Percentage',
                    'perTicket' => 'Per Ticket',
                    'perPurchase' => 'Per Purchase'
                ],
                'label' => '',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'percent',
            'options' => [
                'label' => 'Discount percentage',
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
                'label' => 'Discount in pence',
            ],
        ]);

        $this->add([
            'type' => MultiCheckbox::class,
            'name' => 'ticketIds',
            'options' => [
                'value_options' => $this->getOption('tickets'),
                'label' => 'Usable for Tickets'
            ]
        ]);

        $this->add(new Csrf('security'));
        $this->add(new Submit('create', ['label' => 'Create']));
    }

    public function getInputFilterSpecification()
    {
        return [
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