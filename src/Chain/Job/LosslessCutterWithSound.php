<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\Ffprobe;

class LosslessCutterWithSound extends FileJob {

    const framerate = 30;

    protected function process(Media $filename): Media {
        $cutted = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $video) {
            if (!$video->isVideo()) {
                continue;
            }
            $meta = $video->getMetadataSet();
            $ret = $this->cut($video, $meta['width'], $meta['height'], $meta['cutBefore'], $meta['duration'], $meta['fps'], $meta['target']);
            $cutted[] = new MediaFile($ret, $meta);
        }

        return $cutted;
    }

    protected function cut(string $video, int $width, int $height, float $start, float $duration, float $fps, string $output): string {
        $this->logger->info("Cutting $video");

        $info = new Ffprobe($video);
        $resizeFilter = "";
        if (($height !== $info->getHeight()) || ($width !== $info->getWidth())) {
            $resizeFilter = "-vf scale={$width}x$height:flags=lanczos ";
        }

        $output .= ".avi";
        $ffmpeg = new Process("ffmpeg -y -i $video -ss $start -t $duration " .
                $resizeFilter .
                "-r $fps " .
                "-c:v libx264 -preset ultrafast -crf 0 -c:a pcm_s16le $output");
        $ffmpeg->mustRun();

        return $output;
    }

}
