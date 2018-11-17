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

    public function __construct($id, $purchaseId, $name, $email, $company, DietaryRequirements $dietaryRequirements, $requirements)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->company = $company;
        $this->requirements = $requirements;
        $this->purchaseId = $purchaseId;
        $this->allergies = $dietaryRequirements->getAllergies();
        $this->preference = $dietaryRequirements->getPreference();
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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function getPurchaseId()
    {
        return $this->purchaseId;
    }

    public function getTickets()
    {
        return $this->tickets;
    }

    public function getAllergies()
    {
        return $this->allergies;
    }

    public function getPreference()
    {
        return $this->preference;
    }
}