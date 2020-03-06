<?php

namespace Trismegiste\Videodrome\Util;

/**
 * Meta & info on PDF using Plopper
 */
class PdfInfo {

    const dpi = 72;

    protected $pages;
    protected $width; // in pts
    protected $height; // in pts

    public function __construct(string $filename) {
        $tmp = shell_exec("pdfinfo " . $filename);

        $result = [];
        if (preg_match('/^Pages:[\s]+([\d]+)$/m', $tmp, $result)) {
            $this->pages = (int) $result[1];
        } else {
            throw new \RuntimeException("No pages count in PDF $filename");
        }

        if (preg_match('/^Page size:[\s]+([.\d]+) x ([.\d]+)[\s]+pts$/m', $tmp, $result)) {
            $this->width = (float) $result[1];
            $this->height = (float) $result[2];
        } else {
            throw new \RuntimeException("No page size in PDF $filename");
        }
    }

    public function getPageCount(): int {
        return $this->pages;
    }

    /**
     * Gets width of pdf
     * @return float width in pts
     */
    public function getWidth(): float {
        return $this->width;
    }

    /**
     * Gets height of pdf
     * @return float height in pts
     */
    public function getHeight(): float {
        return $this->height;
    }

    public function getMinDensityFor(int $w, int $h): float {
        $wdpi = self::dpi * $w / $this->width;
        $hdpi = self::dpi * $h / $this->height;

        return max([$hdpi, $wdpi]);
    }

}
