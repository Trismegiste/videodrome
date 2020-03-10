<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\TrailerDummy;

class TrailerDummyTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new TrailerDummy();
        $application->add($command);
        $tester = new CommandTester($application->find('trailer:dummy'));

        $dir = __DIR__ . '/../fixtures/trailer';
        $this->assertEquals(0, $tester->execute([
                    'vector' => $dir . '/vector/',
                    'picture' => $dir . '/pix/',
                    'video' => $dir . '/vid/',
                    'marker' => $dir . '/marker.txt']));
    }

}
