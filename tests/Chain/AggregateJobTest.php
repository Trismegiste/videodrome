<?php

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Trismegiste\Videodrome\Chain\AggregateJob;
use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\JobInterface;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

class AggregateJobTest extends TestCase {

    protected function createMediaFile($name) {
        shell_exec("touch $name");
        return new MediaFile($name);
    }

    public function testDelegateProcessing() {
        $media = array_map([$this, 'createMediaFile'], ['dummy', 'return1', 'return2']);
        $input = new MediaList([$media[0]]);

        $delegate1 = $this->getMockForAbstractClass(FileJob::class);
        $delegate1->expects($this->once())
                ->method('process')
                ->with($input)
                ->willReturn(new MediaList([$media[1]]));

        $delegate2 = $this->getMockForAbstractClass(FileJob::class);
        $delegate2->expects($this->once())
                ->method('process')
                ->with($input)
                ->willReturn(new MediaList([$media[2]]));

        $sut = new AggregateJob([$delegate1, $delegate2]);

        $ret = $sut->execute($input);
        $this->assertCount(2, $ret);
        $this->assertEquals('return1', $ret[0]);
        $this->assertEquals('return2', $ret[1]);
        // clean
        $clean = new MediaList($media);
        $clean->unlink();
    }

    public function testEmptyList() {
        $this->expectException(JobException::class);
        new AggregateJob([]);
    }

    public function testBadObject() {
        $this->expectException(JobException::class);
        new AggregateJob([new stdClass()]);
    }

    public function testPropagateLogger() {
        $delegate1 = $this->getMockForAbstractClass(JobInterface::class);
        $delegate1->expects($this->once())
                ->method('setLogger');

        $delegate2 = $this->getMockForAbstractClass(JobInterface::class);
        $delegate2->expects($this->once())
                ->method('setLogger');

        $sut = new AggregateJob([$delegate1, $delegate2]);
        $sut->setLogger(new NullLogger());
    }

}
