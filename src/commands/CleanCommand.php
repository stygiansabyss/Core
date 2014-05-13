<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
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
	 * An array of available nuka packages
	 *
	 * @var string[]
	 */
	protected $nukaPackages = ['chat', 'forum'];

	/**
	 * An array of packages that will need a config loaded
	 *
	 * @var string[]
	 */
	protected $nukaPackagesWithConfig = ['chat'];

	/**
	 * An object containing the core nuka config details
	 *
	 * @var string[]
	 */
	protected $nukaCoreDetails;

	/**
	 * The output stream for any artisan commands
	 *
	 * @var string
	 */
	protected $stream;

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->nukaCoreDetails = new stdClass();
		$this->stream            = fopen('php://output', 'w');
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
		Artisan::call('key:generate', array(), new StreamOutput($this->stream));
		$this->comment('Adding the migration table...');
		Artisan::call('migrate:install', array(), new StreamOutput($this->stream));
		Artisan::call('nuka:database', array(), new StreamOutput($this->stream));
		Artisan::call('nuka:gulp', array(), new StreamOutput($this->stream));
	}

	protected function runGulp()
	{
		$this->comment('Running gulp commands...');
		$commands = [
			'cd '. base_path(),
			'gulp install'
		];

		SSH::run($commands, function ($line) {
			echo $line.PHP_EOL;
		});
	}

	protected function cleanUp()
	{
		$this->comment('Running clean up commands...');
		$commands = [
			'cd '. base_path(),
			'chmod 755 public',
			'chmod 755 public/index.php'
		];

		SSH::run($commands, function ($line) {
			echo $line.PHP_EOL;
		});
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
