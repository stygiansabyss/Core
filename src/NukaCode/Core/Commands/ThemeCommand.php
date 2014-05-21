<?php namespace NukaCode\Core\Commands;

use Illuminate\Console\Command;

class ThemeCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nuka:theme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile your theme based on your configuration.';

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
     * The config repo
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create a new command instance.
     */
    public function __construct(\NukaCode\Core\Services\SSHCommands $ssh, \Illuminate\Config\Repository $config)
    {
        parent::__construct();

        $this->ssh    = $ssh;
        $this->config = $config;
        $this->stream = fopen('php://output', 'w');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->comment('Creating your theme...');

        $theme    = $this->config->get('app.theme.style');
        $location = $this->config->get('app.theme.src');

        $this->ssh->generateTheme($theme, $location);

        $this->comment('Finished creating theme.');
    }

}
