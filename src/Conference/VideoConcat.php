<?php

namespace Trismegiste\Videodrome\Conference;

use Trismegiste\Videodrome\Task;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\TaskException;

/**
 * Description of VideoConcat
 */
class VideoConcat implements Task {

    private $filename;
    private $sound;

    public function __construct(array $filename, $sound) {
        $this->filename = $filename;
        $this->sound = $sound;
    }

    public function clean() {
        shell_exec("rm sans-son.mp4");
    }

    public function exec() {
        $ffmpeg = new Process('ffmpeg -y -i "concat:' . implode('|', $this->filename) . '" sans-son.mp4');
        $ffmpeg->run();
        if (!$ffmpeg->isSuccessful()) {
            throw new TaskException('Fail to concat ' . implode('|', $this->filename));
        }

        $ffmpeg = new Process("ffmpeg -y -i sans-son.mp4 -i {$this->sound} -shortest -strict -2 -c:v copy -c:a aac result.mp4");
        $ffmpeg->run();
        if (!$ffmpeg->isSuccessful()) {
            throw new TaskException('Fail to combine with sound');
        }
    }

}
