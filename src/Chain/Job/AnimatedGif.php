<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class AnimatedGif extends FileJob {

    protected function process(array $filename): array {
        $firstMeta = $filename[0]->getMetadata();
        $delay = $filename[0]->getData("delay");
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

        return [new MetaFileInfo($output, $firstMeta)];
    }

}
