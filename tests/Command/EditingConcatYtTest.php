<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\EditingConcatYt;
use Trismegiste\Videodrome\Command\EditingConfig;
use Trismegiste\Videodrome\Util\Ffprobe;

class EditingConcatYtTest extends TestCase {

    public function testExecute() {
        $this->assertFileNotExists(EditingConfig::defaultCfgName);
        $application = new Application();
        $application->setAutoExit(false);
        $command = new EditingConcatYt();
        $application->add($command);
        $tester = new CommandTester($application->find('edit:youtube'));

        $output = 'output.mp4';
        $this->assertEquals(0, $tester->execute([
                    'config' => join_paths(__DIR__, '../fixtures', 'editconcat.json'),
                    '--width' => 300,
                    '--height' => 200
        ]));

        $this->assertFileExists($output);
        $info = new Ffprobe($output);
        $this->assertEqualsWithDelta(6, $info->getDuration(), 0.1);
        unlink($output);
    }

}
