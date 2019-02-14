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
    /**
     * @Jms\Type("int")
     */
    private $delegates = -1;

    public function __construct(string $id, string $email, int $delegates)
    {
        $this->id = $id;
        $this->email = $email;
        $this->delegates = $delegates;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDelegates(): int
    {
        return $this->delegates;
    }
}