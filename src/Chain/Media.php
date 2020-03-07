<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * This is a media or a group of media (picture, movie, svg, sound...) with a set of metadata
 */
interface Media {

    public function hasMeta(string $key): bool;

    public function getMeta(string $key);

    public function getMetadataSet(): array;

    public function isLeaf(): bool;

    public function unlink();
    
    // permet de faire un fallback sur pour les metadata ?
    //public function getParent();
}
