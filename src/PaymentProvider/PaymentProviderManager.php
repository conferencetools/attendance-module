<?php

namespace ConferenceTools\Attendance\PaymentProvider;

use Zend\ServiceManager\AbstractPluginManager;

class PaymentProviderManager extends AbstractPluginManager
{
    protected $instanceOf = PaymentProvider::class;
}
