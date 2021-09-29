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
namespace gnekoz\routing;

class RoutingTarget
{
	private $controller;

	private $controllerClassName;

	private $action;

	private $params;

	public function __construct($controller, $action, $params = false)
	{
		$this->controller = $controller;
		$this->controllerClassName = "\\".basename($this->controller, ".php");
		$this->action = $action;
		$this->params = $params;
	}

	public function getController()
	{
		return $this->controller;
	}

	public function getControllerClassName()
	{
		return $this->controllerClassName;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function validate()
	{
		if (!is_readable($this->controller))
		{
			return false;
		}

		require_once $this->controller;
// FIXME
// 		if (!class_exists($this->controllerClassName))
// 		{
// 			return false;
// 		}

// 		$classMethods = get_class_methods($this->controllerClassName);
// 		if (!in_array($this->action, $classMethods))
// 		{
// 			return false;
// 		}

		return true;
	}
}