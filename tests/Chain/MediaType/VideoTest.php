<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaType\Video;

class VideoTest extends TestCase {

    protected $sut;

    protected function setUp(): void {
        $this->sut = new Video(join_paths(__DIR__, '../../fixtures/blue-cut-over.avi'));
    }

    protected function tearDown(): void {
        unset($this->sut);
    }

    public function testSize() {
        $this->assertEquals(160, $this->sut->getWidth());
        $this->assertEquals(90, $this->sut->getHeight());
    }

    public function testDuration() {
        $this->assertEquals(1.0, $this->sut->getDuration());
    }

}
