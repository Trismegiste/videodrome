<?php

namespace videodrome;

/**
 * Description of PngToVideo
 */
class PngToVideo implements Task {

    private $image;
    private $duration;

    public function __construct($image, $duration) {
        $this->image = $image;
        $this->duration = $duration;
    }

    public function clean() {
        shell_exec("rm " . $this->getOutputName());
    }

    public function exec() {
        shell_exec("ffmpeg -y -framerate 3 -loop 1 -i {$this->image} -t {$this->duration} -c:v huffyuv " . $this->getOutputName());
    }

    public function getOutputName() {
        return $this->image . '.avi';
    }

}
