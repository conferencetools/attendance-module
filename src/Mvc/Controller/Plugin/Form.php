<?php


namespace ConferenceTools\Attendance\Mvc\Controller\Plugin;

use Zend\Form\Form as ZendForm;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\PluginManagerInterface;

class Form extends AbstractPlugin
{
    /**
     * @var AbstractPluginManager
     */
    private $formElementManager;

    public function __construct(PluginManagerInterface $formElementManager)
    {
        $this->formElementManager = $formElementManager;
    }

    public function __invoke(string $name, array $options = []): ZendForm
    {
        return $this->formElementManager->get($name, $options);
    }
}