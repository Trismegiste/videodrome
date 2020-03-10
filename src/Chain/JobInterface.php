<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract : Transforms a Media (File or List) into another Media (File or List)
 */
interface JobInterface {

    /**
     * Do the job with a Media source and returns the Media output
     */
    public function execute(Media $filename): Media;

    /**
     * Sets the logger for this object and all of its children Job
     * @param LoggerInterface $log
     */
    public function setLogger(LoggerInterface $log);
}
