<?php


namespace ConferenceTools\Attendance\Controller;


use Carnage\Phactor\ReadModel\Repository;
use Carnage\Phactor\Zend\ControllerPlugin\MessageBus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * @method MessageBus messageBus()
 * @method Repository repository(string $className)
 * @method FlashMessenger flashMessenger()
 */
abstract class AppController extends AbstractActionController
{

}