<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * @ORM\Embeddable()
 */
class Event
{
    /**
     * @ORM\Column(type="string")
     * @Jms\Type("string")
     * @var string
     */
    private $code;
    /**
     * @ORM\Column(type="string")
     * @Jms\Type("string")
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     * @Jms\Type("string")
     * @var string
     */
    private $description;

    public function __construct(string $code, string $name, string $description)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}