<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\MediaType\MediaPdf;

class MediaPdfTest extends TestCase {

    protected $sut;

    protected function setUp(): void {
        $this->sut = new MediaPdf(join_paths(__DIR__, '../../fixtures/fixtures1.pdf'));
    }

    protected function tearDown(): void {
        unset($this->sut);
    }

    public function testPageCount() {
        $this->assertEquals(3, $this->sut->getPageCount());
    }

    public function testDensity() {
        $this->assertEqualsWithDelta(174, $this->sut->getMinDensityFor(1920, 1080), 1);
    }

    public function testNoDurationMetadata() {
        $this->expectException(OutOfBoundsException::class);
        $this->sut->getDurationForPage(2);
    }

    public function testNotAnArrayDurationMetadata() {
        $sut = new MediaPdf($this->sut, ['duration' => 1]);
        $this->expectException(OutOfRangeException::class);
        $sut->getDurationForPage(2);
    }

    public function testNoDurationMetadataForPage() {
        $sut = new MediaPdf($this->sut, ['duration' => [1, 1, 1]]);
        $this->expectException(OutOfRangeException::class);
        $sut->getDurationForPage(3);
    }

    public function testGetDurationMetadataForPage() {
        $sut = new MediaPdf($this->sut, ['duration' => [1, 2, 3]]);
        $this->assertEquals(3.0, $sut->getDurationForPage(2));
    }

}
