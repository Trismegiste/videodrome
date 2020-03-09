<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Util\Ffprobe;

class FfprobeTest extends TestCase {

    public function testInfo() {
        $filename = __DIR__ . '/../fixtures/cutter.mkv';
        $sut = new Ffprobe($filename);
        var_dump($sut);
    }

}
