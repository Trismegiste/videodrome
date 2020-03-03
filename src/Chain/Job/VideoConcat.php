<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * Concat a list of video into one mp4
 */
class VideoConcat extends FileJob {

    protected function process(array $filename, array $unused): array {
        if (count($filename) <= 1) {
            throw new JobException("VideoConcat : Not enough video to concat");
        }
        $output = pathinfo($filename[0], PATHINFO_FILENAME) . '-compil.mp4';

        $ffmpeg = new Process('ffmpeg -y -i "concat:' . implode('|', $filename) . '" ' . $output);
        $ffmpeg->setTimeout(null);
        $ffmpeg->run();

        if (!$ffmpeg->isSuccessful()) {
            throw new JobException('VideoConcat : Fail to concat ' . implode('|', $filename));
        }
        if (!file_exists($output)) {
            throw new JobException("VideoConcat : $output does not exist");
        }
        $this->logger->info("Video concat $output generated");

        return [$output];
    }

}
