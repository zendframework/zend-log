<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
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

        // The following are for backwards compatibility only; users
        // should update their code to use the noop writer instead.
        'null'              => Writer\Noop::class,
        Writer\Null::class  => Writer\Noop::class,
        'zendlogwriternull' => Writer\Noop::class,

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
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'zendlogwriterchromephp'      => InvokableFactory::class,
        'zendlogwriterdb'             => InvokableFactory::class,
        'zendlogwriterfirephp'        => InvokableFactory::class,
        'zendlogwritermail'           => InvokableFactory::class,
        'zendlogwritermock'           => InvokableFactory::class,
        'zendlogwriternoop'           => InvokableFactory::class,
        'zendlogwriterpsr'            => InvokableFactory::class,
        'zendlogwriterstream'         => InvokableFactory::class,
        'zendlogwritersyslog'         => InvokableFactory::class,
        'zendlogwriterfingerscrossed' => InvokableFactory::class,
        'zendlogwriterzendmonitor'    => InvokableFactory::class,
    ];

    protected $instanceOf = Writer\WriterInterface::class;

    /**
     * Allow many writers of the same type (v2)
     * @param bool
     */
    protected $shareByDefault = false;

    /**
     * Allow many writers of the same type (v3)
     * @param bool
     */
    protected $sharedByDefault = false;

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
     * @param mixed $plugin
     * @throws InvalidServiceException
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Plugin of type %s is invalid; must implement %s\Writer\WriterInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__
            ));
        }
    }
}
