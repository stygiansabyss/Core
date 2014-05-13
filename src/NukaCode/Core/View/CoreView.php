<?php namespace NukaCode\Core\View;

use HTML,
	Route,
	Request,
	NukaCode\Core\Facades\View as View,
	Config,
	App,
	Session,
	File,
	Auth;
use NukaCode\Core\Services\Mobile;
use Github\HttpClient\CachedHttpClient;

class CoreView {

	public $route;

	public $routeParts;

	public $layout;

	public $menu;

	public $mobile;

	public $activeUser = null;

	public $errors;

	public $skipView = false;

	public $hasView = false;

	public $data = array();

	public $js = array();

	public $onReadyJs = array();

	public $jsInclude = array();

	public $css = array();

	/**
	 * Layouts array
	 *
	 * @var string[] $layouts Array of layout templates
	 */
	protected $layoutOptions = array(
		'default' => 'layouts.default',
		'ajax'    => 'layouts.ajax',
		'rss'     => 'layouts.rss'
	);

	public function setUp()
	{
		// Clean the route
		$this->cleanRoute();

		// Set up the active user
		$this->activeUser();

		// Determine if we are mobile
		$this->mobile = Mobile::is_mobile();

		// Set up the layout
		if ( is_null($this->layout) ) {
			if ( Request::ajax()) {
				$this->layout = View::make($this->layoutOptions['ajax']);
			} else {
				$this->layout = View::make($this->layoutOptions['default']);
			}
		} else {
			$this->layout = View::make($this->layout);
		}

		return $this;
	}

	public function get()
	{
		return $this;
	}

	public function make()
	{
		if (strpos($this->route, 'missingmethod') === false) {
			$this->makeView();
		}

		return $this;
	}

	public function makeView()
	{
		View::share('menu', $this->menu);
		View::share('mobile', $this->mobile);
		View::share('activeUser', $this->activeUser);
		View::share('jsInclude', $this->jsInclude);
		View::share('onReadyJs', $this->onReadyJs);
		View::share('js', $this->js);
		View::share('css', $this->css);
		View::share('content', null);

		if (!$this->skipView && View::checkView($this->route)) {
			$this->layout->content = View::make($this->route)->with($this->data);

			$this->hasView = true;
		} elseif (!View::checkView($this->route)) {
			$this->errors['noView'] = $this->route;
		}
	}

	public function activeUser()
	{
		// Login required options
		if (Auth::check()) {
			if (!Session::has('activeUser')) {
				Session::put('activeUser', Auth::user());
			}
			$this->activeUser = Session::get('activeUser');
			$this->activeUser->updateLastActive();
		}
	}

	public function missingMethod($method)
	{
		$this->route = str_ireplace('missingMethod', $method, $this->route);

		return $this;
	}

	public function setActiveUser($activeUser)
	{
		$this->addData('activeUser', $activeUser);
		$this->activeUser = $activeUser;

		return $this;
	}

	public function getActiveUser()
	{
		return $this->activeUser;
	}

	public function setMenu($menu)
	{
		$this->menu = $menu;

		return $this;
	}

	public function skipView()
	{
		$this->skipView = true;

		return $this;
	}

	public function addData($key, $value)
	{
		$this->data[$key] = $value;

		$content = array();
		$content[$key] = $value;

		if ($this->hasView) {
			$this->layout->content->with($content);
		}

		return $this;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setPageTitle($pageTitle)
	{
		$this->layout->pageTitle = $pageTitle;

		return $this;
	}

	public function setRoute($route)
	{
		$this->route = $route;

		if (View::checkView($this->route)) {
			unset($this->errors['noview']);
		}

		return $this->make();
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function getRoute()
	{
		return $this->route;
	}

	public function getRouteParts()
	{
		return $this->routeParts;
	}

	// need to remove this. Moved to local.php
	protected function cleanRoute()
	{
		// Format a proper route for view to use
        $route         = explode('\\', Route::currentRouteAction());
        $route         = end($route);
		$route         = str_replace('_', '.', $route);
		$routeParts    = explode('@', $route);
		$routeParts[1] = preg_replace('/^get/', '', $routeParts[1]);
		$routeParts[1] = preg_replace('/^post/', '', $routeParts[1]);
		$route         = strtolower(str_replace(array('Controller'), '', implode('.', $routeParts)));

		$prefix = 'core.';

		if (substr($route, 0, strlen($prefix)) == $prefix) {
			$route = substr($route, strlen($prefix));
		}

		$this->route     = $route;
		$this->routParts = explode('.', $route);
	}

	public static function arrayToSelect($array, $key = 'id', $value = 'name', $first = 'Select One')
	{
		if ($first != false) {
			$results = array(
				$first
			);
		}
		foreach ($array as $item) {
			$item = (object)$item;
			$results[$item->{$key}] = stripslashes($item->{$value});
		}

		return $results;
	}

	public function addJs($newJs)
	{
		$this->js[] = $newJs;

		return $this;
	}

	public function addOnReadyJs($newJs)
	{
		$this->onReadyJs[] = $newJs;

		return $this;
	}

	public function addJsInclude($newJs)
	{
		$this->jsInclude[] = $newJs;

		return $this;
	}

	public function addCss($css)
	{
		$this->css[] = $css;

		return $this;
	}

}