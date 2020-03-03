<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\Job\ImpressToPdf;

class ImpressToPdfTest extends TestCase {

    public function testExecute() {
        $sut = new ImpressToPdf();
        $ret = $sut->execute([__DIR__ . '/fixtures1.odp']);
        $this->assertEquals(['fixtures1.pdf'], $ret);
        $this->assertFileExists($ret[0]);
        unlink($ret[0]);
    }

}
