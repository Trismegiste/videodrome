<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Overelay a PNG above a video : no resize of whatsoever. This is a dumb overlay
 */
class PngOverlay extends FileJob {

    protected function process(Media $filename): Media {
        $result = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $png) {
            $ret = $this->overlay($png, $png->getMeta('video'));
            $result[] = new MediaFile($ret, $png->getMetadataSet());
        }

        return $result;
    }

    protected function overlay(string $png, string $video): string {
        $output = pathinfo($video, PATHINFO_FILENAME) . '-over.avi';
        $this->logger->info("Generating $output");
        $ffmpeg = new Process(['ffmpeg', '-y',
            '-i', $video,
            '-i', $png,
            '-filter_complex',
            '[0:v][1:v]overlay',
            '-c:v', 'huffyuv',
            $output
        ]);
        $ffmpeg->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
