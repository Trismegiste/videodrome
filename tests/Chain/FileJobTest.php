<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;

class FileJobTest extends TestCase {

    public function testEndProcessing() {
        $input = $this->getMockForAbstractClass(Media::class);
        $output = $this->getMockForAbstractClass(Media::class);

        $sut = $this->getMockForAbstractClass(FileJob::class);
        $sut->expects($this->once())
                ->method('process')
                ->with($input)
                ->willReturn($output);

        $ret = $sut->execute($input);
        $this->assertEquals($output, $ret);
    }

    public function testDelegateProcessing() {
        $input = $this->getMockForAbstractClass(Media::class);
        $middle = $this->getMockForAbstractClass(Media::class);
        $output = $this->getMockForAbstractClass(Media::class);

        $delegate = $this->getMockForAbstractClass(FileJob::class);
        $delegate->expects($this->once())
                ->method('process')
                ->with($input)
                ->willReturn($middle);

        $sut = $this->getMockBuilder(FileJob::class)
                ->setConstructorArgs([$delegate])
                ->enableOriginalConstructor()
                ->getMock();

        $sut->expects($this->once())
                ->method('process')
                ->with($middle)
                ->willReturn($output);

        $ret = $sut->execute($input);
        $this->assertEquals($output, $ret);
    }

}
