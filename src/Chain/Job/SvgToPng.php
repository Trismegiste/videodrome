<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Converts SVG to PNG
 */
class SvgToPng extends FileJob {

    /**
     * Convert SVG to PNG
     */
    protected function process(Media $filename): Media {
        $result = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $vector) {
            $png = $this->convert($vector, $vector->getMeta('width'), $vector->getMeta('height'));
            $result[] = new MediaFile($png, $vector->getMetadataSet());
        }

        return $result;
    }

    protected function convert(string $vector, int $width, int $height): string {
        $this->logger->info("Converting $vector");
        $output = pathinfo($vector, PATHINFO_FILENAME) . '.png';
        $ink = new Process([
            'inkscape',
            '-e', $output,
            '-w', $width,
            '-h', $height,
            $vector
        ]);
        $ink->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
