<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Util\Ffprobe;

/**
 * Overlay a SVG above a video
 */
class SvgOverlay extends FileJob {

    /**
     * Puts an overlay on a video
     */
    protected function process(Media $filename): Media {
        // convert all SVG from metadata of each video :
        $vector = new MediaList();
        foreach ($filename as $vid) {
            $info = new Ffprobe($vid);
            $vector[] = new MediaFile($vid->getMeta('svg'), ['width' => $info->getWidth(), 'height' => $info->getHeight()]);
        }
        $cor = new SvgToPng();
        $cor->setLogger($this->logger);
        $pixeled = $cor->execute($vector);

        // now overlay the generated PNG on the video : the indexed order is kept unchanged
        $result = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $idx => $video) {
            $png = $pixeled[$idx];
            $ret = $this->overlay($png, $video);
            $result[] = new MediaFile($ret, $video->getMetadataSet());
        }
        $pixeled->unlink();

        return $result;
    }

    protected function overlay(string $png, string $video): string {
        $output = pathinfo($video, PATHINFO_FILENAME) . '-over.avi';
        $this->logger->info("Generating $output");
        $ffmpeg = new Process(['ffmpeg', '-y',
            '-i', $video,
            '-i', $png,
            '-filter_complex',
            '[0:v][1:v]overlay',
            '-c:v', 'huffyuv',
            $output
        ]);
        $ffmpeg->setTimeout(600);
        $ffmpeg->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
