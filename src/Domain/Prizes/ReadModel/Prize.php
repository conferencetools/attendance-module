<?php


namespace ConferenceTools\Attendance\Domain\Prizes\ReadModel;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Prize
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;
    /** @ORM\Column(type="string") */
    private $name;
    /** @ORM\Column(type="string") */
    private $winner = '';
    /** @ORM\Column(type="boolean") */
    private $collected = false;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWinner(): string
    {
        return $this->winner;
    }

    public function hasBeenCollected(): bool
    {
        return $this->collected;
    }

    public function winnerChosen(string $winner): void
    {
        $this->winner = $winner;
    }
}