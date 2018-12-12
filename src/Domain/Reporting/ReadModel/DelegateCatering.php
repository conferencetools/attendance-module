<?php

namespace ConferenceTools\Attendance\Domain\Reporting\ReadModel;

use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DelegateCatering
{
    /**
     * @var string
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $purchaseId;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $preference;
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $allergies;
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isPaid = false;

    public function __construct(string $id, string $purchaseId, string $name, DietaryRequirements $dietaryRequirements)
    {
        $this->id = $id;
        $this->purchaseId = $purchaseId;
        $this->name = $name;
        $this->preference = $dietaryRequirements->getPreference();
        $this->allergies = $dietaryRequirements->getAllergies();
    }

    public function detailsUpdated(string $name, DietaryRequirements $dietaryRequirements): void
    {
        $this->name = $name;
        $this->preference = $dietaryRequirements->getPreference();
        $this->allergies = $dietaryRequirements->getAllergies();
    }

    public function purchasePaid(): void
    {
        $this->isPaid = true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPreference(): string
    {
        return $this->preference;
    }

    public function getAllergies(): string
    {
        return $this->allergies;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }
}