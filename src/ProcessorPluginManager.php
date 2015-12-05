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

    /**
     * {@inheritdoc}
     */
    public function validate($plugin)
    {
        if ($plugin instanceof Processor\ProcessorInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Processor\ProcessorInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
