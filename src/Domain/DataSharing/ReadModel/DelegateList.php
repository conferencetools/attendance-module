<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\ReadModel;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class DelegateList
{
    /** @ORM\Column(type="string") @ORM\Id() */
    private $id;
    /** @ORM\Column(type="string") */
    private $owner;
    /** @ORM\Column(type="datetime", nullable=true) */
    private $availableTime;
    /** @ORM\Column(type="datetime", nullable=true) */
    private $lastCollectionTime;
    /** @ORM\Column(type="boolean") */
    private $listAvailable = false;
    /** @ORM\Column(type="boolean") */
    private $listTerminated = false;
    /**
     * @var ArrayCollection|Delegate[]
     * @ORM\OneToMany(
     *     targetEntity="ConferenceTools\Attendance\Domain\DataSharing\ReadModel\Delegate",
     *     mappedBy="delegateList",
     *     indexBy="delegateId",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    private $delegates;
    /** @ORM\Column(type="json_array") */
    private $optIns = [];

    public function __construct(string $id, string $owner, OptIn ... $optIns)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->delegates = new ArrayCollection();
        foreach ($optIns as $optIn) {
            $this->optIns[$optIn->getHandle()] = $optIn->getQuestion();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getAvailableTime(): ?\DateTime
    {
        return $this->availableTime;
    }

    public function getLastCollectionTime(): ?\DateTime
    {
        return $this->lastCollectionTime;
    }

    /** @return OptIn[] */
    public function getOptIns(): array
    {
        $callback = function ($k, $v) {
            return new OptIn($k, $v);
        };
        return array_map($callback, array_keys($this->optIns), array_values($this->optIns));
    }

    public function isListAvailable(): bool
    {
        return $this->listAvailable;
    }

    public function isListTerminated(): bool
    {
        return $this->listTerminated;
    }

    public function setAvailableTime(\DateTime $availableTime): void
    {
        $this->availableTime = $availableTime;
    }

    public function setLastCollectionTime(\DateTime $lastCollectionTime): void
    {
        $this->lastCollectionTime = $lastCollectionTime;
    }

    public function makeAvailable(): void
    {
        $this->listAvailable = true;
    }

    public function terminate(): void
    {
        $this->listTerminated = true;
    }

    public function addDelegate(string $delegateId, OptInConsent ...$consents)
    {
        $this->delegates->set($delegateId, new Delegate($this, $delegateId, ...$consents));
    }

    public function updateDelegate(string $delegateId, OptInConsent ...$consents)
    {
        $delegate = $this->delegates->get($delegateId);
        if (!($delegate instanceof Delegate)) {
            $this->addDelegate($delegateId, ...$consents);
        }

        $delegate->updateConsents(...$consents);
    }
}
