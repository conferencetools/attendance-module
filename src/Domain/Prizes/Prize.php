<?php

namespace ConferenceTools\Attendance\Domain\Prizes;


use ConferenceTools\Attendance\Domain\Prizes\Command\ChooseWinner;
use ConferenceTools\Attendance\Domain\Prizes\Command\GiveawayPrize;
use ConferenceTools\Attendance\Domain\Prizes\Command\ReplaceWinner;
use ConferenceTools\Attendance\Domain\Prizes\Event\PrizeGiveaway;
use ConferenceTools\Attendance\Domain\Prizes\Event\WinnerChosen;
use ConferenceTools\Attendance\Domain\Prizes\Command\WinnerIs;
use Phactor\Actor\AbstractActor;

class Prize extends AbstractActor
{
    private $name;
    private $entrants;

    protected function handleGiveawayPrize(GiveawayPrize $command)
    {
        $this->fire(new PrizeGiveaway($this->id(), $command->getName()));
    }

    protected function applyPrizeGiveaway(PrizeGiveaway $event)
    {
        $this->name = $event->getName();
    }

    protected function handleChooseWinner(ChooseWinner $command)
    {
        $entrants = $command->getEntrants();
        $winner = array_pop($entrants);
        $this->fire(new WinnerChosen($this->id(), $winner));
    }

    protected function handleWinnerIs(WinnerIs $command)
    {
        $this->fire(new WinnerChosen($this->id(), $command->getWinner()));
    }

    protected function applyWinnerIs(WinnerIs $command)
    {
        $this->entrants = $command->getEntrants();
    }

    protected function applyChooseWinner(ChooseWinner $command)
    {
        $entrants = $command->getEntrants();
        array_pop($entrants);
        $this->entrants = $entrants;
    }

    protected function applyWinnerChosen(WinnerChosen $event)
    {
        $this->winner = $event->getWinner();
    }

    protected function handleReplaceWinner(ReplaceWinner $command)
    {
        $entrants = $this->entrants;
        $winner = array_pop($entrants);
        $this->fire(new WinnerChosen($this->id(), $winner));
    }

    protected function applyReplaceWinner(ReplaceWinner $command)
    {
        array_pop($this->entrants);
    }

    // Collected
}