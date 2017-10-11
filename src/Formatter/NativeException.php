<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Formatter;

// @codingStandardsIgnoreStart
/**
 * Format an Exception in a similar way PHP does by default when an exception bubbles to the top
 *
 * This is a snippet of the native PHP format which it resembles
 *
 * [Wed Oct 11 15:45:18 2017] PHP Fatal error:  Uncaught RuntimeException: error message in /module/MyModule/src/Module.php:11
 * Stack trace:
 * #0 /vendor/zendframework/zend-modulemanager/src/Listener/ConfigListener.php(124): MyModule\Module->getConfig()
 * #1 [internal function]: Zend\ModuleManager\Listener\ConfigListener->onLoadModule(Object(Zend\ModuleManager\ModuleEvent))
 * #2 /vendor/zendframework/zend-eventmanager/src/EventManager.php(490): call_user_func(Array, Object(Zend\ModuleManager\ModuleEvent))
 * #3 /vendor/zendframework/zend-eventmanager/src/EventManager.php(251): Zend\EventManager\EventManager->triggerListeners('loadModule', Object(Zend\ModuleManager\ModuleEvent))
 * #4 /vendor/zendframework/zend-modulemanager/src/ModuleManager.php(181): Zend\EventManager\EventManager->triggerEvent(Object(Zend\ModuleManager\ModuleEvent))
 */
// @codingStandardsIgnoreEnd
class NativeException implements FormatterInterface
{
    /**
     * Format specifier for DateTime objects in event data
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;

    /**
     * Transform an event created from a PHP exception to a log entry in human-readable format
     *
     * @param mixed[] $event
     * @return string
     */
    public function format($event): string
    {
        if ($event['timestamp'] instanceof \DateTimeInterface) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }

        $output = '[' . $event['timestamp'] . '] ' . $event['priorityName'] . ' ('
            . $event['priority'] . ') ' . $event['message'] .' in '
            . $event['extra']['file'] . ':' . $event['extra']['line']
            . "\n";

        if (! empty($event['extra']['trace'])) {
            $output .= "Stack trace:\n";
            foreach ($event['extra']['trace'] as $i => $traceLine) {
                $output .= "#$i " . $this->formatTraceLine($traceLine) . "\n";
            }
        }

        return trim($output);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }

    /**
     * Transform an element of the array Exception::getTrace
     * to a string of a single line
     *
     * @param mixed[] $trace element of Exception::getTrace
     * @return string of a single line
     */
    private function formatTraceLine(array $trace): string
    {
        $arguments = $this->formatArguments($trace['args']);
        $output = '';

        if (isset($trace['file'])) {
            $output .= $trace['file'];

            if (isset($trace['line'])) {
                $output .= '(' . $trace['line'] . ')';
            }

            $output .= ': ';
        }

        $output .= $trace['class'] ?? '';
        $output .= $trace['type'] ?? '';
        $output .= $trace['function'] ?? '';

        $arguments = $this->formatArguments($trace['args'] ?? []);
        $output .= '(' . $arguments . ')';

        return $output;
    }

    /**
     * Convert function arguments of any type to a short readable string
     *
     * @param mixed[] $arguments
     * @return string a summary of the list of arguments
     */
    private function formatArguments(array $arguments): string
    {
        return implode(', ', array_map([$this, 'formatArgument'], $arguments));
    }

    /**
     * Summarize a variable
     *
     * @param mixed $argument anything at all
     * @return string a summary of the argument
     */
    private function formatArgument($argument): string
    {
        if (is_int($argument) || is_float($argument)) {
            return $argument;
        }

        if (is_string($argument)) {
            if (strlen($argument) < 80) {
                $truncated = $argument;
            } else {
                $truncated = substr($argument, 0, 30) . '...' . substr($argument, -30, 30);
            }

            return "'$truncated'";
        }

        if ($argument === false) {
            return 'false';
        }

        if ($argument === true) {
            return 'true';
        }

        if (is_array($argument)) {
            return 'Array(' . count($argument) . ')';
        }

        if (is_object($argument)) {
            return 'Object(' . get_class($argument) . ')';
        }

        return gettype($argument);
    }
}
