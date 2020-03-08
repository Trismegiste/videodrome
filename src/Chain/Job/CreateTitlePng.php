<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * CreateTitlePng creates a title in PNG
 */
class CreateTitlePng extends FileJob {

    protected function process(Media $filename): Media {
        if ($filename->isLeaf()) {
            throw new JobException("Fail");
        }

        $metadata = $filename->getMetadataSet();
        $assets = $metadata['name'];
        $output = new MediaList([], $filename->getMetadataSet());
        foreach ($assets as $key) {
            $output[] = $this->writeTitle($key, $metadata['width'], $metadata['height'], $metadata['folder']);
        }

        return $output;
    }

    protected function writeTitle(string $key, int $width, int $height, string $folder): MediaFile {
        $handle = imagecreatetruecolor($width, $height);
        $h = crc32($key) % 270;
        list($red, $green, $blue) = $this->hsv2rgb($h, 1, 0.8);
        $background = imagecolorallocate($handle, $red, $green, $blue);
        imagefill($handle, 0, 0, $background);
        imagecolordeallocate($handle, $background);
        // text
        $plotColor = imagecolorallocate($handle, 255, 255, 255);
        imagefttext($handle, $width / strlen($key) * 1.3, 0, $width * 0.1, $height * 0.75, $plotColor, __DIR__ . '/akukamu.otf', strtoupper($key));
        // write
        imagepng($handle, "$folder/$key.png");
        imagedestroy($handle);

        return new MediaFile("$folder/$key.png");
    }

    /**
     * $c = array($hue, $saturation, $brightness)
     * $hue=[0..360], $saturation=[0..1], $brightness=[0..1]
     */
    protected function hsv2rgb(float $h, float $s, float $v): array {
        if ($s == 0) {
            return [$v, $v, $v];
        } else {
            $h = ($h %= 360) / 60;
            $i = floor($h);
            $f = $h - $i;
            $q[0] = $q[1] = $v * (1 - $s);
            $q[2] = $v * (1 - $s * (1 - $f));
            $q[3] = $q[4] = $v;
            $q[5] = $v * (1 - $s * $f);

            return [255 * $q[($i + 4) % 6], 255 * $q[($i + 2) % 6], 255 * $q[$i % 6]];
        }
    }

}
