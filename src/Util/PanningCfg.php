<?php

namespace Trismegiste\Videodrome\Util;

/**
 * Panning config file reader
 */
class PanningCfg implements \IteratorAggregate {

    protected $config;

    public function __construct(string $filename) {
        $content = file($filename);
        $this->config = [];
        foreach ($content as $line) {
            if (preg_match("/^([^\s]+)\s([\+|-])$/", $line, $extract)) {
                $this->config[$extract[1]] = $extract[2];
            }
        }
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->config);
    }

    public function getDirection(string $key, string $default = '+'): string {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

}
