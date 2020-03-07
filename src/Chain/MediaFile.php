<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * An existing file with metadata
 */
class MediaFile extends \SplFileInfo implements Media {

    protected $metadata;

    public function __construct(string $file_name, array $meta = []) {
        if (!file_exists($file_name)) {
            throw new \RuntimeException("$file_name does not exist");
        }
        parent::__construct($file_name);
        $this->metadata = $meta;
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

    public function getMeta(string $key) {
        if (!array_key_exists($key, $this->metadata)) {
            throw new \OutOfBoundsException("Unknown key '$key'");
        }

        return $this->metadata[$key];
    }

    public function getMetadataSet(): array {
        return $this->metadata;
    }

    public function hasMeta(string $key): bool {
        return array_key_exists($key, $this->metadata);
    }

    public function isLeaf(): bool {
        return true;
    }

    public function unlink() {
        unlink($this);
    }

}
