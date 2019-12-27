<?php

namespace videodrome;

/**
 * Description of LoopTask
 */
class LoopTask implements Task {

    private $task;

    public function __construct(array $task = []) {
        $this->task = $task;
    }

    public function push(Task $t) {
        $this->task[] = $t;
    }

    public function clean() {
        foreach ($this->task as $t) {
            $t->clean();
        }
    }

    public function exec() {
        foreach ($this->task as $t) {
            $t->exec();
        }
    }

}
