<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;

/**
 * Muxing
 */
class AddingSound extends FileJob {

    protected function process(array $filename, array $context): array {
        if (!array_key_exists('sound', $context)) {
            throw new JobException("AddingSound : no sound file provided");
        }
        list($video) = $filename;
        $tmp = pathinfo($video);
        $output = $tmp['filename'] . '-sound.' . $tmp['extension'];
        $sound = $context['sound'];
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

        return [$output];
    }

}
