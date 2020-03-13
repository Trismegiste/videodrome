<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;

/**
 * Concat video with sound for youtube
 */
class ConcatYt extends FileJob {

    protected function process(Media $filename): Media {
        if ($filename->isLeaf()) {
            throw new JobException("Must be a list of video");
        }

        $output = $filename->getMeta('target');
        $cmd = ['ffmpeg', '-y'];
        foreach ($filename as $entry) {
            array_push($cmd, '-i', $entry);
        }

        array_push($cmd, '-filter_complex', 'concat=n=' . count($filename) . ':v=1:a=1 [v] [a]'
                , '-map', '[v]', '-map', '[a]'
                , '-codec:v', 'libx264', '-crf', 20, '-pix_fmt', 'yuv420p'
                , '-bf', 2, '-flags', '+cgop'
                , '-codec:a', 'aac', '-strict', -2, '-b:a', '384k', '-ar', 48000
                , '-movflags', 'faststart'
                , $output);

        $this->logger->info('Concat video');
     //   $cmd = implode(' ', $cmd); // FIX : escaping in Process seems to bug (hint: the '=' character ?)
        $ffmpeg = new Process($cmd);
        $ffmpeg->setTimeout(null);
        $ffmpeg->mustRun();

        return new MediaFile($output, $filename->getMetadataSet());
    }

}
