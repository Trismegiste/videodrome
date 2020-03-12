<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\EditingSort;

class EditingSortTest extends TestCase {

    public function testExecute() {
        $tmpCfg = 'tmp.json';
        copy(__DIR__ . '/../fixtures/editing.json', $tmpCfg);
        $this->assertFileExists($tmpCfg);

        $application = new Application();
        $application->setAutoExit(false);
        $command = new EditingSort();
        $application->add($command);
        $tester = new CommandTester($application->find('edit:sort'));
        $tester->setInputs(['3 0', 's']);

        $this->assertEquals(0, $tester->execute([
                    'config' => $tmpCfg
        ]));

        $generatedContent = json_decode(file_get_contents($tmpCfg));
        $this->assertCount(4, $generatedContent);
        $entry = $generatedContent[0];
        $this->assertEquals('yellow.mp4', $entry->label);
        unlink($tmpCfg);
    }

}
