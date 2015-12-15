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
use Zend\ServiceManager\Factory\InvokableFactory;

class FilterPluginManager extends AbstractPluginManager
{
    protected $aliases = [
        'mock'           => Filter\Mock::class,
        'priority'       => Filter\Priority::class,
        'regex'          => Filter\Regex::class,
        'suppress'       => Filter\SuppressFilter::class,
        'suppressfilter' => Filter\SuppressFilter::class,
        'validator'      => Filter\Validator::class,
    ];

    protected $factories = [
        Filter\Mock::class           => InvokableFactory::class,
        Filter\Priority::class       => InvokableFactory::class,
        Filter\Regex::class          => InvokableFactory::class,
        Filter\SuppressFilter::class => InvokableFactory::class,
        Filter\Validator::class      => InvokableFactory::class,
    ];

    protected $instanceOf = Filter\FilterInterface::class;
}
