<?php

namespace Trismegiste\Videodrome;

/**
 * A Task with cleaning
 */
interface Task {

    /**
     * Execute the task
     */
    public function exec();

    /**
     * Cleaning temporary files
     */
    public function clean();
}
