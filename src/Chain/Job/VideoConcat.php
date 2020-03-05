<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * Concat a list of video into one mp4
 */
class VideoConcat extends FileJob {

    protected function process(array $filename): array {
        $this->logger->info("Concat " . count($filename) . " video");

        if (count($filename) <= 1) {
            throw new JobException("VideoConcat : Not enough video to concat");
        }
        $firstMeta = $filename[0]->getMetadata();
        // sorting ?
        if (array_key_exists('start', $firstMeta)) {
            usort($filename, function(MetaFileInfo $a, MetaFileInfo $b) {
                return $a->getData('start') - $b->getData('start');
            });
        }
        $output = $filename[0]->getFilenameNoExtension() . '-compil.mp4';

        $ffmpeg = new Process('ffmpeg -y -i "concat:' . implode('|', $filename) . '" ' . $output);
        $ffmpeg->setTimeout(null);
        $ffmpeg->run();

        if (!$ffmpeg->isSuccessful()) {
            throw new JobException('VideoConcat : Fail to concat ' . implode('|', $filename));
        }

        try {
            $generated = new MetaFileInfo($output, $firstMeta);  // @todo for duration meta, perhaps a good idea to sum all durations ?
        } catch (RuntimeException $ex) {
            throw new JobException("VideoConcat : $output does not exist");
        }
        $this->logger->info("Video concat $output generated");

        return [$generated];
    }

}
