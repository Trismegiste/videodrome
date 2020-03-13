<?php

namespace Trismegiste\Videodrome\Chain;

use Psr\Log\AbstractLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Logger to Symfony Console
 * Design pattern : Bridge
 */
class ConsoleLogger extends AbstractLogger {

    protected $output;

    public function __construct(OutputInterface $out) {
        $this->output = $out;
    }

    public function debug($message, array $context = array()) {
        $this->output->writeln($message, OutputInterface::VERBOSITY_DEBUG);
    }

    public function log($level, $message, array $context = array()) {
        $this->output->writeln($message);
    }

}
