<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * A group of Media with metadata
 */
class MediaList implements \ArrayAccess, \Countable, \IteratorAggregate, Media {

    protected $list;
    protected $metadata;

    public function __construct(array $group = [], array $metadata = []) {
        foreach ($group as $key => $fch) {
            if (!($fch instanceof MediaFile)) {
                throw new \UnexpectedValueException("File with key '$key' is not a MediaFile");
            }
            $this->list[] = $fch;
        }
        $this->metadata = $metadata;
    }

    /** @see \Countable */
    public function count(): int {
        return count($this->list);
    }

    /** @see \ArrayAccess */
    public function offsetExists($offset): bool {
        return array_key_exists($offset, $this->list);
    }

    /** @see \ArrayAccess */
    public function offsetGet($offset) {
        return $this->list[$offset];
    }

    /** @see \ArrayAccess */
    public function offsetSet($offset, $value) {
        if (!($value instanceof MediaFile)) {
            throw new \UnexpectedValueException("File with key '$offset' is not a MediaFile");
        }
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    /** @see \ArrayAccess */
    public function offsetUnset($offset) {
        unset($this->list[$offset]);
    }

    /** @see \IteratorAggregate */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->list);
    }

    public function getMeta(string $key) {
        if (!array_key_exists($key, $this->metadata)) {
            throw new \OutOfBoundsException("Unknown metadata '$key'");
        }

        return $this->metadata[$key];
    }

    /** @see Media */
    public function hasMeta(string $key): bool {
        return array_key_exists($key, $this->metadata);
    }

    /** @see Media */
    public function getMetadataSet(): array {
        return $this->metadata;
    }

    /** @see Media */
    public function isLeaf(): bool {
        return false;
    }

    /** @see Media */
    public function unlink() {
        foreach ($this->list as $fch) {
            $fch->unlink();
        }
    }

}
