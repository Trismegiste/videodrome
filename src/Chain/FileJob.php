<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Abstract job
 */
abstract class FileJob implements JobInterface {

    private $delegated;
    protected $logger;

    public function __construct(JobInterface $child = null) {
        $this->delegated = $child;
        $this->logger = new NullLogger();
    }

    final public function setLogger(LoggerInterface $log) {
        $this->logger = $log;
        if (!is_null($this->delegated)) {
            $this->delegated->setLogger($log);
        }
    }

    final public function execute(Media $filename): Media {
        $this->logger->debug('Entering ' . get_class($this) . '::execute()');
        if (!is_null($this->delegated)) {
            $input = $this->delegated->execute($filename);
            $output = $this->process($input);
            $this->logger->debug('Exiting ' . get_class($this) . '::process()');
            $input->unlink();
        } else {
            $output = $this->process($filename);
        }

        return $output;
    }

    /**
     * Template Method Design Pattern
     */
    abstract protected function process(Media $filename): Media;
}
