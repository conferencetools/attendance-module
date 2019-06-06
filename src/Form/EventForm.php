<?php


namespace ConferenceTools\Attendance\Form;


use Zend\Form\Element\DateTime;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class EventForm extends Form
{
    public function init()
    {
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
            'name' => 'capacity',
            'options' => [
                'label' => 'Maximum capacity for the event',
            ],
        ]);
        $this->add([
            'type' => DateTime::class,
            'name' => 'startsOn',
            'options' => [
                'label' => 'Start date',
            ],
            'attributes' => [
                'class'=> 'datetimepicker-input',
                'id' => "startsOn",
                'data-toggle' => "datetimepicker",
                'data-target' => "#startsOn",
                'autocomplete' => 'off',
            ],
        ]);
        $this->add([
            'type' => DateTime::class,
            'name' => 'endsOn',
            'options' => [
                'label' => 'End date',
            ],
            'attributes' => [
                'class'=> 'datetimepicker-input',
                'id' => "endsOn",
                'data-toggle' => "datetimepicker",
                'data-target' => "#endsOn",
                'autocomplete' => 'off',
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