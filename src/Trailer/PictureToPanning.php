<?php

namespace Trismegiste\Videodrome\Trailer;

use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Task;
use Trismegiste\Videodrome\TaskException;

/**
 * PictureToPanning creates a video panning from a picture
 */
class PictureToPanning implements Task {

    private $width;
    private $height;
    private $duration;
    private $framerate;
    private $picture;
    private $blankVideo = "tmp-blank.avi";
    private $direction;

    public function __construct($w, $h, $t, $rate, $image, $dir = '+') {
        $this->height = $h;
        $this->width = $w;
        $this->duration = $t;
        $this->framerate = $rate;
        $this->picture = $image;
        $this->direction = $dir;
    }

    //put your code here
    public function clean() {
        shell_exec("rm " . $this->blankVideo);
    }

    public function exec() {
        $ffmpeg = new Process("ffmpeg -y -f lavfi -i color=c=black:s={$this->width}x{$this->height}:d={$this->duration}:r={$this->framerate} -c:v huffyuv " . $this->blankVideo);
        $ffmpeg->mustRun();

        $info = getimagesize($this->picture);
        $width = $info[0];
        $height = $info[1];

        if ($height > $this->height) {
            $speed = ($height - $this->height) / $this->duration;
            switch ($this->direction) {
                case '+':
                    // pan to the bottom of the picture
                    $speed = -$speed;
                    $equation = "y=$speed*t";
                    break;
                case '-':
                    // pan to the top of the picture
                    $delta = $height - $this->height;
                    $equation = "y=$speed*t-$delta";
                    break;
            }
        } else if ($width > $this->width) {
            $speed = ($width - $this->width) / $this->duration;
            switch ($this->direction) {
                case '+':
                    // pan to the right of the picture
                    $speed = -$speed;
                    $equation = "x=$speed*t";
                    break;
                case '-';
                    // pan to the left of the picture
                    $delta = $width - $this->width;
                    $equation = "x=$speed*t-$delta";
                    break;
            }
        } else if (($width == $this->width) && ($height == $this->height)) {
            // the picture has the same ratio : no panning
            $equation = "x=0";
        } else {
            throw new TaskException("Bad picture size for format");
        }

        $ffmpeg = new Process("ffmpeg -y -i {$this->blankVideo} -i {$this->picture} -filter_complex \"[0:v][1:v]overlay=$equation:enable='between(t,0,{$this->duration})'\" -c:v huffyuv {$this->picture}.avi");
        $ffmpeg->mustRun();
    }

    public function getFilename() {
        return $this->picture . ".avi";
    }

}
