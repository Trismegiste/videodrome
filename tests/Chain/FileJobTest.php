<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\FileJob;

class FileJobTest extends TestCase {

    public function testEndProcessing() {
        $sut = $this->getMockForAbstractClass(FileJob::class);
        $sut->expects($this->once())
                ->method('process')
                ->willReturn(['return']);

        $ret = $sut->execute(['dummy']);
        $this->assertEquals(['return'], $ret);
    }

    public function testDelegateProcessing() {
        $delegate = $this->getMockForAbstractClass(FileJob::class);
        $delegate->expects($this->once())
                ->method('process')
                ->with(['dummy'])
                ->willReturn(['return']);

        $sut = $this->getMockBuilder(FileJob::class)
                ->setConstructorArgs([$delegate])
                ->enableOriginalConstructor()
                ->getMock();

        $sut->expects($this->once())
                ->method('process')
                ->with(['return'])
                ->willReturn(['final']);

        shell_exec("touch return");
        $ret = $sut->execute(['dummy']);
        $this->assertEquals(['final'], $ret);
    }

}
