<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\Conference;

class ConferenceTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new Conference();
        $application->add($command);
        $tester = new CommandTester($application->find('conference:build'));

        $this->assertEquals(0, $tester->execute([
                    'impress' => __DIR__ . '/../fixtures/fixtures1.odp',
                    'voice' => __DIR__ . '/../fixtures/sound1.ogg',
                    'marker' => __DIR__ . '/../fixtures/sound1.txt',
                    '--width' => 192,
                    '--height' => 108
        ]));

        $generated = 'fixtures1-0-compil-sound.mp4';
        $this->assertRegExp("/$generated/", $tester->getDisplay());
        $this->assertTrue(file_exists($generated));
        unlink($generated);
    }

}
