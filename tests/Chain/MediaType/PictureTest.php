<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaType\Picture;

class PictureTest extends TestCase {

    protected $sut;

    protected function setUp(): void {
        $this->sut = new Picture(join_paths(__DIR__, '../../fixtures/picture1.jpg'));
    }

    protected function tearDown(): void {
        unset($this->sut);
    }

    public function testSize() {
        $this->assertEquals(474, $this->sut->getWidth());
        $this->assertEquals(307, $this->sut->getHeight());
    }

}
