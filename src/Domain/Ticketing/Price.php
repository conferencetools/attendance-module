<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Money
 * @ORM\Embeddable()
 */
class Price
{
    /**
     * @Jms\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $net;

    /**
     * @Jms\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $taxRate;

    private function __construct(int $net, int $taxRate)
    {
        $this->net = $net;
        $this->taxRate = $taxRate;
    }

    public function getNet(): int
    {
        return $this->net;
    }

    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    public function getGross(): int
    {
        return $this->net + $this->calculateTax();
    }

    public function getTax(): int
    {
        return $this->calculateTax();
    }

    public static function fromNetCost(int $net, int $taxRate)
    {
        return new static($net, $taxRate);
    }

    public static function fromGrossCost(int $gross, int $taxRate)
    {
        $inverseTaxRate = 1 / (1 + self::convertPercentageToFloat($taxRate));

        $net = ceil($gross * $inverseTaxRate);

        return new static($net, $taxRate);
    }

    public function isSameTaxRate(Price $other): bool
    {
        return $this->taxRate === $other->taxRate;
    }

    private function assertSameTaxRate(Price $other)
    {
        if (!$this->isSameTaxRate($other)) {
            throw new \InvalidArgumentException('Different tax rates');
        }
    }

    public function equals(Price $other): bool
    {
        return ($this->isSameTaxRate($other) && $other->net === $this->net);
    }

    public function compare(Price $other): int
    {
        $this->assertSameTaxRate($other);
        if ($this->net < $other->net) {
            return -1;
        } elseif ($this->net === $other->net) {
            return 0;
        } else {
            return 1;
        }
    }

    public function greaterThan(Price $other): bool
    {
        return 1 === $this->compare($other);
    }

    public function lessThan(Price $other): bool
    {
        return -1 === $this->compare($other);
    }

    public function add(Price $addend): Price
    {
        $this->assertSameTaxRate($addend);

        return new self($this->net + $addend->net, $this->taxRate);
    }

    public function subtract(Price $subtrahend): Price
    {
        $this->assertSameTaxRate($subtrahend);

        return new self($this->net - $subtrahend->net, $this->taxRate);
    }

    public function multiply($multiple): Price
    {
        return new self((int) ceil($this->net * $multiple), $this->taxRate);
    }

    private static function convertPercentageToFloat(int $percentage): float
    {
        return (float) ($percentage / 100);
    }

    private function calculateTax(): int
    {
        return $this->multiply(self::convertPercentageToFloat($this->taxRate))->net;
    }
}