<?php namespace NukaCode\Core\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	const version = '1.0.0';

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('nukacode/core');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->shareWithApp();
		$this->loadConfig();
		$this->registerViews();
		$this->registerAliases();
	}

	/**
	 * Share the package with application
	 *
	 * @return void
	 */
	protected function shareWithApp()
	{
		$this->app['core'] = $this->app->share(function($app)
		{
			return true;
		});
	}

	/**
	 * Load the config for the package
	 *
	 * @return void
	 */
	protected function loadConfig()
	{
		$this->app['config']->package('nukacode/core', __DIR__.'/../../../config');
	}

	/**
	 * Register views
	 *
	 * @return void
	 */
	protected function registerViews()
	{
		$this->app['view']->addNamespace('core', __DIR__.'/../../../views');
	}

	/**
	 * Register aliases
	 *
	 * @return void
	 */
	protected function registerAliases()
	{
		$aliases = [
			'HTML'                        => 'NukaCode\Core\Facades\HTML',
			'View'                        => 'NukaCode\Core\Facades\View',
			'Mobile'                      => 'NukaCode\Core\Facades\Mobile',
			'CoreView'                    => 'NukaCode\Core\Facades\CoreView',
			'CoreImage'                   => 'NukaCode\Core\Facades\CoreImage',
			'Crud'                        => 'NukaCode\Core\Facades\Crud',
			'Wizard'                      => 'NukaCode\Core\Facades\Wizard',
			'LeftTab'                     => 'NukaCode\Core\Facades\LeftTab',
			'bForm'                       => 'NukaCode\Core\Facades\bForm',
			'Ajax'                        => 'NukaCode\Core\Facades\Ajax',
			'Post'                        => 'NukaCode\Core\Facades\Post',
			'BBCode'                      => 'NukaCode\Core\Facades\BBCode',
			'Message'                     => 'NukaCode\Core\Models\Message',
			'Message_Folder'              => 'NukaCode\Core\Models\Message\Folder',
			'Message_Folder_Message'      => 'NukaCode\Core\Models\Message\Folder\Message',
			'Message_Type'                => 'NukaCode\Core\Models\Message\Type',
			'Message_User_Delete'         => 'NukaCode\Core\Models\Message\User\Delete',
			'Message_User_Read'           => 'NukaCode\Core\Models\Message\User\Read',
			'User_Preference'             => 'NukaCode\Core\Models\User\Preference',
			'User_Preference_User'        => 'NukaCode\Core\Models\User\Preference\User',
			'User_Permission_Action'      => 'NukaCode\Core\Models\User\Permission\Action',
			'User_Permission_Action_Role' => 'NukaCode\Core\Models\User\Permission\Action\Role',
			'User_Permission_Role'        => 'NukaCode\Core\Models\User\Permission\Role',
			'User_Permission_Role_User'   => 'NukaCode\Core\Models\User\Permission\Role\User',
			'Seed'                        => 'NukaCode\Core\Models\Seed',
			'Migration'                   => 'NukaCode\Core\Models\Migration',
            'Utility_Collection'          => 'NukaCode\Core\Database\Collection',
			// 'Control_Exception'           => 'NukaCode\Core\Control_Exception',
		];

		$appAliases = \Config::get('core::nonCoreAliases');
		$loader     = \Illuminate\Foundation\AliasLoader::getInstance();

		foreach ($aliases as $alias => $class) {
			if (!is_null($appAliases)) {
				if (!in_array($alias, $appAliases)) {
					$loader->alias($alias, $class);
				}
			} else {
				$loader->alias($alias, $class);
			}
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}