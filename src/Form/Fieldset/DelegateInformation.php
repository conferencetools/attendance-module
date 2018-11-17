<?php

namespace ConferenceTools\Attendance\Form\Fieldset;

use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class DelegateInformation extends Fieldset implements InputFilterProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->add([
            'type' => Text::class,
            'name' => 'name',
            'options' => [
                'label' => 'Name',
                'help-block' => 'We\'ll print this on your delegate badge and use it to check you in on the day'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'company',
            'options' => [
                'label' => 'Company',
                'help-block' => 'We\'ll print this on your delegate badge'
            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'email',
            'options' => [
                'label' => 'Email',
                'help-block' => 'We\'ll add this email to our attendees mailing list to keep you up to date'
            ],
        ]);
        $this->add([
            'type' => Radio::class,
            'name' => 'preference',
            'options' => [
                'value_options' => [
                    DietaryRequirements::NONE => 'None',
                    DietaryRequirements::VEGETARIAN => 'Vegetarian',
                    DietaryRequirements::VEGAN => 'Vegan',
                ],
                'label' => 'Dietary Preference',
                'help-block' => 'We\'ll pass this on to the caterers'

            ],
        ]);
        $this->add([
            'type' => Text::class,
            'name' => 'allergies',
            'options' => [
                'label' => 'Any Allergies',
                'help-block' => 'We\'ll pass this on to the caterers, please also make yourself known to them on the day'
            ],
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'requirements',
            'options' => [
                'label' => 'Accessibility requirements',
                'help-block' => 'We\'ll pass this on to the venue, please get in touch if you\'d like to discuss these further'
            ],
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'tickets' => ['required' => false]
        ];
    }
}