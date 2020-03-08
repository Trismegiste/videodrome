<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * This is a media or a group of media (picture, movie, svg, sound...) with a set of metadata
 * Design pattern : it's almost a Composite but it's not, since only one level of children is implemented by design
 * A Tree is irrelevant here
 */
interface Media {

    /**
     * This media has a metadata named for a key ? 
     * @param string $key the key to the metadata
     * @return bool
     */
    public function hasMeta(string $key): bool;

    /**
     * Gets the metadata for a key
     * @param string $key the key to the metadata
     * @return mixed the value of the metadata
     */
    public function getMeta(string $key);

    /**
     * Get the full content of the metadata - Don't use this unless you create a new Media
     * @return array the full array of metadata
     */
    public function getMetadataSet(): array;

    /**
     * Is this Media atomic or is it a set of Media ?
     */
    public function isLeaf(): bool;

    /**
     * Delete the Media
     */
    public function unlink();


    /**
     * The source of this Media. Useful for metadata fallback
     */
    //public function getSource() : Media;
}
