<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\AbstractLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Logger for Job to Console
 */
class ConsoleLogger extends AbstractLogger {

    protected $output;

    public function __construct(OutputInterface $out) {
        $this->output = $out;
    }

    public function log($level, $message, array $context = array()) {
        $this->output->writeln($message);
    }

}
