<?php
namespace Deployer;
require 'recipe/codeigniter.php';

// Configuration

set('repository', 'git@gitlab.websearchpro.net:php/bip.git');
set('keep_releases', 5);

add('shared_files', []);
add('shared_dirs', ['cache','images/uploads','assets/sound_files']);

add('writable_dirs', ['cache','images/uploads','assets/sound_files']);

// Servers

server('staging', '192.168.1.3')
    ->user('ubuntu')
    ->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')
    ->set('branch', 'production')
    ->set('deploy_path', '/tmp/deploy')
    ->stage('stage');

server('production', 'k8bip01.ki.se')
    ->user('bjosod')
    ->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')
    ->set('branch', 'stage-v5')
    ->set('deploy_path', '/tmp/deploy')
    ->stage('prod');

set('default_stage', 'prod');

// Tasks

/*desc('Deploy bip to production');
task('bip-to-production', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php-fpm.service');
});
after('deploy:symlink', 'bip-to-production');*/

task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: $result");
});
