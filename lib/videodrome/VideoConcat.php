<?php

namespace videodrome;

/**
 * Description of VideoConcat
 */
class VideoConcat implements Task {

    private $filename;

    public function __construct(array $filename) {
        $this->filename = $filename;
    }

    public function clean() {
        shell_exec("rm sans-son.mp4");
    }

    public function exec() {
        shell_exec('ffmpeg -y -i "concat:' . implode('|', $this->filename) . '" sans-son.mp4');
    }

}
