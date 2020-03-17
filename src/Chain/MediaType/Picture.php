<?php

namespace Trismegiste\Videodrome\Chain\MediaType;

use Trismegiste\Videodrome\Chain\MediaFile;

/**
 * This MediaFile is a picture
 */
class Picture extends MediaFile {

    protected $width;
    protected $height;

    public function __construct(string $file_name, array $meta = []) {
        parent::__construct($file_name, $meta);
        list($width, $height) = getimagesize($file_name);
        $this->height = $height;
        $this->width = $width;
    }

    public function getWidth(): int {
        return $this->width;
    }

    public function getHeight(): int {
        return $this->height;
    }

}
