<?php

use PHPUnit\Framework\TestCase;
use Trismegiste\Videodrome\Chain\AggregateJob;
use Trismegiste\Videodrome\Chain\FileJob;

class AggregateJobTest extends TestCase {

    public function testDelegateProcessing() {
        $delegate1 = $this->getMockForAbstractClass(FileJob::class);
        $delegate1->expects($this->once())
                ->method('process')
                ->with(['dummy'])
                ->willReturn(['return1']);

        $delegate2 = $this->getMockForAbstractClass(FileJob::class);
        $delegate2->expects($this->once())
                ->method('process')
                ->with(['dummy'])
                ->willReturn(['return2']);

        $sut = new AggregateJob([$delegate1, $delegate2]);

        $ret = $sut->execute(['dummy']);
        $this->assertEquals(['return1', 'return2'], $ret);
    }

}
