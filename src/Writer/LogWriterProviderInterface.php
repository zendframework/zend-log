<?php

/**
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 *
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Log\Writer;

interface LogWriterProviderInterface
{
    /**
     * Provide plugin manager configuration for log writers.
     *
     * @return array
     */
    public function getLogWriterConfig();
}
