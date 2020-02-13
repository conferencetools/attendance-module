<?php

namespace ConferenceTools\Attendance\Form\Admin;

use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class SponsorForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Sponsor name',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'email',
            'options' => [
                'label' => 'Sponsor email',
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'password',
            'options' => [
                'label' => 'Sponsor password',
            ],
        ]);

        $this->add([
            'type' => Submit::class,
            'name' => 'create',
            'options' => [
                'label' => 'Create',
            ],
            'attributes' => [
                'class'=> 'btn-primary',
            ]
        ]);
    }
}