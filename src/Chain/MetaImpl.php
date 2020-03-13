<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * Implementation of metadata
 */
trait MetaImpl {

    protected $metadata;

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

}
