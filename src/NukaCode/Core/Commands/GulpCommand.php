<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;

class GulpCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'nuka:gulp';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set up everything needed for gulp.js to work.';

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
        $this->ssh = $ssh;
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Update the ignore file
		$ignoredFiles = file(base_path('.gitignore'));

		if (!in_array('/node_modules', $ignoredFiles)) {
			$this->comment('Adding node_modules directory to .gitignore');
			\File::append(base_path('.gitignore'), "/node_modules");
		}

		$this->comment('Adding all the gulp plugins...');

        $this->ssh->installGulpDependencies();

		$this->comment('Finished adding gulp plugins.');
	}

}
