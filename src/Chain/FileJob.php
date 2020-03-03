<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Abstract job
 */
abstract class FileJob implements JobInterface {

    protected $delegated;
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

    final public function execute(array $filename, array $context = []): array {
        if (!is_null($this->delegated)) {
            $input = $this->delegated->execute($filename, $context);
            $output = $this->process($input, $context);
            foreach ($input as $fch) {
                unlink($fch);
            }
        } else {
            $output = $this->process($filename, $context);
        }

        return $output;
    }

    abstract protected function process(array $filename, array $context): array;
}
