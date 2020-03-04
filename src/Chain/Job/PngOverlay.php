<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * Overelay a PNG above a video
 */
class PngOverlay extends FileJob {

    protected function process(array $filename): array {
        $result = [];
        foreach ($filename as $png) {
            $meta = $png->getMetadata();
            $ret = $this->overlay($png, $meta['video']);
            $result[] = new MetaFileInfo($ret, $meta);
        }

        return $result;
    }

    protected function overlay(string $png, string $video): string {
        $output = pathinfo($video, PATHINFO_FILENAME) . '-over.avi';
        $this->logger->info("Generating $output");
        $ffmpeg = new Process('ffmpeg -y -i ' . $video . ' -i ' . $png . ' -filter_complex "[0:v][1:v]overlay" -c:v huffyuv ' . $output);
        $ffmpeg->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
