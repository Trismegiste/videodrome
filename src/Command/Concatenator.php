<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Trismegiste\Videodrome\Chain\ConsoleLogger;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;

/**
 * Concat video with a marker file for sorting
 */
class Concatenator extends Command {

    protected static $defaultName = 'trailer:concat';

    protected function configure() {
        $this->setDescription("Concat video")
                ->addArgument('video', InputArgument::REQUIRED, "Folder full of video")
                ->addArgument('marker', InputArgument::REQUIRED, "Audacity Timecode Marker from the sound file");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $sorting = $this->getSequence($input->getArgument('marker'));
        $finder = new Finder();
        $finder->in($input->getArgument('video'))->files()->name('/-(cut|extended)-over.avi$/');
        $videoList = iterator_to_array($finder->getIterator());
        if (count($videoList) !== count($sorting)) {
            throw new RuntimeException("Count mismatch between marker file and video count");
        }

        $sortedList = [];
        foreach ($sorting as $key) {
            foreach ($videoList as $vid) {
                if (preg_match("/^$key-(cut|extended)-over.avi$/", $vid->getFilename())) {
                    $sortedList[] = (string) $vid;
                }
            }
        }

        $output->writeln("Concat video");
        $cor = new VideoConcat();
        $cor->setLogger(new ConsoleLogger($output));
        $cor->execute($sortedList);
    }

    protected function getSequence($markerFile) {
        if (!file_exists($markerFile)) {
            throw new RuntimeException("Marker file not found");
        }
        $timing = file($markerFile);
        $timecode = [];
        foreach ($timing as $clip) {
            if (preg_match("/^([.0-9]+)\s([.0-9]+)\s([^\s]+)$/", $clip, $extract)) {
                $timecode[] = $extract[3];
            }
        }

        return $timecode;
    }

}
