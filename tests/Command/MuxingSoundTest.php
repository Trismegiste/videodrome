<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\MuxingSound;

class MuxingSoundTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new MuxingSound();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:muxing'));

        $this->assertEquals(0, $tester->execute([
                    'video' => __DIR__ . '/../fixtures/blue-cut-over.avi',
                    'sound' => __DIR__ . '/../fixtures/sound1.ogg'
        ]));

        $name = "blue-cut-over-sound.avi";
        $this->assertRegExp("/$name generated/", $tester->getDisplay());
        $this->assertFileExists($name);
        unlink($name);
    }

}
