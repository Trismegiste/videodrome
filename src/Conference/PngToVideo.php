<?php

namespace Trismegiste\Videodrome\Conference;

use Trismegiste\Videodrome\Task;

/**
 * Description of PngToVideo
 */
class PngToVideo implements Task {

    private $image;
    private $duration;
    private $framerate = 3;

    public function __construct($image, $duration) {
        $this->image = $image;
        $this->duration = $duration;
    }

    public function clean() {
        shell_exec("rm " . $this->getOutputName());
    }

    public function exec() {
        $animate = new \Symfony\Component\Process\Process([
            'ffmpeg', '-y',
            '-framerate', $this->framerate,
            '-loop', 1,
            '-i', $this->image,
            '-t', $this->duration,
            '-c:v', 'huffyuv',
            $this->getOutputName()
        ]);
        $animate->run();
        if (!$animate->isSuccessful()) {
            throw new \Trismegiste\Videodrome\TaskException("Error when generating " . $this->getOutputName());
        }
    }

    public function getOutputName() {
        return $this->image . '.avi';
    }

}
