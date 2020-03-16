<?php

namespace Trismegiste\Videodrome\Chain\MediaType;

use Trismegiste\Videodrome\Chain\MediaFile;
use Trismegiste\Videodrome\Util\PdfInfo;

/**
 * A PDF Media
 */
class MediaPdf extends MediaFile {

    protected $info;

    public function __construct(string $file_name, array $meta = []) {
        parent::__construct($file_name, $meta);
        $this->info = new PdfInfo($file_name);
        // @todo Checking between count($meta['duration']) and 
        // $info->getPageCount() IF there is a duration but maybe it's too specific for PdfToPng
    }

    /**
     * Gets page count for this PDF
     * @return int
     */
    public function getPageCount(): int {
        return $this->info->getPageCount();
    }

    /**
     * Gets the duration for one page
     * @param int $idx zero-based index for this page
     * @return float duration in seconds
     * @throws \OutOfBoundsException if there is no duration metadata
     * @throws \OutOfRangeException No duration for the requested page index
     */
    public function getDurationForPage(int $idx): float {
        if (!array_key_exists('duration', $this->metadata)) {
            throw new \OutOfBoundsException('This PDF has no duration metadata');
        }

        $duration = $this->metadata['duration'];
        if (!is_array($duration) || !array_key_exists($idx, $duration)) {
            throw new \OutOfRangeException("No duration metadata for page $idx");
        }

        return $duration[$idx];
    }

    public function getMinDensityFor(int $w, int $h): float {
        return $this->info->getMinDensityFor($w, $h);
    }

}
