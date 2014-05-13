<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Output\StreamOutput;

class InstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nuka:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the everything needed to get a nuka site up and running.';

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
		$this->stream = fopen('php://output', 'w');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->comment('Starting site installation...');

        // Allow the user to install extra NukaCode packages
		Artisan::call('nuka:setup', array(), new StreamOutput($this->stream));

        // Handles the key generation, database migrations and seeds as well as the common error clean up
		Artisan::call('nuka:clean', array(), new StreamOutput($this->stream));

		$this->comment('Installation complete!');
	}

}
