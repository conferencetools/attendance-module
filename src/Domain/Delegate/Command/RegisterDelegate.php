<?php


namespace ConferenceTools\Attendance\Domain\Delegate\Command;

use JMS\Serializer\Annotation as Jms;

class RegisterDelegate
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $firstname;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $lastname;
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
     * @var string
     * @Jms\Type("string")
     */
    private $twitter;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $requirements;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $purchaseId;

    public function __construct(string $purchaseId, string $firstname, string $lastname, string $email, string $company, string $twitter, string $requirements)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->company = $company;
        $this->twitter = $twitter;
        $this->requirements = $requirements;
        $this->purchaseId = $purchaseId;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getTwitter(): string
    {
        return $this->twitter;
    }

    public function getRequirements(): string
    {
        return $this->requirements;
    }
}