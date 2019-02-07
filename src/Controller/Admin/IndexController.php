<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use Zend\View\Model\ViewModel;

class IndexController extends AppController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}