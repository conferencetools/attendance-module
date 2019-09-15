<?php

namespace ConferenceTools\Attendance\Test\Functional;

use Codeception\Event\SuiteEvent;
use Codeception\Event\TestEvent;
use \Codeception\Events;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class DBManager extends \Codeception\Extension
{
    public static $events = array(
        Events::SUITE_AFTER  => 'afterSuite',
        Events::SUITE_BEFORE => 'beforeSuite',
        Events::TEST_BEFORE => 'beforeTest',
    );

    public function afterSuite(SuiteEvent $e)
    {
        if (file_exists(self::getDataDir(). 'template.sqlite')) {
            unlink(self::getDataDir(). 'template.sqlite');
        }

        if (file_exists(self::getDataDir(). 'db.sqlite')) {
            unlink(self::getDataDir(). 'db.sqlite');
        }
    }

    public function beforeSuite(SuiteEvent $e)
    {
        if (!file_exists(self::getDataDir(). 'template.sqlite')) {
            $zend = $this->getModule('ZF2');
            $application = $zend->grabServiceFromContainer('doctrine.cli');
            /** @var Symfony\Component\Console\Application $application */
            $application->setAutoExit(false);
            $application->run(new ArrayInput(['command' => 'orm:schema-tool:create']), new NullOutput());
            rename(self::getDataDir(). 'db.sqlite', self::getDataDir(). 'template.sqlite');
        }
    }

    public function beforeTest(TestEvent $e)
    {
        if (file_exists(self::getDataDir(). 'db.sqlite')) {
            unlink(self::getDataDir(). 'db.sqlite');
        }

        copy(self::getDataDir(). 'template.sqlite', self::getDataDir(). 'db.sqlite');
    }
}