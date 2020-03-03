<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Abstract job
 */
abstract class FileJob implements JobInterface {

    protected $delegated;
    protected $logger;

    public function __construct(JobInterface $child = null) {
        $this->delegated = $child;
    }

    final public function setLogger(LoggerInterface $log) {
        $this->logger = $log;
        if (!is_null($this->delegated)) {
            $this->delegated->setLogger($log);
        }
    }

    final public function execute(array $filename): array {
        if (!is_null($this->delegated)) {
            $input = $this->delegated->execute($filename);
            $output = $this->process($input);
            foreach ($input as $fch) {
                unlink($fch);
            }
        } else {
            $output = $this->process($filename);
        }

        return $output;
    }

    abstract protected function process(array $filename): array;
}
