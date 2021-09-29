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
namespace Gnekoz\Http;

use gnekoz\http\File;

class Request {

	private $ajaxRequest = false;

	private $method;

	private $url;

	private $path;

	private $parameters = array();

	private $queryString;

	private $protocol;

	private $hostname;

	private $fragment;

	private $userAgent;

	private $files = array();


	public function __construct($url)
	{
 		$tokens = parse_url($url);
 		if (isset($tokens['path'])) $this->path = $tokens['path'];
 		if (isset($tokens['query'])) $this->queryString = $tokens['query'];
 		if (isset($tokens['fragment'])) $this->fragment = $tokens['fragment'];

 		$this->method = $_SERVER['REQUEST_METHOD'];
 		$this->protocol = $_SERVER['SERVER_PROTOCOL'];
 		$this->hostname = $_SERVER['HTTP_HOST'];
 		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];

		if ($this->isPost())
		{
			$this->parameters = $_POST;
		} else if ($this->isGet())
		{
			$this->parameters = $_GET;
		}

		// Manage file uploads
		foreach ($_FILES as $id => $info)
		{
		  $this->files[$id] = new File($id, $info);
		}

 		//print_r($tokens);

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
				&& $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest")
		{
			$this->isAjaxRequest = true;
		}
	}

	public function isAjaxRequest()
	{
		return $this->ajaxRequest;
	}

	public function isPost()
	{
		return strtoupper($this->method) == "POST";
	}

	public function isGet()
	{
		return strtoupper($this->method) == "GET";
	}

	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function hasParameter($name)
	{
	  return isset($this->parameters[$name]);
	}

	public function getParameters()
	{
		return $this->parameters;
	}


	public function getPath()
	{
		return $this->path;
	}

	public function getProtocol()
	{
		return $this->protocol;
	}

	public function getHostname()
	{
		return $this->Hostname;
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function isFileUploaded($id = false)
	{
	  //var_dump($this->files); exit();
	  foreach ($this->files as $file)
	  {
	    if ($file->hasError() == false)
	    {
	      if (!$id && $file->getID() != $id)
	      {
	        continue;
	      }
	      return true;
	    }
	  }
	  return false;
	}

	public function getFile($id)
	{
	  return $this->files[$id];
	}

	public function getFiles()
	{
	  return $this->files;
	}
}