<?php

namespace videodrome;

/**
 * ImpressToPdf convert an Impress file to a PDF
 */
class ImpressToPdf implements Task {

    public function exec(array $param) {
        echo "coucou";
    }

    public function clean() {
        echo "clean";
    }

}
