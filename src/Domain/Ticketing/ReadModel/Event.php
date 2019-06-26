<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
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
    /** @ORM\Column(type="integer") */
    private $registered = 0;
    /** @ORM\Column(type="datetime") */
    private $startsOn;
    /** @ORM\Column(type="datetime") */
    private $endsOn;

    public function __construct(string $id, Descriptor $descriptor, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->id = $id;
        $this->name = $descriptor->getName();
        $this->description = $descriptor->getDescription();
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescriptor(): Descriptor
    {
        return new Descriptor($this->name, $this->description);
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

    public function getRemainingCapacity(): int
    {
        return $this->capacity - $this->registered;
    }

    public function increaseRegistered(int $by): void
    {
        $this->registered += $by;
    }

    public function decreaseRegistered(int $by): void
    {
        $this->registered -= $by;
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