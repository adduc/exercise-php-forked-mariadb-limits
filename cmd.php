#!/usr/bin/env php
<?php declare(strict_types=1);

(new class {
    const FORK_LIMIT = 100;

    public function __invoke()
    {

        $db = new PDO('mysql:host=172.17.0.1', 'root', 'password');
        $db->exec("CREATE DATABASE IF NOT EXISTS test");
        unset($db);

        foreach (range(1, self::FORK_LIMIT) as $i) {
            switch($pid = pcntl_fork()) {
                case -1:
                    fwrite(STDERR, "Could not fork");
                    exit(1);
                case 0:
                    return $this->createTable($i);
                    // return $this->createDatabase($i);
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
        $sql = "CREATE DATABASE test_%d_%d";
        $msg = "$i: %d\n";
        $j = 0;

        while (true) {
            $db->exec(sprintf($sql, $i, ++$j));

            if ($j % 1000 == 0) {
                printf($msg, $j);
            }
        }
    }

    public function createTable($i)
    {
        $db = new PDO('mysql:host=172.17.0.1', 'root', 'password');
        $sql = '
            CREATE TABLE test.a_test_%s_%s (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `owner_id` bigint(20) DEFAULT NULL,
                `lower_name` varchar(255) NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `website` varchar(2048) DEFAULT NULL,
                `original_service_type` int(11) DEFAULT NULL,
                `original_url` varchar(2048) DEFAULT NULL,
                `default_branch` varchar(255) DEFAULT NULL,
                `num_watches` int(11) DEFAULT NULL,
                `num_stars` int(11) DEFAULT NULL,
                `num_forks` int(11) DEFAULT NULL,
                `num_issues` int(11) DEFAULT NULL,
                `num_closed_issues` int(11) DEFAULT NULL,
                `num_pulls` int(11) DEFAULT NULL,
                `num_closed_pulls` int(11) DEFAULT NULL,
                `num_milestones` int(11) NOT NULL DEFAULT 0,
                `num_closed_milestones` int(11) NOT NULL DEFAULT 0,
                `is_private` tinyint(1) DEFAULT NULL,
                `is_empty` tinyint(1) DEFAULT NULL,
                `is_archived` tinyint(1) DEFAULT NULL,
                `is_mirror` tinyint(1) DEFAULT NULL,
                `status` int(11) NOT NULL DEFAULT 0,
                `is_fork` tinyint(1) NOT NULL DEFAULT 0,
                `fork_id` bigint(20) DEFAULT NULL,
                `is_template` tinyint(1) NOT NULL DEFAULT 0,
                `template_id` bigint(20) DEFAULT NULL,
                `size` bigint(20) NOT NULL DEFAULT 0,
                `is_fsck_enabled` tinyint(1) NOT NULL DEFAULT 1,
                `close_issues_via_commit_in_any_branch` tinyint(1) NOT NULL DEFAULT 0,
                `topics` text DEFAULT NULL,
                `avatar` varchar(64) DEFAULT NULL,
                `created_unix` bigint(20) DEFAULT NULL,
                `updated_unix` bigint(20) DEFAULT NULL,
                `owner_name` varchar(255) DEFAULT NULL,
                `num_projects` int(11) NOT NULL DEFAULT 0,
                `num_closed_projects` int(11) NOT NULL DEFAULT 0,
                `trust_model` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `UQE_repository_s` (`owner_id`,`lower_name`),
                KEY `IDX_repository_lower_name` (`lower_name`),
                KEY `IDX_repository_owner_id` (`owner_id`),
                KEY `IDX_repository_is_fork` (`is_fork`),
                KEY `IDX_repository_template_id` (`template_id`),
                KEY `IDX_repository_created_unix` (`created_unix`),
                KEY `IDX_repository_updated_unix` (`updated_unix`),
                KEY `IDX_repository_is_empty` (`is_empty`),
                KEY `IDX_repository_is_archived` (`is_archived`),
                KEY `IDX_repository_name` (`name`),
                KEY `IDX_repository_is_private` (`is_private`),
                KEY `IDX_repository_is_mirror` (`is_mirror`),
                KEY `IDX_repository_fork_id` (`fork_id`),
                KEY `IDX_repository_is_template` (`is_template`),
                KEY `IDX_repository_original_service_type` (`original_service_type`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;    
        ';
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

