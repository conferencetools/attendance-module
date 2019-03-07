<?php

namespace ConferenceTools\AttendanceTest\Domain\Prizes;

use Codeception\Test\Unit;
use ConferenceTools\Attendance\Domain\Prizes\Command\ChooseWinner;
use ConferenceTools\Attendance\Domain\Prizes\Command\GiveawayPrize;
use ConferenceTools\Attendance\Domain\Prizes\Command\ReplaceWinner;
use ConferenceTools\Attendance\Domain\Prizes\Command\WinnerIs;
use ConferenceTools\Attendance\Domain\Prizes\Event\PrizeGiveaway;
use ConferenceTools\Attendance\Domain\Prizes\Event\WinnerChosen;
use ConferenceTools\Attendance\Domain\Prizes\Prize;
use Phactor\Test\ActorHelper;

class PrizeTest extends Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(Prize::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testPrizeGiveaway()
    {
        $this->helper->when(new GiveawayPrize('Prize'));
        $this->helper->expect(new PrizeGiveaway($this->actorId, 'Prize'));
    }

    public function testWinnerIs()
    {
        srand(0);
        $this->helper->given([new PrizeGiveaway($this->actorId, 'Prize')]);
        $this->helper->when(new WinnerIs($this->actorId, 'd1', 'd2', 'd3', 'd4', 'd5'));
        $this->helper->expect(new WinnerChosen($this->actorId, 'd1'));
    }

    public function testChooseWinner()
    {
        srand(0);
        $this->helper->given([new PrizeGiveaway($this->actorId, 'Prize')]);
        $this->helper->when(new ChooseWinner($this->actorId, 'd1', 'd2', 'd3', 'd4', 'd5'));
        $this->helper->expect(new WinnerChosen($this->actorId, 'd5'));
    }

    public function testReplaceWinner()
    {
        srand(0);
        $this->helper->given([
            new PrizeGiveaway($this->actorId, 'Prize'),
            new ChooseWinner($this->actorId, 'd1', 'd2', 'd3', 'd4', 'd5'),
            new WinnerChosen($this->actorId, 'd5')
        ]);
        $this->helper->when(new ReplaceWinner($this->actorId));
        $this->helper->expect(new WinnerChosen($this->actorId, 'd4'));
    }

    //@TODO test scenarios with winners chosen multiple times
}
