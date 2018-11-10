<?php


namespace ConferenceTools\Attendance\Controller;


use Phactor\ReadModel\Repository;
use Phactor\Zend\ControllerPlugin\MessageBus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * @method MessageBus messageBus()
 * @method Repository repository(string $className)
 * @method FlashMessenger flashMessenger()
 * @method \Zend\Form\Form form(string $name, array $options = [])
 */
abstract class AppController extends AbstractActionController
{

}