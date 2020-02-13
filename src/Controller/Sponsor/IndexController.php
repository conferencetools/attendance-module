<?php

namespace ConferenceTools\Attendance\Controller\Sponsor;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList;
use ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class IndexController extends AppController
{
    public function indexAction()
    {
        $sponsor = $this->currentSponsor();
        $viewData = ['sponsor' => $sponsor];

        if ($sponsor->getDelegateListId() !== null) {
            $viewData['delegateList'] = $this->repository(DelegateList::class)->get($sponsor->getDelegateListId());
        }

        return new ViewModel($viewData);
    }
}