<?php

namespace Trismegiste\Videodrome\Util;

/**
 * an Audacity marker file
 */
class AudacityMarker implements \IteratorAggregate {

    protected $timecode;

    public function __construct(string $filename) {
        $timing = file($filename);
        $this->timecode = [];
        foreach ($timing as $clip) {
            if (preg_match("/^([.0-9]+)\s([.0-9]+)\s([^\s]+)$/", $clip, $extract)) {
                $this->timecode[$extract[3]] = [
                    'start' => (float) $extract[1],
                    'duration' => $extract[2] - $extract[1]
                ];
            }
        }
    }

    public function getStart(string $key): float {
        return $this->timecode[$key]['start'];
    }

    public function getDuration(string $key): float {
        return $this->timecode[$key]['duration'];
    }

    public function getKeys(): array {
        return array_keys($this->timecode);
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->timecode);
    }

}
