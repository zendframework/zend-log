<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Plugin manager for log writers.
 */
class WriterPluginManager extends AbstractPluginManager
{
    protected $aliases = [
        'null'             => 'noop',
        Writer\Null::class => 'noop',

        'chromephp'      => Writer\ChromePhp::class,
        'db'             => Writer\Db::class,
        'fingerscrossed' => Writer\FingersCrossed::class,
        'firephp'        => Writer\FirePhp::class,
        'mail'           => Writer\Mail::class,
        'mock'           => Writer\Mock::class,
        'noop'           => Writer\Noop::class,
        'psr'            => Writer\Psr::class,
        'stream'         => Writer\Stream::class,
        'syslog'         => Writer\Syslog::class,
        'zendmonitor'    => Writer\ZendMonitor::class,
    ];

    protected $factories = [
        Writer\ChromePhp::class      => InvokableFactory::class,
        Writer\Db::class             => InvokableFactory::class,
        Writer\FirePhp::class        => InvokableFactory::class,
        Writer\Mail::class           => InvokableFactory::class,
        Writer\Mock::class           => InvokableFactory::class,
        Writer\Noop::class           => InvokableFactory::class,
        Writer\Psr::class            => InvokableFactory::class,
        Writer\Stream::class         => InvokableFactory::class,
        Writer\Syslog::class         => InvokableFactory::class,
        Writer\FingersCrossed::class => InvokableFactory::class,
        Writer\ZendMonitor::class    => InvokableFactory::class,
    ];

    protected $instanceOf = Writer\WriterInterface::class;

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param mixed $instance
     * @throws InvalidServiceException
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $instance
     * @throws InvalidServiceException
     */
    public function validatePlugin($instance)
    {
        $this->validate($instance);
    }
}
