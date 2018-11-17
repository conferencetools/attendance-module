<?php


namespace ConferenceTools\Attendance\Domain\Delegate\Command;

use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use Phactor\Message\HasActorId;
use JMS\Serializer\Annotation as Jms;

class UpdateDelegateDetails implements HasActorId
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $name;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $email;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $company;
    /**
     * @var DietaryRequirements
     * @Jms\Type("ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements")
     */
    private $dietaryRequirements;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $requirements;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $delegateId;

    public function __construct(string $delegateId, string $name, string $email, string $company, DietaryRequirements $dietaryRequirements, string $requirements)
    {
        $this->name = $name;
        $this->email = $email;
        $this->company = $company;
        $this->requirements = $requirements;
        $this->delegateId = $delegateId;
        $this->dietaryRequirements = $dietaryRequirements;
    }

    public function getActorId(): string
    {
        return $this->delegateId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getRequirements(): string
    {
        return $this->requirements;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDietaryRequirements(): DietaryRequirements
    {
        return $this->dietaryRequirements;
    }
}