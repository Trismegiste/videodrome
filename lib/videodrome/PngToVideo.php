<?php

namespace videodrome;

/**
 * Description of PngToVideo
 */
class PngToVideo implements Task {

    private $image;
    private $duration;
    private $output;

    public function __construct($image, $duration, $output) {
        $this->image = $image;
        $this->duration = $duration;
        $this->output = $output;
    }

    public function clean() {
        shell_exec("rm {$this->output}");
    }

    public function exec() {
        shell_exec("ffmpeg -y -framerate 3 -loop 1 -i {$this->image} -t {$this->duration} -c:v huffyuv {$this->output}");
    }

}
