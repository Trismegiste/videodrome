<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\Video;

/**
 * A cutter of video
 */
class Cutter extends FileJob {

    const framerate = 30;

    protected function process(Media $filename): Media {
        $cutted = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $video) {
            if (!$video->isVideo()) {
                continue;
            }
            $meta = $video->getMetadataSet();
            $ret = $this->cut($video, $meta['width'], $meta['height'], $meta['cutBefore'], $meta['duration']);
            $cutted[] = new Video($ret, $meta);
        }

        return $cutted;
    }

    protected function cut(string $video, int $width, int $height, float $start, float $duration): string {
        $this->logger->info("Cutting $video");
        $output = pathinfo($video, PATHINFO_FILENAME) . "-cut.avi";
        $ffmpeg = new Process(['ffmpeg', '-y',
            '-i', $video,
            '-ss', $start, '-t', $duration,
            '-map', '0:v',
            '-vf', "scale={$width}x$height:flags=lanczos",
            '-r', self::framerate,
            '-c:v', 'huffyuv',
            $output
        ]);
        $ffmpeg->mustRun();

        return $output;
    }

}
