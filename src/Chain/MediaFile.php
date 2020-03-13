<?php

namespace Trismegiste\Videodrome\Chain;

/**
 * An existing file with metadata
 */
class MediaFile extends \SplFileInfo implements Media {

    use MetaImpl;

    public function __construct(string $file_name, array $meta = []) {
        if (!file_exists($file_name)) {
            throw new \RuntimeException("$file_name does not exist");
        }
        parent::__construct($file_name);
        $this->metadata = $meta;
    }

    /**
     * Is this Media a picture ?
     * @return bool
     */
    public function isPicture(): bool {
        return preg_match('#^image/.+#', mime_content_type((string) $this));
    }

    /**
     * Is this Media a video ?
     * @return bool
     */
    public function isVideo(): bool {
        $mime = mime_content_type((string) $this);

        if (preg_match('#^video/.+#', $mime)) {
            return true;
        }
        // this is a patch for non-specified mimetypes in some linux :
        if (($mime === 'application/octet-stream') && in_array($this->getExtension(), ['3gp'])) {
            return true;
        }

        return false;
    }

    /**
     * Gets the filename of Media without extension
     * @return string
     */
    public function getFilenameNoExtension(): string {
        return $this->getBasename('.' . $this->getExtension());
    }

    /** @see Media */
    public function isLeaf(): bool {
        return true;
    }

    /** @see Media */
    public function unlink() {
        unlink($this);
    }

}
