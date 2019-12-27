<?php

namespace Trismegiste\Videodrome;

/**
 * A Task with cleaning
 */
interface Task {

    public function exec();

    public function clean();
}
