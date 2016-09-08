<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Formatter;

use DateTime;
use Exception;

class Json implements FormatterInterface
{


    /**
     * Format specifier for DateTime objects in event data (default: ISO 8601)
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;


    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }

        return @json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string)$dateTimeFormat;
        return $this;
    }

    /**
     * Normalizes given $data.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function normalize($data, $depth = 0)
    {
        if ($depth > 9) {
            return 'Over 9 levels deep, aborting normalization';
        }
        if (is_array($data) || $data instanceof \Traversable) {
            $normalized = [];
            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ >= 1000) {
                    $normalized['...'] = 'Over 1000 items, aborting normalization';
                    break;
                }
                $normalized[$key] = $this->normalize($value, $depth + 1);
            }
            return $normalized;
        }
        if ($data instanceof Exception) {
            return $this->normalizeException($data);
        }
        return $data;
    }

    /**
     * Normalizes given exception with its own stack trace
     *
     * @param \Throwable $e
     *
     * @return array
     */
    protected function normalizeException(\Throwable $e)
    {
        $data = [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ];
        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data['trace'][] = $frame['file'] . ':' . $frame['line'];
            } elseif (isset($frame['function']) && $frame['function'] === '{closure}') {
                // We should again normalize the frames, because it might contain invalid items
                $data['trace'][] = $frame['function'];
            } else {
                // We should again normalize the frames, because it might contain invalid items
                $data['trace'][] = $this->normalize($frame);
            }
        }
        if ($previous = $e->getPrevious()) {
            $data['previous'] = $this->normalizeException($previous);
        }
        return $data;
    }
}
