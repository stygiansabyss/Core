<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\StreamOutput;

class CleanCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nuka:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the final commands to set up the site.';

	/**
	 * The output stream for any artisan commands
	 *
	 * @var string
	 */
	protected $stream;

	/**
	 * The ssh commands instance to run against
	 *
	 * @var string
	 */
	protected $ssh;

	/**
	 * Create a new command instance.
	 */
	public function __construct(\NukaCode\Core\Services\SSHCommands $ssh)
	{
		parent::__construct();

        $this->ssh             = $ssh;
		$this->stream          = fopen('php://output', 'w');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->comment('Starting final steps...');

		// Run the installation
		$this->runArtisan();

		// Run gulp commands
		$this->runGulp();

		// Clean up
		$this->cleanUp();
	}

	/********************************************************************
	 * Unique Methods
	 *******************************************************************/
	protected function runArtisan()
	{
		$this->comment('Generating a key...');
        // Generate a new secret key for hashing
		Artisan::call('key:generate', array(), new StreamOutput($this->stream));

		$this->comment('Adding the migration table...');

        // Install the base laravel migration
		Artisan::call('migrate:install', array(), new StreamOutput($this->stream));

        // Run the NukaCode package migrations and seeds
		Artisan::call('nuka:database', array(), new StreamOutput($this->stream));

        // Run the gulp task 'install' to generate master.css and all.js
		Artisan::call('nuka:gulp', array(), new StreamOutput($this->stream));
	}

	protected function runGulp()
	{
		$this->comment('Running gulp commands...');
		$this->ssh->runGulpInstallTask();
	}

	protected function cleanUp()
	{
		$this->comment('Running clean up commands...');
        $this->ssh->publicPermissions();
	}

}
