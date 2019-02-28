<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\DelegateSearchForm;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class CheckinController extends AppController
{
    public function indexAction()
    {
        $form = $this->form(DelegateSearchForm::class);
        $results = [];

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                //$data['purchaserEmail'] = $data['email'];

                $expressions = [];

                foreach ($data as $key => $search) {
                    if (empty($search)) {
                        continue;
                    }

                    $expressions[] = Criteria::expr()->contains($key, $search);
                }

                $criteria = Criteria::create();
                $criteria->where(Criteria::expr()->eq('isPaid', true));
                switch (count($expressions)) {
                    case 0:
                        break;
                    case 1:
                        $criteria->andWhere($expressions[0]);
                        break;
                    default:
                        $criteria->andWhere(Criteria::expr()->orX(...$expressions));
                        break;
                }

                $results = $this->repository(Delegate::class)->matching($criteria);
            }
        }

        return new ViewModel(['form' => $form, 'results' => $results, 'tickets' => $this->getTickets()]);
    }
}