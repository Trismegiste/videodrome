<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

/**
 * Ths class resizes an image to be larger as an output format as this format fits into the extended image
 */
class ImageExtender extends FileJob {

    protected function process(array $filename): array {
        $output = [];
        foreach ($filename as $picture) {
            if (!$picture->isPicture()) {
                continue;
            }
            $this->logger->info("Extending $picture");
            $ret = $this->resize($picture, $picture->getData('width'), $picture->getData('height'));
            $output[] = new MetaFileInfo($ret, $picture->getMetadata());
        }

        return $output;
    }

    private function resize(string $picture, int $targetWidth, int $targetHeight): string {
        $output = pathinfo($picture, PATHINFO_FILENAME) . '-extended.png';
        $info = getimagesize($picture);
        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        $nh = round($targetWidth / $ratio);
        $nw = round($targetHeight * $ratio);

        if ($nh >= $targetHeight) {
            $resize = $targetWidth . 'x' . $nh;
        } else {
            $resize = $nw . 'x' . $targetHeight;
        }

        $imagick = new Process([
            "convert", $picture,
            '-resize', $resize,
            $output
        ]);
        $imagick->mustRun();

        return $output;
    }

}
