<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * @ORM\Embeddable()
 */
class Descriptor
{
    /**
     * @ORM\Column(type="string")
     * @Jms\Type("string")
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     * @Jms\Type("string")
     */
    private $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
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
