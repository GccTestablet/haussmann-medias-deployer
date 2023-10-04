<?php

namespace Deployer;

use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

require_once 'recipe/symfony.php';
require_once 'contrib/yarn.php';
require_once 'contrib/slack.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

set('repository', $_ENV['REPOSITORY']);

add('shared_files', []);
add('shared_dirs', [
    'public/media',
    'public/tmp',
    'public/download',
]);
set('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
]);
set('copy_dirs', [
    'node_modules',
    'vendor',
]);

set('deploy_path', function () {
    $path = $_ENV['DEPLOY_PATH'] ?? run('pwd');

    return $path . '/{{hostname}}';
});
set('release_name', function () {
    return date('YmdHis');
});

import('config/config.yaml');

# Hosts
import('config/hosts.yaml');

# Tasks
task('what_branch', function () {
    $selectedBranch = get('branch');
    if (!$selectedBranch) {
        $selectedBranch = ask('What branch to deploy?', 'main');
    }

    set('branch', $selectedBranch);
});

task('yarn:build', function () {
    cd('{{release_path}}');
    run('bin/phing yarn_build');
});

# Hooks
import('config/hooks.yaml');
