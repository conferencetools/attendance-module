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
     * @var Money
     * @ORM\Embedded(class="ConferenceTools\Attendance\Domain\Ticketing\Money")
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Money")
     */
    private $net;

    /**
     * @Jms\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $taxRate;

    private function __construct(Money $net, int $taxRate)
    {
        $this->net = $net;
        $this->taxRate = $taxRate;
    }

    public function getNet(): Money
    {
        return $this->net;
    }

    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    public function getGross(): Money
    {
        return $this->calculateGross($this->net);
    }

    public function getTax(): Money
    {
        return $this->calculateTaxFromNet($this->net);
    }

    public static function fromNetCost(Money $net, int $taxRate)
    {
        return new static($net, $taxRate);
    }

    public static function fromGrossCost(Money $gross, int $taxRate)
    {
        $inverseTaxRate = 1 / (1 + self::convertPercentageToFloat($taxRate));

        $net = $gross->subtract($gross->multiply($inverseTaxRate));

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
        return ($this->isSameTaxRate($other) && $other->net->equals($this->net));
    }

    public function compare(Price $other): int
    {
        $this->assertSameTaxRate($other);
        if ($this->net->lessThan($other->net)) {
            return -1;
        } elseif ($this->net->equals($other->net)) {
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

        return new self($this->net->add($addend->net), $this->taxRate);
    }

    public function subtract(Price $subtrahend): Price
    {
        $this->assertSameTaxRate($subtrahend);

        return new self($this->net->subtract($subtrahend->net), $this->taxRate);
    }

    public function multiply($multiple): Price
    {
        return new self($this->net->multiply($multiple), $this->taxRate);
    }

    private static function convertPercentageToFloat(int $percentage): float
    {
        return (float) ($percentage / 100);
    }

    private function calculateTaxFromNet(Money $net): Money
    {
        return $net->multiply(self::convertPercentageToFloat($this->taxRate));
    }

    private function calculateGross(Money $net): Money
    {
        return $net->add($this->calculateTaxFromNet($net));
    }
}