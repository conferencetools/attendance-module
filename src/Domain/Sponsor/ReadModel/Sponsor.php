<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\ReadModel;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Sponsor
{
    /** @ORM\Column(type="string") @ORM\Id() */
    private $id;
    /** @ORM\Column(type="string") */
    private $name;
    /** @ORM\Column(type="string") */
    private $user;

    public function __construct(string $id, string $name, string $user)
    {
        $this->id = $id;
        $this->name = $name;
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
