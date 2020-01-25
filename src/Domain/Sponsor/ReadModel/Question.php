<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\ReadModel;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Question
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor", inversedBy="questions")
     * @ORM\JoinColumn(name="sponsor_id", referencedColumnName="id")
     */
    private $sponsor;
    /** @ORM\Column(type="string") */
    private $question;
    /** @ORM\Column(type="string") @ORM\Id() */
    private $handle;

    public function __construct(Sponsor $sponsor, OptIn $optIn)
    {
        $this->sponsor = $sponsor;
        $this->question = $optIn->getQuestion();
        $this->handle = $optIn->getHandle();
    }

    public function getSponsor(): Sponsor
    {
        return $this->sponsor;
    }

    public function getOptIn(): OptIn
    {
        return new OptIn($this->handle, $this->question);
    }
}