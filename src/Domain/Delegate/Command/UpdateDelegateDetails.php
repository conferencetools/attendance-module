<?php


namespace ConferenceTools\Attendance\Domain\Delegate\Command;

use Phactor\Message\HasActorId;
use JMS\Serializer\Annotation as Jms;

class UpdateDelegateDetails implements HasActorId
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
    private $delegateId;

    public function __construct(string $delegateId, string $firstname, string $lastname, string $email, string $company, string $twitter, string $requirements)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->company = $company;
        $this->twitter = $twitter;
        $this->requirements = $requirements;
        $this->delegateId = $delegateId;
    }

    public function getActorId(): string
    {
        return $this->delegateId;
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