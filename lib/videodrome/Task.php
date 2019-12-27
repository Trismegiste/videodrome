<?php

namespace videodrome;

/**
 * A Task with cleaning
 */
interface Task {

    public function exec();

    public function clean();
}
