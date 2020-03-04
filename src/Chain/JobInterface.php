<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract
 */
interface JobInterface {

    /**
     * Do the job
     * @param array $filename an array of MetaFileInfo
     * @return array new files
     */
    public function execute(array $filename): array;

    public function setLogger(LoggerInterface $log);
}
