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

class ClassLoader
{
	private $classPaths = array();

	public function __construct()
	{
		spl_autoload_register(array($this, 'loadClass'));
	}


	/**
	 * Register additional searching path for class lookup
	 * @param string $path - absolute path
	 */
	public function addClassPath($path)
	{
		if (substr($path, strlen($path) - 1, 1) != DIRECTORY_SEPARATOR) {
			$path .= DIRECTORY_SEPARATOR;
		}
		$this->classPaths[] = $path;
	}


	/**
	 * Search and include class definition file
	 * @param string $className
	 */
	public function loadClass($className)
	{
		// Transforms full class name to path
		$className = ltrim($className, '\\');
		$path = str_replace("\\", DIRECTORY_SEPARATOR, $className);
		$path = str_replace("_", DIRECTORY_SEPARATOR, $path);

		// Gets class name and relative path
		$className = basename($path);
		$path = dirname($path) . DIRECTORY_SEPARATOR;

		// Guess class definition file
		if (self::findClassFile("{$path}{$className}.php")) return;
		if (self::findClassFile("{$path}{$className}.class.php")) return;
		if (self::findClassFile("{$path}{$className}.interface.php")) return;

		// Exception for PHPMailer (why!? why!??)
		$className = strtolower($className);
		if (self::findClassFile("{$path}class.{$className}.php")) return;

	}


	/**
	 * Check class file against registered search paths
	 * @param string $classFile
	 */
	private function findClassFile($classFile)
	{
		foreach ($this->classPaths as $path) {
			if (is_readable($path.$classFile)) {
				include_once $path.$classFile;
				return true;
			}
		}
		return false;
	}
}