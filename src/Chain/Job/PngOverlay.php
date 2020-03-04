<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * Overelay a PNG above a video
 */
class PngOverlay extends FileJob {

    protected function process(array $filename, array $context): array {
        $video = $context['video'];
        if (count($filename) !== count($video)) {
            throw new JobException("PngOverlay : count mismatch");
        }
        $result = [];
        foreach ($filename as $png) {
            $result[] = $this->overlay($png, $video[$png]);
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
