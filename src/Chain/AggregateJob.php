<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Trismegiste\Videodrome\Chain\JobInterface;

/**
 * Aggregation of multiple Job
 */
class AggregateJob implements JobInterface {

    protected $delegated;
    protected $logger;

    public function __construct(array $children) {
        if (0 === count($children)) {
            throw new JobException("Cannot aggregate empty array of JobInterface");
        }
        foreach ($children as $key => $item) {
            if (!($item instanceof JobInterface)) {
                throw new JobException("Job at key '$key' does not implement JobInterface");
            }
        }
        $this->delegated = $children;
        $this->logger = new NullLogger();
    }

    public function execute(array $filename, array $context = array()): array {
        $output = [];
        foreach ($this->delegated as $child) {
            $output = array_merge($output, $child->execute($filename, $context));
        }

        return $output;
    }

    public function setLogger(LoggerInterface $log) {
        foreach ($this->delegated as $child) {
            $child->setLogger($log);
        }
    }

}
