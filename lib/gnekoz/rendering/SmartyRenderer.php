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
namespace gnekoz\rendering;

use gnekoz\utility\NumberHelper;

use \Smarty;

class SmartyRenderer implements Renderer, TemplateRenderer, TextRenderer
{
	private static $compileDir = '';
	
	private static $cacheDir = '';
	
	private static $templateDir = '';
	
	private static $webRoot = '';
	
	private $contentType = "text/html";

	private $charset = "UTF-8";

	private $template;

	private static $engine = null;

	private $app;

	public function __construct($app) {
		$this->app = $app;
	}

	public function getContentType() {
		return $this->contentType;
	}

	public function setContentType($type) {
		$this->contentType = $type;
	}

	/**
	 * Set the path smarty template to render
	 *
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	public function render($data) {
		$this->getEngine()->clearAllAssign();

		// TODO da rimuovere!!!
		$data['_gnekozDebugInfo'] = array(
		    'memory usage' => NumberHelper::convertBtyeSize(memory_get_usage(true)),
		    'memory peak usage' => NumberHelper::convertBtyeSize(memory_get_peak_usage(true)));

		foreach ($data as $name => $val) {
			$this->getEngine()->assign($name, $val);
		}

		//$this->getEngine()->register_object($object, $object_impl);
		$this->getEngine()->registerPlugin("function","web_root", array($this, 'smartyGetWebRoot'));
		$result = $this->getEngine()->fetch($this->template);
		return $result;
	}

	private function getEngine()
	{
		if (self::$engine == null) {
			self::$engine = new Smarty();

			// Set charset
			if (!defined('SMARTY_RESOURCE_CHAR_SET')) {
				define('SMARTY_RESOURCE_CHAR_SET', $this->getCharset());
			}

			// Set Smarty path
			self::$engine->compile_dir = self::$compileDir;
			self::$engine->cache_dir = self::$cacheDir;
			self::$engine->template_dir = self::$templateDir;
		}
		//echo self::$engine->template_dir; exit();
		return self::$engine;
	}


	/**
	 *
	 */
	public function getCharset() {
		return $this->charset;
	}

	
	public static function setTemplateDir($absolutePath)
	{
		self::$templateDir = $absolutePath;
	}
	
	public static function setCacheDir($absolutePath)
	{
		self::$cacheDir = $absolutePath;
	}

	public static function setCompileDir($absolutePath)
	{
		self::$compileDir = $absolutePath;
	}

	public static function setWebRoot($webRoot)
	{
		self::$webRoot = $webRoot;
	}
	
	public function smartyGetWebRoot($params, $smarty)
	{
		return self::$webRoot;
	}	
}
