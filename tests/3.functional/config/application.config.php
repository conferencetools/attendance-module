<?php
/**
 * If you need an environment-specific system or application configuration,
 * there is an example in the documentation
 * @see https://docs.zendframework.com/tutorials/advanced-config/#environment-specific-system-configuration
 * @see https://docs.zendframework.com/tutorials/advanced-config/#environment-specific-application-configuration
 */
return [
    // Retrieve list of modules used in this application.
    'modules' => [
        'Zend\Mail',
        'Zend\Mvc\Plugin\FlashMessenger',
        'Zend\Session',
        'Zend\Navigation',
        'Zend\Serializer',
        'Zend\Log',
        'Zend\Router',
        'Zend\Validator',
        'Zend\Form',
        'TwbBundle',
        //'Zend\I18n',
        'DoctrineModule',
        'DoctrineORMModule',
        'Carnage\ZendfonyCli',
        'Phactor\Zend',
        'Phactor\Doctrine\Zend',
        'ConferenceTools\Admin',
        'ConferenceTools\Attendance',
    ],

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        'module_paths' => [
            __DIR__ . '/../src',
            './vendor',
        ],
        'config_glob_paths' => [
            realpath(__DIR__) . sprintf('/{,*.}{global,test,local}.php')
        ],
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
        'check_dependencies' => true,
    ],

    // Used to create an own service manager. May contain one or more child arrays.
    // 'service_listener_options' => [
    //     [
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ],
    // ],

    // Initial configuration with which to seed the ServiceManager.
    // Should be compatible with Zend\ServiceManager\Config.
    // 'service_manager' => [],
];
