<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaType\Video;

/**
 * Muxing video and sound
 */
class AddingSound extends FileJob {

    protected function process(Media $video): Media {
        if (!$video->isLeaf()) {
            throw new JobException('AddingSound can only process one single file');
        }

        $output = $video->getFilenameNoExtension() . '-sound.' . $video->getExtension();
        $sound = $video->getMeta('sound');
        if (!file_exists($sound)) {
            throw new JobException("AddingSound : Sound file '$sound' does not exist");
        }

        $ffmpeg = new Process([
            'ffmpeg', '-y',
            '-i', $video,
            '-i', $sound,
            '-shortest',
            '-strict', -2,
            '-c:v', 'copy',
            '-c:a', 'aac',
            $output
        ]);
        $ffmpeg->run();
        if (!$ffmpeg->isSuccessful()) {
            throw new JobException('AddingSound : Fail to combine with sound');
        }
        $this->logger->info("Video with sound $output generated");

        return new Video($output, $video->getMetadataSet());
    }

}
