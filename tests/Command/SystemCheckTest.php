<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\SystemCheck;

class SystemCheckTest extends TestCase {

    public function testExecute() {
        $application = new Application();
        $application->setAutoExit(false);
        $command = new SystemCheck();
        $application->add($command);

        $tester = new CommandTester($application->find('system:check'));

        $this->assertEquals(0, $tester->execute([]));
        $this->assertRegExp("/Checking/", $tester->getDisplay());
    }

}
