<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Util\PanningCfg;

class PanningCfgTest extends TestCase {

    public function testRead() {
        $sut = new PanningCfg(__DIR__ . '/../fixtures/panning.cfg');
        $this->assertCount(1, $sut);
        $this->assertEquals('-', $sut->getDirection('picture3'));
        $this->assertEquals('+', $sut->getDirection('doesnotexist'));
        $this->assertEquals('-', $sut->getDirection('doesnotexist', '-'));
    }

}
