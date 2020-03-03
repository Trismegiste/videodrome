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
                    'impress' => dirname(__DIR__) . '/Chain/Job/fixtures1.odp',
                    'voice' => dirname(__DIR__) . '/Chain/Job/sound1.ogg',
                    'marker' => __DIR__ . '/sound1.txt'
        ]));

        $generated = 'fixtures1-0-compil-sound.mp4';
        $this->assertRegExp("/$generated/", $tester->getDisplay());
        $this->assertTrue(file_exists($generated));
        unlink($generated);
    }

}
