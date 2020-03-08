<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Convert PNG into Video
 */
class PngToVideo extends FileJob {

    const framerate = 6;

    protected function process(Media $filename): Media {
        $result = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $png) {
            $vidName = $png->getFilenameNoExtension() . '.avi';
            $this->createVid($png, $png->getMeta('duration'), $vidName);
            $result[] = new MediaFile($vidName, $png->getMetadataSet());
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
        $this->logger->info("$output generated");
    }

}
