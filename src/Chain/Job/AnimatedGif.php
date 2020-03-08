<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;

class AnimatedGif extends FileJob {

    protected function process(Media $filename): Media {
        $this->logger->info("Generating GIF...");
        if ($filename->isLeaf()) {
            throw new JobException("Multiple pictures must be provided");
        }
        $delay = $filename->getMeta('delay');
        $output = "generated.gif";

        foreach ($filename as $idx => $picture) {
            $magick = new Process("convert {$picture} tmp-{$idx}.png");
            $magick->mustRun();
        }

        $ffmpeg = new Process([
            'ffmpeg', '-y',
            '-f', 'image2',
            '-framerate', 1 / $delay,
            '-i', 'tmp-%d.png',
            $output
        ]);
        $ffmpeg->mustRun();

        foreach ($filename as $idx => $picture) {
            unlink("tmp-{$idx}.png");
        }

        $this->logger->info("$output generated.");

        return new MediaFile($output, $filename->getMetadataSet());
    }

}
