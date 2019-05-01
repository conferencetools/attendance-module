<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Event
{
    /** @ORM\Id @ORM\Column(type="string") */
    private $id;
    /** @ORM\Column(type="string") */
    private $name;
    /** @ORM\Column(type="string") */
    private $description;
    /** @ORM\Column(type="integer") */
    private $capacity;
    /** @ORM\Column(type="datetime") */
    private $startsOn;
    /** @ORM\Column(type="datetime") */
    private $endsOn;

    public function __construct(string $id, string $name, string $description, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getStartsOn(): \DateTime
    {
        return $this->startsOn;
    }

    public function getEndsOn(): \DateTime
    {
        return $this->endsOn;
    }
}