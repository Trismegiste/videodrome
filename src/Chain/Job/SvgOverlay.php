<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MetaFileInfo;
use Trismegiste\Videodrome\Chain\Job\SvgToPng;

/**
 * Overlay a SVG above a video
 */
class SvgOverlay extends FileJob {

    /**
     * Puts an overlay on a video
     * @param array $filename an array of MetaFileInfo pointing to a video file
     * @return array of MetaFileInfo pointing to the resulting video
     */
    protected function process(array $filename): array {
        // convert all SVG from metadata of each video :
        $vector = [];
        foreach ($filename as $vid) {
            $vector[] = new MetaFileInfo($vid->getData('svg'));
        }
        $cor = new SvgToPng();
        $cor->setLogger($this->logger);
        $pixeled = $cor->execute($vector);

        // now overlay the generated PNG on the video : the indexed order is kept unchanged
        $result = [];
        foreach ($filename as $idx => $video) {
            $meta = $video->getMetadata();
            $png = $pixeled[$idx];
            $ret = $this->overlay($png, $video);
            unlink($png);
            $result[] = new MetaFileInfo($ret, $meta);
        }

        return $result;
    }

    protected function overlay(string $png, string $video): string {
        $output = pathinfo($video, PATHINFO_FILENAME) . '-over.avi';
        $this->logger->info("Generating $output");
        $ffmpeg = new Process('ffmpeg -y -i ' . $video . ' -i ' . $png . ' -filter_complex "[0:v][1:v]overlay" -c:v huffyuv ' . $output);
        $ffmpeg->mustRun();

        if (!file_exists($output)) {
            throw new JobException("Cannot generate $output");
        }

        return $output;
    }

}
