<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * This class creates a video panning from a picture
 */
class ImagePanning extends FileJob {

    const framerate = 30;

    private $blankCanvas = "tmp-canvas.png";
    private $blankVideo = "tmp-blank.avi";

    protected function process(Media $filename): Media {
        $panned = new MediaList([], $filename->getMetadataSet());
        foreach ($filename as $picture) {
            $meta = $picture->getMetadataSet();
            $ret = $this->pan($picture, $meta['width'], $meta['height'], $meta['duration'], $meta['direction']);
            $panned[] = new MediaFile($ret, $meta);
        }

        return $panned;
    }

    protected function pan(string $picture, int $vidWidth, int $vidHeight, float $duration, string $dir): string {
        $this->logger->info("Panning $picture");
        $output = pathinfo($picture, PATHINFO_FILENAME) . '.avi';

        // creating the canvas
        $imagick = new Process([
            'convert',
            '-size', "{$vidWidth}x$vidHeight",
            "canvas:black",
            $this->blankCanvas
        ]);
        $imagick->mustRun();

        // Animating the black canvas
        // Why I don't use the lavfi filter ? Because there are some duration problems (probably rounding timeframe)
        $ffmpeg = new Process(["ffmpeg", "-y",
            "-framerate", self::framerate,
            "-loop", 1,
            "-i", $this->blankCanvas,
            "-t", $duration,
            "-c:v", "huffyuv",
            $this->blankVideo
        ]);
        $ffmpeg->mustRun();
        unlink($this->blankCanvas);

        $equation = $this->getEquation($picture, $vidWidth, $vidHeight, $duration, $dir);
        $ffmpeg = new Process(['ffmpeg', '-y',
            '-i', $this->blankVideo,
            '-i', $picture,
            '-filter_complex', "[0:v][1:v]overlay=$equation:enable='between(t,0,$duration)'",
            '-c:v', 'huffyuv',
            $output
        ]);
        $ffmpeg->mustRun();
        unlink($this->blankVideo);

        return $output;
    }

    /**
     * Gets the equation for panning a picture in a video for ffmpeg
     * 
     * @return string
     * @throws JobException
     */
    protected function getEquation(string $picture, int $vidWidth, int $vidHeight, float $duration, string $direction = '+'): string {
        list($width, $height) = getimagesize($picture);

        if ($height > $vidHeight) {
            $speed = ($height - $vidHeight) / $duration;
            switch ($direction) {
                case '+':
                    // pan to the bottom of the picture
                    $speed = -$speed;
                    $equation = "y=$speed*t";
                    break;
                case '-':
                    // pan to the top of the picture
                    $delta = $height - $vidHeight;
                    $equation = "y=$speed*t-$delta";
                    break;
            }
        } else if ($width > $vidWidth) {
            $speed = ($width - $vidWidth) / $duration;
            switch ($direction) {
                case '+':
                    // pan to the right of the picture
                    $speed = -$speed;
                    $equation = "x=$speed*t";
                    break;
                case '-';
                    // pan to the left of the picture
                    $delta = $width - $vidWidth;
                    $equation = "x=$speed*t-$delta";
                    break;
            }
        } else if (($width == $vidWidth) && ($height == $vidHeight)) {
            // the picture has the same ratio : no panning
            $equation = "x=0";
        } else {
            throw new JobException("Bad picture size for format");
        }

        return $equation;
    }

}
