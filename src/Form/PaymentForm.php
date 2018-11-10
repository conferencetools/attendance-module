<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;

class PaymentForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('payment-form', $options);
    }

    public function init()
    {
        $this->add(new Hidden('stripe_token'));
        $this->add(new Csrf('security'));
    }
}