<?php

namespace ConferenceTools\Attendance\Domain\Delegate\ReadModel;

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
    private $firstname;
    /**
     * @ORM\Column(type="string")
     */
    private $lastname;
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
    private $twitter;
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

    public function __construct($id, $purchaseId, $firstname, $lastname, $email, $company, $twitter, $requirements)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->company = $company;
        $this->twitter = $twitter;
        $this->requirements = $requirements;
        $this->purchaseId = $purchaseId;
    }

    public function addTicket($ticketId)
    {
        $this->tickets[] =  $ticketId;
    }

    public function updateDetails($firstname, $lastname, $email, $company, $twitter, $requirements)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->company = $company;
        $this->twitter = $twitter;
        $this->requirements = $requirements;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
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
}