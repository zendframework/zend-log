<?php
/**
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\Log\Filter\LogFilterProviderInterface;
use Zend\Log\Formatter\LogFormatterProviderInterface;
use Zend\Log\Processor\LogProcessorProviderInterface;
use Zend\Log\Writer\LogWriterProviderInterface;

class Module
{
    /**
     * Return default zend-log configuration for zend-mvc applications.
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();

        return [
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }

    /**
     * Register specifications for all zend-log plugin managers with the ServiceListener.
     *
     * @param \Zend\ModuleManager\ModuleEvent
     * @return void
     */
    public function init($event)
    {
        $container = $event->getParam('ServiceManager');
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            'LogProcessorManager',
            'log_processors',
            LogProcessorProviderInterface::class,
            'getLogProcessorConfig'
        );

        $serviceListener->addServiceManager(
            'LogWriterManager',
            'log_writers',
            LogWriterProviderInterface::class,
            'getLogWriterConfig'
        );

        $serviceListener->addServiceManager(
            'LogFilterManager',
            'log_filters',
            LogFilterProviderInterface::class,
            'getLogFilterConfig'
        );

        $serviceListener->addServiceManager(
            'LogFormatterManager',
            'log_formatters',
            LogFormatterProviderInterface::class,
            'getLogFormatterConfig'
        );
    }
}
