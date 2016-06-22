<?php
namespace Zend\Log\Writer;

use Zend\Log\Formatter\Simple as SimpleFormatter;

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
        parent::__construct($options);

        $this->mode = isset($options['mode']) ? $options['mode']: 0 ;
        $this->destination = isset($options['destination']) ? $options['destination']: null;


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
}
