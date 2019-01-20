<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;

use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class AvailabilityDates
{
    /**
     * @JMS\Type("DateTime")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $availableFrom;
    /**
     * @JMS\Type("DateTime")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $availableUntil;

    private function __construct() {}

    public static function after(\DateTime $after)
    {
        $instance = new self;
        $instance->availableFrom = $after;

        return $instance;
    }

    public static function until(\DateTime $until)
    {
        $instance = new self;
        $instance->availableUntil = $until;

        return $instance;
    }

    public static function between(\DateTime $from, \DateTime $to)
    {
        if ($to <= $from) {
            throw new \DomainException('From date must be before to date');
        }

        $instance = new self;
        $instance->availableFrom = $from;
        $instance->availableUntil = $to;

        return $instance;
    }

    public static function always()
    {
        return new self;
    }

    public function getAvailableFrom()
    {
        return $this->availableFrom;
    }

    public function getAvailableUntil()
    {
        return $this->availableUntil;
    }

    public function availableNow(): bool
    {
        $now = new \DateTime('now', new \DateTimeZone(\DateTimeZone::UTC));

        if ($this->availableFrom === null) {
            return $this->availableUntil === null || $now <= $this->availableUntil;
        }

        if ($this->availableUntil === null) {
            return $this->availableFrom <= $now;
        }

        return $this->availableFrom <= $now && $now <= $this->availableFrom;
    }
}