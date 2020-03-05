<?php

namespace Trismegiste\Videodrome\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trismegiste\Videodrome\Chain\AggregateJob;
use Trismegiste\Videodrome\Chain\Job\AddingSound;
use Trismegiste\Videodrome\Chain\Job\Cutter;
use Trismegiste\Videodrome\Chain\Job\ImagePanning;
use Trismegiste\Videodrome\Chain\Job\PngOverlay;
use Trismegiste\Videodrome\Chain\Job\VideoConcat;

/**
 * Build Trailer
 */
class Trailer extends Command {

    protected function execute(InputInterface $input, OutputInterface $output) {
        FAIL
        $cor = new AddingSound(new VideoConcat(new PngOverlay(new AggregateJob([new ImagePanning(), new Cutter]))));
        $cor->execute($media);
    }

}
