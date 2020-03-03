<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\CutterResize;

class CutterResizeTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new CutterResize();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:cutter'));

        $this->assertEquals(0, $tester->execute([
                    'folder' => __DIR__ . '/../fixtures',
                    'marker' => __DIR__ . '/../fixtures/trailer.txt'
        ]));

        $this->assertRegExp("/Video extractor/", $tester->getDisplay());
        $name = "cutter-cut.avi";
        $this->assertFileExists($name);
        unlink($name);
    }

}
