<?php

namespace ConferenceTools\Attendance\Form;

use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use TwbBundle\Form\View\Helper\TwbBundleForm;
use Zend\Form\Element\Number;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Digits;
use Zend\Validator\EmailAddress;
use Zend\Validator\GreaterThan;
use Zend\Validator\NotEmpty;

class PurchaseForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        /** @var Ticket[] $tickets */
        $tickets = $this->getOption('tickets');
        $fieldset = new Fieldset('quantity');

        foreach ($tickets as $ticketId => $ticket) {
            if ($ticket->getRemaining() > 0) {
                $fieldset->add((new Number($ticketId))->setAttributes(['class' => 'form-control', 'min' => 0, 'max' => $ticket->getRemaining(), 'value' => 0]));
            }
        }

        $this->add($fieldset);
        $this->add([
            'type' => Text::class,
            'name' => 'purchase_email',
            'options' => [
                'label' => 'Email',
                'label_attributes' => ['class' => 'col-sm-4 control-label'],
                'twb-layout' => TwbBundleForm::LAYOUT_HORIZONTAL,
                'column-size' => 'sm-8',
            ],
            'attributes' => ['class' => 'form-control', 'placeholder' => 'Your receipt will be emailed to this address']
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'delegates',
            'options' => [
                'label' => 'Number of delegates',
                'label_attributes' => ['class' => 'col-sm-4 control-label'],
                'twb-layout' => TwbBundleForm::LAYOUT_HORIZONTAL,
                'column-size' => 'sm-8',
            ],
            'attributes' => ['class' => 'form-control', 'placeholder' => 'Number of delegates the tickets are for']
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'discount_code',
            'options' => [
                'label' => 'Discount Code',
                'label_attributes' => ['class' => 'col-sm-4 control-label'],
                'twb-layout' => TwbBundleForm::LAYOUT_HORIZONTAL,
                'column-size' => 'sm-8',
            ],
            'attributes' => ['class' => 'form-control', 'placeholder' => 'Discount Code']
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'purchase_email' => [
                'allow_empty' => false,
                'required' => true,
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => EmailAddress::class],
                ]
            ],
            'delegates' => [
                'allow_empty' => true,
                'required' => true,
            ],
            'discount_code' => [
                'allow_empty' => true,
                'required' => true,
            ]
        ];
    }
}