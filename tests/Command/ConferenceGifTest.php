<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\ConferenceGif;

class ConferenceGifTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new ConferenceGif();
        $application->add($command);
        $tester = new CommandTester($application->find('conference:gif'));

        $this->assertEquals(0, $tester->execute([
                    'impress' => __DIR__ . '/../fixtures/fixtures1.odp',
                    '--width' => 192,
                    '--height' => 108
        ]));

        $generated = 'generated.gif';
        $this->assertRegExp("/$generated/", $tester->getDisplay());
        $this->assertTrue(file_exists($generated));
        unlink($generated);
    }

}
