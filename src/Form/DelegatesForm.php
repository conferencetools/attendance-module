<?php


namespace ConferenceTools\Attendance\Form;


use Zend\Form\Element\Csrf;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Form;

class DelegatesForm extends Form
{
    public function init()
    {
        $maxDelegates = $this->getOption('maxDelegates');
        for ($i = 0; $i < $maxDelegates; $i++) {
            $fieldsetName = 'delegate_' . $i;
            $this->add(['type' => Fieldset\DelegateInformation::class, 'name' => $fieldsetName]);
            $this->get($fieldsetName)->add(
                new MultiCheckbox(
                    'tickets',
                    ['value_options' => $this->getOption('ticketOptions'), 'label' => 'Tickets']
                )
            );
        }

        $this->add(new Csrf('security'));
        $this->add(new Submit('continue', ['label' => 'Continue']));
    }
}