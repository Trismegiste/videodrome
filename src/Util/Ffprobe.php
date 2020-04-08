<?php

namespace Trismegiste\Videodrome\Util;

/**
 * Get info from a video with ffprobe
 */
class Ffprobe {

    protected $duration;
    protected $width;
    protected $height;

    public function __construct(string $filename) {
        // @todo Use Process, this line bugs when $filename contains a whitespace
        $tmp = shell_exec("ffprobe -v quiet -i $filename -select_streams v:0 -print_format json -show_entries stream=width,height:format=duration");
        $probe = json_decode($tmp);
        $this->width = $probe->streams[0]->width;
        $this->height = $probe->streams[0]->height;
        $this->duration = $probe->format->duration;
    }

    public function getDuration(): float {
        return $this->duration;
    }

    public function getHeight(): int {
        return $this->height;
    }

    public function getWidth(): int {
        return $this->width;
    }

}
