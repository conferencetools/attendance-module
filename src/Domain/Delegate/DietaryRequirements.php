<?php


namespace ConferenceTools\Attendance\Domain\Delegate;

use JMS\Serializer\Annotation as Jms;

class DietaryRequirements
{
    const VEGETARIAN = 'vegetarian';
    const VEGAN = 'vegan';
    const NONE = 'none';

    /**
     * @var string
     * @Jms\Type("string")
     */
    private $preference;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $allergies;

    public function __construct(string $preference, string $allergies)
    {
        if (!in_array($preference, [self::VEGAN, self::NONE, self::VEGETARIAN])) {
            throw new \DomainException('Unknown preference: ' . $preference);
        }
        $this->preference = $preference;
        $this->allergies = $allergies;
    }

    public function getPreference(): string
    {
        return $this->preference;
    }

    public function getAllergies(): string
    {
        return $this->allergies;
    }
}