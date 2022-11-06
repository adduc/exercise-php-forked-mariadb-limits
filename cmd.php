#!/usr/bin/env php
<?php declare(strict_types=1);

(new class {
    const FORK_LIMIT = 16;

    public function __invoke()
    {
        foreach (range(1, self::FORK_LIMIT) as $i) {
            switch($pid = pcntl_fork()) {
                case -1:
                    fwrite(STDERR, "Could not fork");
                    exit(1);
                case 0:
                    return $this->createDatabase($i);
                default:
                    $pids[] = $pid;
            }
        }

        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }
    }

    public function createDatabase($i)
    {
        $db = new PDO('mysql:host=172.17.0.1', 'root', 'password');
        $sql = "CREATE DATABASE %d_%d";
        $msg = "$i: %d\n";
        $j = 0;

        while (true) {
            $db->exec(sprintf($sql, $i, ++$j));

            if ($j % 1000 == 0) {
                printf($msg, $j);
            }
        }
    }
})();

