<?php

namespace Trismegiste\Videodrome\Conference;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\Task;
use Trismegiste\Videodrome\TaskException;

/**
 * PngToVideo creates a short video from a PNG
 */
class PngToVideo implements Task {

    private $image;
    private $duration;
    private $framerate = 6;
    private $progbar;

    public function __construct(ProgressBar $prog, $image, $duration) {
        $this->image = $image;
        $this->duration = $duration;
        $this->progbar = $prog;
    }

    public function clean() {
        shell_exec("rm " . $this->getOutputName());
    }

    public function exec() {
        $animate = new Process([
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
            throw new TaskException("Error when generating " . $this->getOutputName());
        }

        $this->progbar->advance();
    }

    public function getOutputName() {
        return $this->image . '.avi';
    }

}
