<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nuka:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install and configure nuka packages.';

	/**
	 * An array of available nuka packages
	 *
	 * @var string[]
	 */
	protected $packages = ['chat', 'forum', 'steam-api'];

    /**
     * The ssh commands instance to run against
     *
     * @var string
     */
    protected $ssh;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
    public function __construct(\NukaCode\Core\Services\SSHCommands $ssh)
    {
        parent::__construct();

        $this->ssh = $ssh;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->comment('Starting NukaCode package options...');

		// Get out nuka packages
		$this->packageOptions();

		$this->comment('NukaCode package setup complete!');
	}

	/********************************************************************
	 * Unique Methods
	 *******************************************************************/
	protected function packageOptions()
	{
		$this->comment('Starting NukaCode package options...');
		foreach ($this->packages as $package) {
			if ($this->confirm('Do you wish to install NukaCode\\'. $package .'? [yes|no]')) {
				$this->comment('Installing NukaCode\\'. $package .'...');

                $this->ssh->installComposerPackage($package);

				$this->comment('Install of nukacode\\'. $package .' complete!');
			}
		}
	}

}
