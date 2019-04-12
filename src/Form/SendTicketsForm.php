<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class SendTicketsForm extends Form
{
    public function init()
    {
        $this->add(
            new MultiCheckbox(
                'tickets',
                ['value_options' => $this->getOption('ticketOptions'), 'label' => 'Tickets']
            )
        );
        $this->add(new Submit('create', ['label' => 'Send']));
    }
}