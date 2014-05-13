<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;

class DatabaseCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nuka:database';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run the nuka migration and seeds.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(\NukaCode\Core\Services\Migrating $migrating)
	{
        $this->migrating = $migrating;
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->migrating->packageMigrations();
	}

}
