<?php

namespace Trismegiste\Videodrome\Util;

/**
 * Config file reader for Cutter
 */
class CutterCfg implements \IteratorAggregate {

    protected $config;

    public function __construct(string $filename) {
        $content = file($filename);
        $this->config = [];
        foreach ($content as $line) {
            if (preg_match("/^([^\s]+)\s([.0-9]+)$/", $line, $extract)) {
                $this->config[$extract[1]] = $extract[2];
            }
        }
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->config);
    }

    public function getStart(string $key, float $default = 0): float {
        return (array_key_exists($key, $this->config)) ? $this->config[$key] : $default;
    }

}
