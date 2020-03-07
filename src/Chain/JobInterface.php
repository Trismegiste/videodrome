<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract
 */
interface JobInterface {

    /**
     * Do the job
     * @param MediaList $filename a MediaList of MetaFileInfo
     * @return array new files
     */
    public function execute(MediaList $filename): MediaList;

    public function setLogger(LoggerInterface $log);
}
