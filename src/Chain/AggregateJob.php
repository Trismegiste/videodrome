<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Trismegiste\Videodrome\Chain\JobInterface;

/**
 * Aggregation of multiple Job (parallel)
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

    public function execute(Media $filename): Media {
        $output = new MediaList([], $filename->getMetadataSet());
        foreach ($this->delegated as $child) {
            $ret = $child->execute($filename);
            foreach ($ret as $fch) {
                $output[] = $fch;
            }
        }

        return $output;
    }

    public function setLogger(LoggerInterface $log) {
        foreach ($this->delegated as $child) {
            $child->setLogger($log);
        }
    }

}
