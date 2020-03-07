<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * A group of MetaFileInfo with metadata
 */
class MediaList implements \ArrayAccess, \Countable, \IteratorAggregate {

    protected $list;
    protected $metadata;

    public function __construct(array $group = [], array $metadata = []) {
        foreach ($group as $key => $fch) {
            if (!($fch instanceof MetaFileInfo)) {
                throw new \UnexpectedValueException("File with key '$key' is not a MetaFileInfo");
            }
            $this->list[] = $fch;
        }
        $this->metadata = $metadata;
    }

    public function count(): int {
        return count($this->list);
    }

    public function offsetExists($offset): bool {
        return array_key_exists($offset, $this->list);
    }

    public function offsetGet($offset) {
        return $this->list[$offset];
    }

    public function offsetSet($offset, $value) {
        if (!($value instanceof MetaFileInfo)) {
            throw new \UnexpectedValueException("File with key '$offset' is not a MetaFileInfo");
        }
        $this->list[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->list[$offset]);
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->list);
    }

    public function getData(string $key) {
        if (!array_key_exists($key, $this->metadata)) {
            throw new \OutOfBoundsException("Unknown metadata '$key'");
        }

        return $this->metadata[$key];
    }

    public function createChild(array $group, array $override): MediaList {
        return new MediaList($group, array_merge($this->metadata, $override));
    }

    public function hasData(string $key): bool {
        return array_key_exists($key, $this->metadata);
    }

}
