<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Util\AudacityMarker;

class AudacityMarkerTest extends TestCase {

    public function testCreation() {
        $sut = new AudacityMarker(__DIR__ . '/../fixtures/concat.txt');
        $this->assertCount(2, $sut);
        $this->assertEquals(['red', 'blue'], $sut->getKeys());
        $this->assertEquals(1, $sut->getDuration('red'));
        $this->assertEquals(1, $sut->getStart('blue'));
    }

}
