<?php

/**
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Log\Processor;

interface LogProcessorProviderInterface
{
    /**
     * Provide plugin manager configuration for log processors.
     *
     * @return array
     */
    public function getLogProcessorConfig();
}
