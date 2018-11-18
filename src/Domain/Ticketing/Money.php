<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Money
 * @ORM\Embeddable()
 */
final class Money
{
    /**
     * @JMS\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function equals(Money $other): bool
    {
        return $other->amount === $this->amount;
    }

    public function compare(Money $other): int
    {
        if ($this->amount < $other->amount) {
            return -1;
        } elseif ($this->amount == $other->amount) {
            return 0;
        } else {
            return 1;
        }
    }

    public function greaterThan(Money $other): bool
    {
        return 1 === $this->compare($other);
    }

    public function lessThan(Money $other): bool
    {
        return -1 === $this->compare($other);
    }

    public function add(Money $addend): Money
    {
        return new self($this->amount + $addend->amount);
    }

    public function subtract(Money $subtrahend): Money
    {
        return new self($this->amount - $subtrahend->amount);
    }

    public function multiply($multiple): Money
    {
        return new self(ceil($this->amount * $multiple));
    }
}
