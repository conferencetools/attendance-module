<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;

class PurchaseStartedBy
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $email;

    public function __construct(string $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}