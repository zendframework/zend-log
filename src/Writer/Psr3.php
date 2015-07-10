<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer;

use Psr\Log\LogLevel;
use Psr\Log\LoggerAwareTrait as Psr3LoggerAwareTrait;
use Psr\Log\LoggerInterface as Psr3LoggerInterface;
use Psr\Log\NullLogger;
use Zend\Log\Logger;

class Psr3 extends AbstractWriter
{
    use Psr3LoggerAwareTrait;

    /**
     * Map priority to PSR-3 LogLevels
     *
     * @var array
     */
    protected $psr3PriorityMap = [
        Logger::EMERG  => LogLevel::EMERGENCY,
        Logger::ALERT  => LogLevel::ALERT,
        Logger::CRIT   => LogLevel::CRITICAL,
        Logger::ERR    => LogLevel::ERROR,
        Logger::WARN   => LogLevel::WARNING,
        Logger::NOTICE => LogLevel::NOTICE,
        Logger::INFO   => LogLevel::INFO,
        Logger::DEBUG  => LogLevel::DEBUG,
    ];

    protected $defaultLogLevel = LogLevel::WARNING;

    /**
     * Constructor
     *
     * Set options for a writer. Accepted options are:
     * - filters: array of filters to add to this filter
     * - formatter: formatter for this writer
     * - logger: Psr\Log\LoggerInterface implementation
     *
     * @param  array|Traversable|LoggerInterface $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Psr3LoggerInterface) {
            $this->setLogger($options);
        }
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (is_array($options) && isset($options['logger'])) {
            $this->setLogger($options['logger']);
        }
        parent::__construct($options);

        if (null === $this->logger) {
            $this->logger = new NullLogger;
        }
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        $priority = $event['priority'];
        $message  = $event['message'];
        $context  = $event['extra'];

        if (isset($this->psr3PriorityMap[$priority])) {
            $level = $this->psr3PriorityMap[$priority];
        } else {
            $level = $this->defaultLogLevel;
        }

        $this->logger->log($level, $message, $context);
    }
}
