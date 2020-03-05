<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\Trailer;

class TrailerTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new Trailer();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:build'));

        $dir = __DIR__ . '/../fixtures/trailer';
        $this->assertEquals(0, $tester->execute([
                    'vector' => $dir . '/vector/',
                    'picture' => $dir . '/pix/',
                    'video' => $dir . '/vid/',
                    'sound' => $dir . '/../sound1.ogg',
                    'marker' => $dir . '/marker.txt'
        ]));

        $this->assertRegExp("/Build a trailer/", $tester->getDisplay());
        $name = 'darkred-extended-over-compil-sound.mp4';
        $this->assertFileExists($name);
        unlink($name);
    }

}
