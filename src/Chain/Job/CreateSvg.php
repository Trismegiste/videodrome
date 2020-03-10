<?php

namespace Trismegiste\Videodrome\Chain\Job;

use Trismegiste\Videodrome\Chain\FileJob;
use Trismegiste\Videodrome\Chain\JobException;
use Trismegiste\Videodrome\Chain\Media;
use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Chain\MediaList;

/**
 * Generates a SVG from a template
 */
class CreateSvg extends FileJob {

    protected function process(Media $filename): Media {
        if ($filename->isLeaf()) {
            throw new JobException("Fail");
        }

        $metadata = $filename->getMetadataSet();
        $assets = $metadata['name'];
        $output = new MediaList([], $filename->getMetadataSet());
        foreach ($assets as $key) {
            $output[] = $this->writeSvg($key, $metadata['width'], $metadata['height'], $metadata['folder']);
        }

        return $output;
    }

    protected function writeSvg(string $key, int $width, int $height, string $folder): MediaFile {
        $data = [
            'width' => $width,
            'height' => $height,
            'title' => strtoupper($key)
        ];
        // $data is used into the template below :
        ob_start();
        include join_paths(__DIR__, 'title.svg');
        $result = ob_get_clean();
        file_put_contents("$folder/$key.svg", $result);

        return new MediaFile("$folder/$key.svg");
    }

}
