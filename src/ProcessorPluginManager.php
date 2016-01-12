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
 * Plugin manager for log processors.
 */
class ProcessorPluginManager extends AbstractPluginManager
{
    protected $aliases = [
        'backtrace'      => Processor\Backtrace::class,
        'psrplaceholder' => Processor\PsrPlaceHolder::class,
        'referenceid'    => Processor\ReferenceId::class,
        'requestid'      => Processor\RequestId::class
    ];

    protected $factories = [
        Processor\Backtrace::class      => InvokableFactory::class,
        Processor\PsrPlaceHolder::class => InvokableFactory::class,
        Processor\ReferenceId::class    => InvokableFactory::class,
        Processor\RequestId::class      => InvokableFactory::class
    ];

    protected $instanceOf = Processor\ProcessorInterface::class;

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
