<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\MiscConcatYt;
use Trismegiste\Videodrome\Util\Ffprobe;

class MiscConcatYtTest extends TestCase
{

    public function testExecute()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new MiscConcatYt();
        $application->add($command);
        $tester = new CommandTester($application->find('misc:concat'));

        $output = 'output.mp4';
        $this->assertEquals(0, $tester->execute([
                'folder' => join_paths(__DIR__, '../fixtures/concat')
        ]));

        $this->assertFileExists($output);
        $info = new Ffprobe($output);
        $this->assertEqualsWithDelta(4.034, $info->getDuration(), 0.1);
        unlink($output);
    }

}
