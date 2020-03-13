<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class ImpressToPdfTest extends TestCase {

    public function testExecute() {
        $sut = new ImpressToPdf();
        $ret = $sut->execute(new MediaFile(__DIR__ . '/../../fixtures/fixtures1.odp'));
        $this->assertTrue($ret->isLeaf());
        $this->assertEquals('fixtures1.pdf', (string) $ret);
        $this->assertFileExists((string) $ret);
        $ret->unlink();
    }

    public function testFailNotLeaf() {
        $this->expectException(JobException::class);
        $sut = new ImpressToPdf();
        $ret = $sut->execute(new MediaList());
    }

}
