<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\TrailerBuilder;

class TrailerBuilderTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new TrailerBuilder();
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
        $probe = json_decode(shell_exec("ffprobe -v quiet -i $name -print_format json -show_entries format=duration"));
        $this->assertEquals(3, $probe->format->duration);
        unlink($name);
    }

    public function testExecuteFail() {
        $this->expectException(RuntimeException::class);
        $application = new Application();
        $application->setAutoExit(false);
        $command = new TrailerBuilder();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:build'));

        $dir = __DIR__ . '/../fixtures/trailer';
        $this->assertEquals(0, $tester->execute([
                    'vector' => $dir . '/vector/',
                    'picture' => $dir . '/pix/',
                    'video' => $dir . '/vid/',
                    'sound' => $dir . '/../sound1.ogg',
                    'marker' => $dir . '/badmarker.txt'
        ]));
    }

}
