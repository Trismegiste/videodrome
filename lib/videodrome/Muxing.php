<?php

namespace videodrome;

/**
 * Description of Muxing
 */
class Muxing implements Task {

    private $video;
    private $sound;

    public function __construct($vid, $sound) {
        $this->video = $vid;
        $this->sound = $sound;
    }

    public function clean() {
        
    }

    public function exec() {
        shell_exec("ffmpeg -y -i {$this->video} -i {$this->sound} -shortest -strict -2 -c:v copy -c:a aac result.mp4");
    }

}
