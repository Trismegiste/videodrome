<?php

namespace Trismegiste\Videodrome\Trailer;

use Trismegiste\Videodrome\Task;
use Symfony\Component\Process\Process;

/**
 * ImageResizeForPanning resizes an image to be larger as an output format fits into
 */
class ImageResizeForPanning implements Task {

    private $picture;
    private $width;
    private $height;

    public function __construct($fch, $w, $h) {
        $this->picture = $fch;
        $this->width = $w;
        $this->height = $h;
    }

    public function exec() {
        $info = getimagesize($this->picture);
        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;

        $nh = round($this->width / $ratio);
        $nw = round($this->height * $ratio);

        if ($nh >= $this->height) {
            $resize = $this->width . 'x' . $nh;
        } else {
            $resize = $nw . 'x' . $this->height;
        }

        $imagick = new Process([
            "convert", $this->picture,
            '-resize', $resize,
            $this->getResizedName()
        ]);
        $imagick->mustRun();
    }

    public function clean() {
        shell_exec("rm " . $this->getResizedName());
    }

    public function getResizedName() {
        $info = pathinfo($this->picture);
        return "tmp-" . $info['filename'] . '.png';
    }

}
