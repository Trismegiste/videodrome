<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\ConsoleLogger;

class ConsoleLoggerTest extends TestCase {

    public function testInfo() {
        $out = $this->getMockForAbstractClass(OutputInterface::class);
        $out->expects($this->once())
                ->method('writeln');

        $sut = new ConsoleLogger($out);
        $sut->info('dummy');
    }

}
