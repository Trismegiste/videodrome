<?php

namespace videodrome;

/**
 * A Task with cleaning
 */
interface Task {

    public function exec(array $param);

    public function clean();
}
