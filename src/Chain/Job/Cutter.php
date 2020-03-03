<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * A cutter of video
 */
class Cutter extends FileJob {

    const framerate = 30;

    protected function process(array $filename, array $context): array {
        $duration = $context['duration'];
        $starting = $context['start'];
        if (count($duration) !== count($filename)) {
            throw new JobException("Cutter : count mismatch between durations (" . count($duration) . ") and images (" . count($filename) . ')');
        }
        if (count($starting) !== count($filename)) {
            throw new JobException("Cutter : count mismatch between starting points (" . count($starting) . ") and images (" . count($filename) . ')');
        }

        $cutted = [];
        foreach ($filename as $video) {
            $cutted[] = $this->cut($video, $context['width'], $context['height'], $starting[$video], $duration[$video]);
        }

        return $cutted;
    }

    protected function cut(string $video, int $width, int $height, float $start, float $duration): string {
        $output = pathinfo($video, PATHINFO_FILENAME) . "-cut.avi";
        $ffmpeg = new Process("ffmpeg -y -i $video -ss $start -t $duration " .
                "-map 0:v -vf scale={$width}x$height:flags=lanczos -r " . self::framerate . " -c:v huffyuv " . $output);
        $ffmpeg->mustRun();

        return $output;
    }

}
