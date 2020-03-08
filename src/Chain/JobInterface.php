<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract : Transforms a set of Media into another set of Media
 */
interface JobInterface {

    /**
     * Do the job with a Media source and returns the Media output
     */
    public function execute(Media $filename): Media;

    /**
     * Sets the logger for this object and its children
     * @param LoggerInterface $log
     */
    public function setLogger(LoggerInterface $log);
}
