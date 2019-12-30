<?php

namespace Trismegiste\Videodrome\Conference;

use Trismegiste\Videodrome\Task;
use Symfony\Component\Process\Process;
use Trismegiste\Videodrome\TaskException;

/**
 * VideoConcat concats a set of video into a single one
 */
class VideoConcat implements Task {

    private $filename;
    private $sound;
    private $tmpwosound = 'sans-son.mp4';

    public function __construct(array $filename, $sound) {
        $this->filename = $filename;
        $this->sound = $sound;
    }

    public function clean() {
        shell_exec("rm " . $this->tmpwosound);
    }

    public function exec() {
        $ffmpeg = new Process('ffmpeg -y -i "concat:' . implode('|', $this->filename) . '" ' . $this->tmpwosound);
        $ffmpeg->setTimeout(null);
        $ffmpeg->run();
        if (!$ffmpeg->isSuccessful()) {
            throw new TaskException('Fail to concat ' . implode('|', $this->filename));
        }

        $ffmpeg = new Process(['ffmpeg', '-y',
            '-i', 'sans-son.mp4',
            '-i', $this->sound,
            '-shortest',
            '-strict', -2,
            '-c:v', 'copy',
            '-c:a', 'aac',
            'result.mp4'
        ]);
        $ffmpeg->run();
        if (!$ffmpeg->isSuccessful()) {
            throw new TaskException('Fail to combine with sound');
        }
    }

}
