<?php


namespace ConferenceTools\Attendance\Domain\Discounting;


use Phactor\Actor\AbstractActor;

class Discount extends AbstractActor
{
    private $name;
    private $appliesTo;
    private $discountType;
    private $availabilityDates;
    private $codes;

    public function handleCreateDiscount()
    {
        //raise DiscountCreated
        //raise CheckExpiry
    }

    public function handleAddCode()
    {
        //raise DiscountCodeCreated
    }

    public function handleCheckExpiry()
    {
        //raise DiscountAvailable x codes
        //raise DiscountExpired x codes
    }

}