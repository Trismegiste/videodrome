<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\OverlayTitle;

class OverlayTitleTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new OverlayTitle();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:overlay'));

        $this->assertEquals(0, $tester->execute([
                    'video' => __DIR__ . '/../fixtures',
                    'vector' => __DIR__ . '/../fixtures'
        ]));

        $this->assertRegExp("/Video overlay/", $tester->getDisplay());
        $name = "picture1-cut-over.avi";
        $this->assertFileExists($name);
        unlink($name);
    }

}
