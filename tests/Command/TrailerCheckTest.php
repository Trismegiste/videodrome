<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\TrailerCheck;

class TrailerCheckTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new TrailerCheck();
        $application->add($command);

        $tester = new CommandTester($application->find('trailer:check'));

        $dir = __DIR__ . '/../fixtures/trailer';
        $this->assertEquals(0, $tester->execute([
                    'vector' => $dir . '/vector/',
                    'picture' => $dir . '/pix/',
                    'video' => $dir . '/vid/',
                    'sound' => $dir . '/../sound1.ogg',
                    'marker' => $dir . '/marker.txt'
        ]));
        $this->assertRegExp("/OK/", $tester->getDisplay());
    }

}
