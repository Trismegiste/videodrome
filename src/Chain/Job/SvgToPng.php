<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * Convets SCG to PNG
 */
class SvgToPng extends FileJob {

    protected function process(array $filename): array {
        $result = [];
        foreach ($filename as $vector) {
            $result[] = new MetaFileInfo($this->convert($vector), $vector->getMetadata());
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
