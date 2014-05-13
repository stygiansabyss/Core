<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\StreamOutput;

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
	 * An object containing the core config details
	 *
	 * @var string[]
	 */
	protected $coreDetails;

	/**
	 * An object containing the chat config details
	 *
	 * @var string[]
	 */
	protected $chatDetails;

	/**
	 * The JSON object for the chat config
	 *
	 * @var string
	 */
	protected $chatConfig;

	/**
	 * An object containing the forum config details
	 *
	 * @var string[]
	 */
	protected $forumDetails;

	/**
	 * An object containing the steam config details
	 *
	 * @var string[]
	 */
	protected $steamDetails;

	/**
	 * The output stream for any artisan commands
	 *
	 * @var string
	 */
	protected $stream;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->coreDetails  = new stdClass();
		$this->chatDetails  = new stdClass();
		$this->forumDetails = new stdClass();
		$this->steamDetails = new stdClass();
		$this->stream       = fopen('php://output', 'w');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->comment('Starting nuka configuration...');

		// Set up the configs
		$this->setUpCore();

		// Get out nuka packages
		$this->updatenuka();

		$this->comment('nuka configuration complete!');
	}

	/********************************************************************
	 * Unique Methods
	 *******************************************************************/
	protected function confirmConfig($type)
	{
		$this->line('Your '. $type .' configuration will be set to the following.');

		switch ($type) {
			case 'core':
				$this->line(print_r($this->coreDetails, 1));
				if (!$this->confirm('Do you want to keep this configuration? [yes|no]')) {
					return $this->setUpCore();
				} else {
					return $this->configureCore();
				}
			break;
			case 'chat':
				$this->line($this->chatConfig ."\n");
				if (!$this->confirm('Do you want to keep this configuration? [yes|no]')) {
					return $this->setUpChat();
				} else {
					return $this->configureChat();
				}
			break;
			case 'forum':
				$this->line($this->forumDetails ."\n");
				if (!$this->confirm('Do you want to keep this configuration? [yes|no]')) {
					return $this->setUpForum();
				} else {
					return $this->configureForum();
				}
			break;
			case 'steam':
				$this->line($this->steamDetails ."\n");
				if (!$this->confirm('Do you want to keep this configuration? [yes|no]')) {
					return $this->setUpSteam();
				} else {
					return $this->configureSteam();
				}
			break;
		}
	}

	protected function updatenuka()
	{
		$this->comment('Starting nuka package options...');
		foreach ($this->packages as $package) {
			if ($this->confirm('Do you wish to install nuka\\'. $package .'? [yes|no]')) {
				$this->comment('Installing nuka\\'. $package .'...');

				$commands = [
					'cd '. base_path(),
					'composer require nuka/'. $package .':dev-master',
					'php artisan config:publish nuka/'. $package
				];

				SSH::run($commands, function ($line) {
					echo $line.PHP_EOL;
				});

				$this->setUpnuka($package);

				$this->comment('Install of nuka\\'. $package .' complete!');
			}
		}
	}

	/********************************************************************
	 * Set Up Methods
	 *******************************************************************/

	protected function setUpnuka($package)
	{
		switch ($package) {
			case 'chat':
				return $this->setUpChat();
			break;
			case 'steam-api':
				return $this->setUpSteam();
			break;
		}
	}

	protected function setUpCore()
	{
		// Set up our nuka config
		$this->comment('Setting up core details...');
		$this->coreDetails->controlRoomDetail = $this->ask('What is this site\'s control room name?');
		$this->coreDetails->siteName          = $this->ask('What is this name to display for this site?');
		$this->coreDetails->siteIcon          = $this->ask('What is this icon to display for this site? (Use tha last part of the font-awesome icon class)');
		$this->coreDetails->menu              = $this->ask('What is menu style should this site default to? (utopian or twitter)');

		$this->confirmConfig('core');
	}

	protected function setUpChat()
	{
		// Set up our nuka config
		$this->comment('Setting up chat details...');
		$this->chatDetails->debug             = $this->confirm('Should the chats show debug info?  [Hit enter to leave as true]', true) ? true : false;
		$this->chatDetails->port              = $this->ask('What is the chat port?  [Hit enter to leave as 1337]', 1337);
		$this->chatDetails->backLog           = $this->ask('How much back log should the chats get?  [Hit enter to leave as 100]', 100);
		$this->chatDetails->backFill          = $this->ask('How much should the chats backfil on connect?  [Hit enter to leave as 30]', 30);
		$this->chatDetails->apiEndPoint       = $this->ask('What is the chat url?');
		$this->chatDetails->connectionMessage = $this->confirm('Should the chats show a connection message?  [Hit enter to leave as true]', true) ? true : false;

		$this->chatConfig = json_encode($this->chatDetails, JSON_PRETTY_PRINT);

		$this->confirmConfig('chat');
	}

	protected function setUpForum()
	{
		// Set up our nuka config
		$this->comment('Setting up forum details...');
		$this->forumDetails->forumNews = $this->confirm('Will this site use forum posts on the front page?  [Hit enter to leave as true]', true) ? true : false;

		$this->confirmConfig('forum');
	}

	protected function setUpSteam()
	{
		// Set up our nuka config
		$this->comment('Setting up steam api details...');
		$this->steamDetails->steamApiKey = $this->ask('What is your steam api key?');

		while ($this->steamDetails->steamApiKey == null) {
			$this->steamDetails->steamApiKey = $this->ask('This cannot be empty.  What is your steam api key?');
		}

		$this->confirmConfig('steam');
	}

	/********************************************************************
	 * Configuration Methods
	 *******************************************************************/

	protected function configureCore()
	{
		list($path, $contents) = $this->getConfig('packages/nuka/core/config.php');

		foreach ($this->coreDetails as $key => $value) {
			$contents = str_replace($this->laravel['config']['core::'. $key], $value, $contents);
		}

		File::put($path, $contents);
	}

	protected function configureChat()
	{
		list($path, $contents) = $this->getConfig('packages/nuka/chat/chatConfig.json');
		File::put($path, $this->chatConfig);
	}

	protected function configureForum()
	{
		list($path, $contents) = $this->getConfig('packages/nuka/forum/config.php');

		foreach ($this->forumDetails as $key => $value) {
			$contents = str_replace($this->laravel['config']['forum::'. $key], $value, $contents);
		}

		File::put($path, $contents);
	}

	protected function configureSteam()
	{
		list($path, $contents) = $this->getConfig('packages/nuka/steam-api/config.php');

		foreach ($this->steamDetails as $key => $value) {
			$contents = str_replace($this->laravel['config']['steam-api::'. $key], $value, $contents);
		}

		File::put($path, $contents);
	}

	/********************************************************************
	 * Extra Methods
	 *******************************************************************/
	protected function getConfig($file)
	{
		$path = $this->laravel['path'].'/config/'. $file;

		$contents = File::get($path);

		return array($path, $contents);
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
