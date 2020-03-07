<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract
 */
interface JobInterface {

    /**
     * Do the job
     */
    public function execute(Media $filename): Media;

    public function setLogger(LoggerInterface $log);
}
