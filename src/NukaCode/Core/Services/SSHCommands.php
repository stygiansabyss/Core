<?php namespace NukaCode\Core\Services;

class SSHCommands {

    public function installComposerPackage($package)
    {
        $commands = [
            'cd '. base_path(),
            'composer require nukacode/'. $package .':dev-master',
            'php artisan config:publish nukacode/'. $package
        ];

        \SSH::run($commands, function ($line) {
            echo $line.PHP_EOL;
        });
    }

    public function publicPermissions()
    {
        $commands = [
            'cd '. base_path(),
            'chmod 755 public',
            'chmod 755 public/index.php'
        ];

        \SSH::run($commands, function ($line) {
            echo $line.PHP_EOL;
        });
    }

    public function installGulpDependencies()
    {
        $commands = [
            'cd '. base_path(),
            'npm install --save-dev gulp gulp-autoprefixer gulp-util gulp-notify gulp-minify-css gulp-uglify gulp-less gulp-rename gulp-concat'
        ];

        \SSH::run($commands, function($line) {
            echo $line.PHP_EOL;
        });
    }

    public function runGulpInstallTask()
    {
        $commands = [
            'cd '. base_path(),
            'gulp install'
        ];

        \SSH::run($commands, function ($line) {
            echo $line.PHP_EOL;
        });
    }
} 