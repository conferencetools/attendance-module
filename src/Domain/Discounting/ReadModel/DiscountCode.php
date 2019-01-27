<?php

namespace ConferenceTools\Attendance\Domain\Discounting\ReadModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DiscountCode
{
    /**
     * @ORM\ManyToOne(targetEntity="ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountType", inversedBy="codes")
     * @ORM\JoinColumn(name="discountCodeId", referencedColumnName="id")
     */
    private $discountType;
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $code;

    public function __construct(DiscountType $discountType, string $code)
    {
        $this->discountType = $discountType;
        $this->code = $code;
    }

    public function getDiscountType(): DiscountType
    {
        return $this->discountType;
    }

}