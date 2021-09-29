<?php
/*
 * Gnekoz Framework for PHP applications
 * Copyright (C) 2012  Luca Stauble
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace gnekoz;

use gnekoz\configuration\Reader;
use gnekoz\routing\Router;
use gnekoz\routing\rules\Direct;
use gnekoz\ClassLoader;
//use gnekoz\session\SessionListener;
//use gnekoz\session\SessionHandler;

abstract class Application //implements SessionListener
{
	/**
	 * Application main configuration file name
	 * @var string
	 */
	const MAIN_CONF_FILE = "main.xml";

	/**
	 * Application configuration directory name
	 * @var string
	 */
	const CONF_DIR = "conf";

	/**
	 * Application libraries directory name
	 * @var string
	 */
	const LIB_DIR = "lib";

	/**
	 * Application controllers directory name
	 * @var string
	 */
	const CONTROLLERS_DIR = "controllers";

	/**
	 * Application views directory name
	 * @var string
	 */
	const VIEWS_DIR = "views";

	/**
	 * Application resources directory name
	 * @var string
	 */
	const RES_DIR = "resources";


	const DEFAULT_CONTROLLER = "Home";

	const DEFAULT_ACTION = "index";


	private $name;

	private $srcRoot;

	private $webRoot;

	private $libDir;

	private $controllersDir;

	private $viewsDir;

	private $resourcesDir;

	private $confDir;

	private $sessions = array();

	private $configuration;

	private $classLoader;

	private $router;

	private $sessionHandler;

	private static $applications = array();

	public function __construct($name, $srcRoot, $webRoot)
	{
		$this->name = $name;
		$this->srcRoot = $srcRoot;
		$this->webRoot = $webRoot;
		$this->confDir = $srcRoot . DIRECTORY_SEPARATOR . self::CONF_DIR;
		$this->controllersDir = $srcRoot . DIRECTORY_SEPARATOR . self::CONTROLLERS_DIR;
		$this->viewsDir = $srcRoot . DIRECTORY_SEPARATOR . self::VIEWS_DIR;
		$this->libDir = $srcRoot . DIRECTORY_SEPARATOR . self::LIB_DIR;

		// Create application class loader and configure basic settings
		$this->classLoader = new ClassLoader();
		$this->classLoader->addClassPath($this->srcRoot);

		// Create application request router
		$this->router = new Router($this, new Direct());

		// Create application session handler
		//$this->sessionHandler = new SessionHandler($this);
		//$this->registerSessionHandler($this->sessionHandler);

		// Read application main configuration file
		$cf = $this->confDir.DIRECTORY_SEPARATOR.self::MAIN_CONF_FILE;
		if (is_readable($cf)) {
			$this->configuration = new Reader($cf);
			$this->configuration->addVar("app.rootdir" , $this->srcRoot);
			$this->configuration->addVar("app.libdir"  , $this->libDir);
			$this->configuration->addVar("app.confdir" , $this->confDir);
			$this->configuration->addVar("app.webroot" , $this->webRoot);

			// Set php options
			//echo "prima:".ini_get('error_reporting')."<br/>";
			$phpSettings = $this->configuration->getProperty("/php/*");
			foreach ($phpSettings as $name => $value) {
				ini_set($name, $value);
				//print_r("$name = ".ini_get($name));
			}
			//echo "dopo:".ini_get('error_reporting')."<br/>"; exit();

			// Configure autoloader for additional search path
			$libs = $this->configuration->getProperty('/libraries/library');
			$this->classLoader->addClassPath($this->libDir);
			if (is_array($libs)) {
				foreach ($libs as $lib) {
					$this->classLoader->addClassPath($this->libDir.DIRECTORY_SEPARATOR.$lib);
				}
			}
		}

		$this->onInitialize();
	}

	public function handleRequest($req)
	{
		$autoStartEnabled = (bool) ini_get('session.auto_start');
		if (!$autoStartEnabled) {
			session_start();
		}

		$this->router->dispatch($req);
	}

	public function registerSessionHandler($handler)
	{
		session_set_save_handler($handler, true);
	}

	public abstract function onInitialize();

	public abstract function onShutdown();

	public abstract function onSessionCreate($id);

	public abstract function onSessionDestroy($id);

	public abstract function onSessionRestore($id);

	public function getName()
	{
		return $this->name;
	}

	public function getSrcRoot()
	{
		return $this->srcRoot;
	}

	public function getWebRoot()
	{
		return $this->webRoot;
	}

	public function getViewsDir()
	{
		return $this->viewsDir;
	}

	public function getLibDir()
	{
		return $this->libDir;
	}

	public function getClassLoader()
	{
		return $this->classLoader;
	}

	public function bindSession($session)
	{
		$this->sessions[$session->getId()] = $session;
	}

	public function getConfiguration()
	{
		return $this->configuration;
	}

	public static function addApplication($app)
	{
		self::$applications[$app->getName()] = $app;
	}

	protected static function getApplication($name)
	{
		//print_r(self::$applications);
		if (isset(self::$applications[$name]))
		{
			return self::$applications[$name];
		}
		return null;
	}
}
