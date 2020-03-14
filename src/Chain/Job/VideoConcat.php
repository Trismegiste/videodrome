<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;

/**
 * Concat a list of video into one mp4
 */
class VideoConcat extends FileJob {

    protected function process(Media $filename): Media {
        $this->logger->info("Concat " . count($filename) . " video");

        if (count($filename) <= 1) {
            throw new JobException("VideoConcat : Not enough video to concat");
        }

        $tmpArray = iterator_to_array($filename);
        // sorting ?
        if ($tmpArray[0]->hasMeta('start')) {
            usort($tmpArray, function(Media $a, Media $b) {
                return $a->getMeta('start') - $b->getMeta('start');
            });
        }
        $output = $tmpArray[0]->getFilenameNoExtension() . '-compil.mp4';

        $ffmpeg = new Process([
            'ffmpeg', '-y',
            '-i', 'concat:' . implode('|', $tmpArray),
            $output
        ]);
        $ffmpeg->setTimeout(null);
        $ffmpeg->run();

        if (!$ffmpeg->isSuccessful()) {
            throw new JobException('VideoConcat : Fail to concat ' . implode('|', $tmpArray));
        }

        try {
            $generated = new MediaFile($output, $filename->getMetadataSet());
        } catch (RuntimeException $ex) {
            throw new JobException("VideoConcat : $output does not exist");
        }
        $this->logger->info("Video concat $output generated");

        return $generated;
    }

}
