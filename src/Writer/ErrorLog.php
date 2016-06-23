<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer;

use Traversable;
use Zend\Log\Formatter\Simple as SimpleFormatter;
use Zend\Validator\EmailAddress as EmailAddressValidator;

class ErrorLog extends AbstractWriter
{
    /**
     * @var int
     */
    protected $mode;

    /**
     * @var string|null
     */
    protected $destination;


    /**
     * ErrorLog constructor.
     * @param array|null|\Traversable $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }

        if (! is_array($options)) {
            $options = [];
        }

        parent::__construct($options);

        $mode = isset($options['mode']) ? (int) $options['mode'] : 0;
        if (0 <= $mode && $mode <= 4) {
            $this->mode = $mode;
        }

        $destination = isset($options['destination']) ? (string) $options['destination'] : null;
        $is_stream = $this->mode == 3 && $this->isStream($destination) ;


        $validator = new EmailAddressValidator();
        $validator->setOptions(['domain' => false]);
        $is_email = $this->mode == 1 && $validator->isValid($destination);

        if ($is_stream || $is_email) {
            $this->destination = $destination;
        }

        if ($this->formatter === null) {
            $this->formatter = new SimpleFormatter();
        }
    }


    /**
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    protected function doWrite(array $event)
    {
        $event = $this->formatter->format($event);

        error_log($event, $this->mode, $this->destination);
    }


    /**
     * Checks if a string contains a valid file name
     *
     * @param string $name file name
     * @return boolean
     */
    protected function isStream($name)
    {
        @$f = fopen($name, 'r');
        if (!$f) {
            return false;
        }

        $result = is_resource($f);
        fclose($f);

        return $result;
    }
}
