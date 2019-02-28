<?php

namespace ConferenceTools\Attendance\Domain\Delegate\ReadModel;

use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Delegate
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     */
    private $email;
    /**
     * @ORM\Column(type="string")
     */
    private $company;
    /**
     * @ORM\Column(type="string")
     */
    private $requirements;
    /**
     * @ORM\Column(type="string")
     */
    private $purchaseId;
    /**
     * @ORM\Column(type="json_array")
     */
    private $tickets = [];
    /**
     * @ORM\Column(type="string")
     */
    private $allergies;
    /**
     * @ORM\Column(type="string")
     */
    private $preference;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPaid = false;
    /**
     * @ORM\Column(type="string")
     */
    private $delegateType;
    /**
     * @ORM\Column(type="string")
     */
    private $purchaserEmail;
    /**
     * @ORM\Column(type="boolean")
     */
    private $checkedIn = false;

    public function __construct(
        string $id,
        string $purchaseId,
        string $purchaserEmail,
        string $name,
        string $email,
        string $company, DietaryRequirements $dietaryRequirements,
        string $requirements,
        string $delegateType
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->company = $company;
        $this->requirements = $requirements;
        $this->purchaseId = $purchaseId;
        $this->allergies = $dietaryRequirements->getAllergies();
        $this->preference = $dietaryRequirements->getPreference();
        $this->delegateType = $delegateType;
        $this->purchaserEmail = $purchaserEmail;
    }

    public function addTicket($ticketId)
    {
        $this->tickets[] =  $ticketId;
    }

    public function updateDetails($name, $email, $company, DietaryRequirements $dietaryRequirements, $requirements)
    {
        $this->name = $name;
        $this->email = $email;
        $this->company = $company;
        $this->allergies = $dietaryRequirements->getAllergies();
        $this->preference = $dietaryRequirements->getPreference();
        $this->requirements = $requirements;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getRequirements(): string
    {
        return $this->requirements;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getAllergies(): string
    {
        return $this->allergies;
    }

    public function getPreference(): string
    {
        return $this->preference;
    }

    public function purchasePaid(): void
    {
        $this->isPaid = true;
    }

    public function getDelegateType(): string
    {
        return $this->delegateType;
    }

    public function getPurchaserEmail(): string
    {
        return $this->purchaserEmail;
    }

    public function checkIn(): void
    {
        $this->checkedIn = true;
    }

    public function checkedIn(): bool
    {
        return $this->checkedIn;
    }
}