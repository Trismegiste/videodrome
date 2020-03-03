<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * Convert PNG into Video
 */
class PngToVideo extends FileJob {

    const framerate = 6;

    protected function process(array $filename, array $context): array {
        $duration = $context['duration'];
        if (count($filename) !== count($duration)) {
            throw new JobException("PNG count mismatch with duration");
        }

        $result = [];
        foreach ($filename as $idx => $png) {
            $vidName = pathinfo($png, PATHINFO_FILENAME) . '.avi';
            $this->createVid($png, $duration[$idx], $vidName);
            if (!file_exists($vidName)) {
                throw new JobException("PngToVideo : $vidName does not exist");
            }
            $result[] = $vidName;
        }

        return $result;
    }

    private function createVid(string $png, float $duration, string $output) {
        $animate = new Process([
            'ffmpeg', '-y',
            '-framerate', self::framerate,
            '-loop', 1,
            '-i', $png,
            '-t', $duration,
            '-c:v', 'huffyuv',
            $output
        ]);
        $animate->run();
        if (!$animate->isSuccessful()) {
            throw new JobException("PngToVideo : Error when generating " . $output);
        }
    }

}
