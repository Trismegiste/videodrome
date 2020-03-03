<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * Convets SCG to PNG
 */
class SvgToPng extends FileJob {

    protected function process(array $filename, array $context): array {
        $result = [];
        foreach ($filename as $vector) {
            $result[] = $this->convert($vector);
        }

        return $result;
    }

    protected function convert(string $vector): string {
        $this->logger->info("Converting $vector");
        $output = pathinfo($vector, PATHINFO_FILENAME) . '.png';
        $ink = new Process([
            'inkscape',
            '-e', $output,
            $vector
        ]);
        $ink->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
