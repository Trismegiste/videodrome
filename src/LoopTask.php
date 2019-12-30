<?php

namespace Trismegiste\Videodrome;

/**
 * A loop of Tasks
 */
class LoopTask implements Task {

    private $task;

    /**
     * Constructor for the loop
     * 
     * @param array $task an array of Tasks
     */
    public function __construct(array $task = []) {
        $this->task = $task;
    }

    /**
     * Adds a Task
     * 
     * @param \Trismegiste\Videodrome\Task $t
     */
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
