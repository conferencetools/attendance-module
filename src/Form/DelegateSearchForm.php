<?php

namespace ConferenceTools\Attendance\Form;

use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class DelegateSearchForm extends Form
{
    public function init()
    {
        $this->add(new Text('name', ['label' => 'Name']));
        $this->add(new Text('email', ['label' => 'Email']));
        $this->add(new Text('id', ['label' => 'Ticket Id']));
        $this->add(new Submit('submit', ['label' => 'Search']));
    }
}