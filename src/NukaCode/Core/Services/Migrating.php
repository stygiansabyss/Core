<?php namespace NukaCode\Core\Services;

use Symfony\Component\Console\Output\StreamOutput;

class Migrating {

    public function packageMigrations ()
    {
        // Set up the variables
        $stream             = fopen('php://output', 'w');
        $nukaDirectories    = \File::directories(base_path('vendor/nukacode'));
        $migrationDirectory = '/src/database/migrations';
        $seedDirectory      = '/src/database/seeds';

        foreach ($nukaDirectories as $nukaDirectory) {
            $package = explode('/', $nukaDirectory);
            $package = end($package);

            // Handle the migrations
            if (File::exists($nukaDirectory . $migrationDirectory)) {
                // Set up a migration location artisan can use
                $migrationLocation = str_replace(base_path() .'/', '', $nukaDirectory . $migrationDirectory);

                $this->comment('Running '. $package .' migrations...');

                // Run the migrations
                \Artisan::call('migrate', array('--path' => $migrationLocation), new StreamOutput($stream));

                $this->comment(ucwords($package) .' migrations complete!');
            }

            // Handle the seeds
            if (File::exists($nukaDirectory . $seedDirectory)) {
                $seeds = File::files($nukaDirectory . $seedDirectory);

                if (count($seeds) > 0) {
                    $this->comment('Running '. $package .' seeds...');

                    foreach ($seeds as $seed) {
                        $seeder = explode('/', $seed);
                        $seeder = str_replace('.php', '', end($seeder));

                        // Do not run for any DatabaseSeeder files
                        if (strpos($seeder, 'DatabaseSeeder') === false) {
                            // Only run if the seed is not already in the database
                            if (Seed::whereName($seeder)->first() != null) continue;

                            // Run the seed
                            \Artisan::call('db:seed', array('--class' => $seeder), new StreamOutput($stream));

                            // Add the seed to the table
                            $newSeed       = new Seed;
                            $newSeed->name = $seeder;
                            $newSeed->save();

                            $this->comment(ucwords($package) .' '. $seeder .' seeded!');
                        }
                    }

                    $this->comment(ucwords($package) .' seeds complete!');
                }
            }
        }
    }
} 