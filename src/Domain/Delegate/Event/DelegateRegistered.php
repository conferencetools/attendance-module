<?php

namespace ConferenceTools\Attendance\Domain\Delegate\Event;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use JMS\Serializer\Annotation as Jms;

class DelegateRegistered
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
    private $id;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $purchaseId;
    /**
     * @Jms\Type("string")
     */
    private $delegateType = 'delegate';

    public function __construct(
        string $id,
        string $purchaseId,
        string $name,
        string $email,
        string $company,
        DietaryRequirements $dietaryRequirements,
        string $requirements,
        string $delegateType
    ) {
        $this->email = $email;
        $this->company = $company;
        $this->requirements = $requirements;
        $this->id = $id;
        $this->purchaseId = $purchaseId;
        $this->name = $name;
        $this->dietaryRequirements = $dietaryRequirements;
        $this->delegateType = $delegateType;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getDietaryRequirements(): DietaryRequirements
    {
        return $this->dietaryRequirements;
    }

    public function getRequirements(): string
    {
        return $this->requirements;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDelegateType(): string
    {
        return $this->delegateType;
    }
}