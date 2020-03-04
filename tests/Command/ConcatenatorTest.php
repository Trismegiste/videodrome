<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\Concatenator;

class ConcatenatorTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new Concatenator();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:concat'));

        $this->assertEquals(0, $tester->execute([
                    'video' => __DIR__ . '/../fixtures',
                    'marker' => __DIR__ . '/../fixtures/concat.txt'
        ]));

        $this->assertRegExp("/Concat 2 video/", $tester->getDisplay());
        $this->assertRegExp("/red-extended-over-compil.mp4 generated/", $tester->getDisplay());
        $name = "red-extended-over-compil.mp4";
        $this->assertFileExists($name);
        unlink($name);
    }

}
