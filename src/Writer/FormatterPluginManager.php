<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer;

use Zend\Log\Exception;
use Zend\Log\Formatter;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\InvokableFactory;

class FormatterPluginManager extends AbstractPluginManager
{
    protected $aliases = [
        'base'             => Formatter\Base::class,
        'simple'           => Formatter\Simple::class,
        'xml'              => Formatter\Xml::class,
        'db'               => Formatter\Db::class,
        'errorhandler'     => Formatter\ErrorHandler::class,
        'exceptionhandler' => Formatter\ExceptionHandler::class,
    ];

    protected $factories = [
        Formatter\Base::class             => InvokableFactory::class,
        Formatter\Simple::class           => InvokableFactory::class,
        Formatter\Xml::class              => InvokableFactory::class,
        Formatter\Db::class               => InvokableFactory::class,
        Formatter\ErrorHandler::class     => InvokableFactory::class,
        Formatter\ExceptionHandler::class => InvokableFactory::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function validate($plugin)
    {
        if ($plugin instanceof Formatter\FormatterInterface) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Formatter\FormatterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
