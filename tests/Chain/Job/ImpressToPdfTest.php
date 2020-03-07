<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;
use Trismegiste\Videodrome\Chain\MediaList;
use Trismegiste\Videodrome\Chain\MetaFileInfo;

class ImpressToPdfTest extends TestCase {

    public function testExecute() {
        $sut = new ImpressToPdf();
        $ret = $sut->execute(new MediaList([new MetaFileInfo(__DIR__ . '/../../fixtures/fixtures1.odp')]));
        $this->assertEquals('fixtures1.pdf', (string) $ret[0]);
        $this->assertFileExists((string) $ret[0]);
        unlink($ret[0]);
    }

}
