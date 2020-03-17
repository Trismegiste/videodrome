<?php

namespace Trismegiste\Videodrome\Chain\MediaType;

use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Util\Ffprobe;

/**
 * This MediaFile is a video
 */
class Video extends MediaFile {

    protected $info;

    public function __construct(string $file_name, array $meta = []) {
        parent::__construct($file_name, $meta);
        $this->info = new Ffprobe($file_name);
    }

    public function getWidth(): int {
        return $this->info->getWidth();
    }

    public function getHeight(): int {
        return $this->info->getHeight();
    }

    public function getDuration(): float {
        return $this->info->getDuration();
    }

}
