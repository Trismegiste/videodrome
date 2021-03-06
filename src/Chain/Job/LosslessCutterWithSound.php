<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MediaType\Video;
use Trismegiste\Videodrome\Util\Ffprobe;

/**
 * Video croping and encoding in lossless codecs for further editing
 */
class LosslessCutterWithSound extends FileJob {

    protected function process(Media $filename): Media {
        $cutted = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $video) {
            if (!$video->isVideo()) {
                continue;
            }
            $meta = $video->getMetadataSet();
            $ret = $this->cut($video, $meta['width'], $meta['height'], $meta['cutBefore'], $meta['duration'], $meta['fps'], $meta['target']);
            $cutted[] = new Video($ret, $meta);
        }

        return $cutted;
    }

    protected function cut(string $video, int $width, int $height, float $start, float $duration, float $fps, string $output): string {
        $this->logger->info("Cutting $video");

        $cmd = ["ffmpeg", '-y', '-i', $video];
        if (0 != $duration) {
            array_push($cmd, '-ss', $start, '-t', $duration);
        }

        $info = new Ffprobe($video);
        if (($height !== $info->getHeight()) || ($width !== $info->getWidth())) {
            array_push($cmd, '-vf', "scale={$width}x$height:flags=lanczos");
        }

        $output .= ".avi";
        array_push($cmd, '-r', $fps, '-c:v', 'libx264', '-preset', 'ultrafast', '-crf', 0, '-pix_fmt', 'yuv444p', '-c:a', 'pcm_s16le', $output);
        $ffmpeg = new Process($cmd);
        $ffmpeg->setTimeout(null);
        $ffmpeg->mustRun();

        return $output;
    }

}
