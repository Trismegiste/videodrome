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

    /**
     * Is this Media a picture ?
     * @return bool
     */
    public function isPicture(): bool {
        return in_array($this->getExtension(), ['png', 'jpg', 'jpeg']);
    }

    /**
     * Is this Media a video ?
     * @return bool
     */
    public function isVideo(): bool {
        return in_array($this->getExtension(), ['avi', 'mkv', 'mp4', 'webm']);
    }

    /**
     * Gets the filename of Media without extension
     * @return string
     */
    public function getFilenameNoExtension(): string {
        return $this->getBasename('.' . $this->getExtension());
    }

    /** @see Media */
    public function getMeta(string $key) {
        if (!array_key_exists($key, $this->metadata)) {
            throw new \OutOfBoundsException("Unknown key '$key'");
        }

        return $this->metadata[$key];
    }

    /** @see Media */
    public function getMetadataSet(): array {
        return $this->metadata;
    }

    /** @see Media */
    public function hasMeta(string $key): bool {
        return array_key_exists($key, $this->metadata);
    }

    /** @see Media */
    public function isLeaf(): bool {
        return true;
    }

    /** @see Media */
    public function unlink() {
        unlink($this);
    }

}
