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

//phpinfo(); exit();
//ini_set("display_errors", "on");

use gnekoz\configuration\Reader;
use gnekoz\ClassLoader;
use gnekoz\http\Request;

class Core
{
	/**
	 * Libraries directory name
	 * @var string
	 */
	const LIB_DIR = "lib";


	/**
	 * Applications directory name
	 * @var string
	 */
	const APPS_DIR = "applications";


	/**
	 * Root directory path
	 * @var string
	 */
	private static $rootDir;

	/**
	 * Application directory path
	 * @var string
	 */
	private static $appsDir;

	/**
	 * Libraries directory path
	 * @var string
	 */
	private static $libDir;


	private static $applications = array();


	private static $classLoader;


	/**
	 * Array of searching paths for class autoloader
	 * @var array
	 */
	private static $classPaths = array();


	/**
	 * Framework boot routine
	 */
	public static function boot()
	{
		// Framework base paths resolution
		self::$rootDir = realpath(dirname(__FILE__)
		                 .DIRECTORY_SEPARATOR.".."
		                 .DIRECTORY_SEPARATOR."..");

		self::$libDir = self::$rootDir . DIRECTORY_SEPARATOR . self::LIB_DIR;

		self::$appsDir = self::$rootDir . DIRECTORY_SEPARATOR . self::APPS_DIR;


		// Initialize autoloader with basic configuration
		require_once self::$libDir.DIRECTORY_SEPARATOR."gnekoz".DIRECTORY_SEPARATOR."ClassLoader.php";
		self::$classLoader = new ClassLoader();
		self::$classLoader->addClassPath(self::$libDir);

		//echo "Boot terminato";

		self::handleRequest();
	}


	/**
	 * Handle HTTP request
	 *
	 * This method is intented for standard stateless applications. The
	 * HTTP request is handled by application's bootloader.php which invokes
	 * Core boot. All the framework's components are resolved and loaded - and
	 * applications are discovered - from that request.
	 *
	 * I suppose that in a fastcgi implementation the sequence will be the
	 * opposite: Core will handle and dispatch the request
	 */
	private static function handleRequest()
	{
		// Resolve invoked app
		$bootloaderFile = $_SERVER['SCRIPT_FILENAME'];
		$bootloaderUrl = $_SERVER['SCRIPT_NAME'];

		$appRoot = realpath(dirname($bootloaderFile) . DIRECTORY_SEPARATOR . "..");
		$appName = basename($appRoot);
		$appWebRoot = substr($bootloaderUrl, 0, strrpos($bootloaderUrl, '/'));

		// Create instance of application's App component
		require_once($appRoot.DIRECTORY_SEPARATOR."App.php");
		$appClass = "$appName\\App";
		$app = new $appClass($appName, $appRoot, $appWebRoot);


		Application::addApplication($app);
// 		// This doesn't make much sense in this context...
// 		self::$applications[$appName] = $app;

		// TODO move this code to Application class
		$url = array();
		if (!preg_match("/^\\$appWebRoot(.*)$/", $_SERVER['REQUEST_URI'], $url))
		{
			ob_clean();
			header('HTTP/1.0 404 Not Found');
			exit(1);
		}
		$req = new Request($url[1]);
		$app->handleRequest($req);
	}



	/**
	 * Return the absolute path of root directory
	 */
	public static function getRootDir()
	{
		return self::$rootDir;
	}


	/**
	 * Return the absolute path of applications directory
	 */
	public static function getApplicationsDir()
	{
		return self::$appsDir;
	}


	/**
	 * Return the absolute path of the libraries directory
	 */
	public static function getLibraryDir()
	{
		return self::$libDir;
	}
}

// Bootstrap
Core::boot();