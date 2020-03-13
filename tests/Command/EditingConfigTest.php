<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Trismegiste\Videodrome\Command\EditingConfig;

class EditingConfigTest extends TestCase {

    public function testExecute() {
        $this->assertFileNotExists(EditingConfig::defaultCfgName);
        $application = new Application();
        $application->setAutoExit(false);
        $command = new EditingConfig();
        $application->add($command);
        $tester = new CommandTester($application->find('edit:config'));
        $tester->setInputs([
            '1', '1.5', '3.5',
            '21',
            '1', '2', '2:', '3',
            'q'
        ]);

        $this->assertEquals(0, $tester->execute([
                    'video' => __DIR__ . '/../fixtures/',
                    '-x' => true
        ]));

        $this->assertFileExists(EditingConfig::defaultCfgName);
        $generatedContent = json_decode(file_get_contents(EditingConfig::defaultCfgName), true);
        $this->assertCount(2, $generatedContent);
        $entry = $generatedContent[0];
        $this->assertEquals(2, $entry['duration']);
        $this->assertEquals(1.5, $entry['start']);
        $this->assertEquals('cutter.mkv', $entry['label']);
        unlink(EditingConfig::defaultCfgName);
    }

}
