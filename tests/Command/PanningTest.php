<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\Panning;

class PanningTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new Panning();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:panning'));

        $this->assertEquals(0, $tester->execute([
                    'folder' => __DIR__ . '/../fixtures',
                    'marker' => __DIR__ . '/../fixtures/panning.txt'
        ]));

        $this->assertRegExp("/Panning generator/", $tester->getDisplay());
        foreach ([1, 3] as $k) {
            $name = "picture$k-extended.avi";
            $this->assertFileExists($name);
            unlink($name);
        }
    }

}
