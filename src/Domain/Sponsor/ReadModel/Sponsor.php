<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\ReadModel;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\Delegate;
use Doctrine\Common\Collections\ArrayCollection;
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
    /** @ORM\Column(type="string", nullable=true) */
    private $delegateListId;
    /**
     * @var ArrayCollection|Question[]
     * @ORM\OneToMany(
     *     targetEntity="ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Question",
     *     mappedBy="sponsor",
     *     indexBy="handle",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private $questions;

    public function __construct(string $id, string $name, string $user)
    {
        $this->id = $id;
        $this->name = $name;
        $this->user = $user;
        $this->questions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDelegateListId(): ?string
    {
        return $this->delegateListId;
    }

    public function hasCreatedList(): bool
    {
        return $this->delegateListId !== null;
    }

    /** @return OptIn[] */
    public function getQuestions(): iterable
    {
        return $this->questions->map(static function (Question $q) {
            return $q->getOptIn();
        })->toArray();
    }

    public function addQuestion(OptIn $optIn): void
    {
        $entity = new Question($this, $optIn);
        $this->questions->add($entity);
    }

    public function deleteQuestion(string $handle): void
    {
        $question = $this->questions[$handle];
        $this->questions->removeElement($question);
    }

    public function delegateListCreated(string $delegateListId)
    {
        // prevent loss of access to an existing list, should somehow a second list be created for a sponsor
        if ($this->delegateListId === null) {
            $this->delegateListId = $delegateListId;
        }
    }
}
