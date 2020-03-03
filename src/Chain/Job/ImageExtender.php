<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;

/**
 * Ths class resizes an image to be larger as an output format as this format fits into the extended image
 */
class ImageExtender extends FileJob {

    protected function process(array $filename, array $context): array {
        $width = $context['width'];
        $height = $context['height'];

        $output = [];
        foreach ($filename as $picture) {
            $this->logger->info("Extending $picture");
            $output[] = $this->resize($picture, $width, $height);
        }

        return $output;
    }

    private function resize($picture, $targetWidth, $targetHeight) {
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
