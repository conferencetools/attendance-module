<?php

namespace ConferenceTools\Attendance;

use Zend\EventManager\EventManager;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        /** @var EventManager $eventManager */
        $eventManager = $event->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $routeMatch = $e->getRouteMatch();
            $layout = $routeMatch->getParam('layout', '');
            if (!empty($layout)) {
                $e->getTarget()->layout($layout);
            }
        });
    }
}