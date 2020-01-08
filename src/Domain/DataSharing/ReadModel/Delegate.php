<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\ReadModel;

use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="DelegateList_Delegate")
 */
class Delegate
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList", inversedBy="delegates")
     * @ORM\JoinColumn(name="delegate_list_id", referencedColumnName="id")
     */
    private $delegateList;
    /** @ORM\Column(type="string") @ORM\Id() */
    private $delegateId;
    /** @ORM\Column(type="json_array") */
    private $consents = [];

    public function __construct(DelegateList $delegateList, string $delegateId, OptInConsent ...$consents)
    {
        $this->delegateList = $delegateList;
        $this->delegateId = $delegateId;
        foreach ($consents as $consent) {
            $this->consents[$consent->getHandle()] = $consent->isConsentGranted();
        }
    }

    public function getDelegateList(): DelegateList
    {
        return $this->delegateList;
    }

    public function getDelegateId(): string
    {
        return $this->delegateId;
    }

    public function getConsents(): array
    {
        return $this->consents;
    }

    public function includeOnList(): bool
    {
        return array_search(true, $this->consents) === false ? false : true;
    }

    public function updateConsents(OptInConsent ...$consents): void
    {
        $this->consents = [];
        foreach ($consents as $consent) {
            $this->consents[$consent->getHandle()] = $consent->isConsentGranted();
        }
    }
}