<?php namespace NukaCode\Core\Services;

class SSHCommands {

    public function generateTheme($theme, $location)
    {
        switch ($location) {
            case 'local':
                $directory = 'app/assets/less';
                break;
            case 'vendor':
                $directory = 'vendor/nukacode/core/assets/less';
                break;
            default:
                throw new \Exception('Unrecognized src [' . $location . '] provided.');
                break;
        }

        if ($theme == 'default') {
            $commands = [
                'cd ' . base_path(),
                'lessc '. $directory .'/master.less public/css/master.css',
                'gulp css-mini'
            ];
        } else {
            if (!\File::exists($directory .'/themes/' . $theme)) {
                throw new \Exception('Theme directory [' . $theme . '] does not exist.');
            }

            $commands = [
                'cd ' . base_path(),
                'lessc '. $directory .'/themes/' . $theme . '/master.less public/css/master.css',
                'gulp css-mini'
            ];
        }

        \SSH::run($commands, function ($line) {
            echo $line . PHP_EOL;
        });
    }

    public function installComposerPackage($package)
    {
        $commands = [
            'cd ' . base_path(),
            'composer require nukacode/' . $package . ':dev-master',
            'php artisan config:publish nukacode/' . $package
        ];

        \SSH::run($commands, function ($line) {
            echo $line . PHP_EOL;
        });
    }

    public function publicPermissions()
    {
        $commands = [
            'cd ' . base_path(),
            'chmod 755 public',
            'chmod 755 public/index.php'
        ];

        \SSH::run($commands, function ($line) {
            echo $line . PHP_EOL;
        });
    }

    public function installGulpDependencies()
    {
        $commands = [
            'cd ' . base_path(),
            'npm install --save-dev gulp gulp-autoprefixer gulp-util gulp-notify gulp-minify-css gulp-uglify gulp-less gulp-rename gulp-concat'
        ];

        \SSH::run($commands, function ($line) {
            echo $line . PHP_EOL;
        });
    }

    public function runGulpInstallTask()
    {
        $commands = [
            'cd ' . base_path(),
            'gulp install'
        ];

        \SSH::run($commands, function ($line) {
            echo $line . PHP_EOL;
        });
    }
} 