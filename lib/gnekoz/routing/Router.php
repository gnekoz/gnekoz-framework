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

use gnekoz\http\Response;

class Router
{
	private $rules = array();

	private $app;

	public function __construct($app, $rules)
	{
		$this->app = $app;
		$this->rules[] = $rules;
	}

	public function addRule($rule)
	{
		$this->rules[] = $rule;
	}


	public function dispatch($request)
	{
		// Choose target action based on given rules. Last matching rule wins
		$target = null;
		foreach ($this->rules as $rule)
		{
			$curTarget = $rule->getTarget($this->app, $request);
			if ($curTarget != null) {
				$target = $curTarget;
			}
		}

		if ($target == null)
		{
			ob_clean();
			header('HTTP/1.0 404 Not Found');
			return;
		}

		if (!$target->validate())
		{
			ob_clean();
			header('HTTP/1.0 404 Not Found');
			return;
		}

		$response = new Response($this->app);

		$controllerClassName = $target->getControllerClassName();

		// FIXME
		$controllerClassName = $this->app->getName()."\\controllers$controllerClassName";
		$controllerAction = $target->getAction();
		$controller = new $controllerClassName($request, $response);
		$controller->init();
		$controller->$controllerAction();


		ob_clean();
		echo $response->getOutput();
		ob_flush();
	}
}