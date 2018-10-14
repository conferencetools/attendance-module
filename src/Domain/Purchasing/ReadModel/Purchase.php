<?php

namespace ConferenceTools\Attendance\Domain\Purchasing\ReadModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Purchase
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     */
    private $tickets;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function addTickets(string $ticketId, int $quantity)
    {
        $this->tickets[$ticketId] = $quantity;
    }

    public function getTickets()
    {
        return $this->tickets;
    }

    public function getMaxDelegates()
    {
        $delegates = 0;
        foreach ($this->getTickets() as $ticketId => $quantity) {
            $delegates += $quantity;
        }

        return $delegates;
    }
}