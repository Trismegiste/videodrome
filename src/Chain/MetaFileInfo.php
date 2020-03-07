<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * An existing file with metadata
 */
class MetaFileInfo extends \SplFileInfo {

    protected $metadata;

    public function __construct(string $file_name, array $meta = []) {
        if (!file_exists($file_name)) {
            throw new \RuntimeException("$file_name does not exist");
        }
        parent::__construct($file_name);
        $this->metadata = $meta;
    }

    public function getData(string $key) {
        if (!array_key_exists($key, $this->metadata)) {
            throw new \OutOfBoundsException("Unknown key '$key'");
        }

        return $this->metadata[$key];
    }

    public function isPicture(): bool {
        return in_array($this->getExtension(), ['png', 'jpg', 'jpeg']);
    }

    public function isVideo(): bool {
        return in_array($this->getExtension(), ['avi', 'mkv', 'mp4', 'webm']);
    }

    public function getFilenameNoExtension(): string {
        return $this->getBasename('.' . $this->getExtension());
    }

    public function createChild(string $filename, array $override = []): MetaFileInfo {
        return new MetaFileInfo($filename, array_merge($this->metadata, $override));
    }

    public function hasData(string $key): bool {
        return array_key_exists($key, $this->metadata);
    }

}
