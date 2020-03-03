<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\LoggerInterface;

/**
 * Job contract
 */
interface JobInterface {

    public function execute(array $filename, array $context = []): array;

    public function setLogger(LoggerInterface $log);
}
