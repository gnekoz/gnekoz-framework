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
namespace gnekoz\routing\Rules;

use gnekoz\routing\RoutingTarget;
use gnekoz\routing\RoutingRule;
use gnekoz\Application;


class Direct implements RoutingRule
{
	public function getTarget($app, $request)
	{
		// Case 1: directory
		$path = str_replace("/", DIRECTORY_SEPARATOR, $request->getPath());
		$path = $app->getSrcRoot() . DIRECTORY_SEPARATOR
				. APPLICATION::CONTROLLERS_DIR . $path;
		if (is_readable($path))
		{
			$controller = $path . DIRECTORY_SEPARATOR
			      	      . Application::DEFAULT_CONTROLLER . ".php";
			return new RoutingTarget($controller, Application::DEFAULT_ACTION);
		}


		// Case 2: controller
		$pathTokens = array();
		$pathTokens = split(DIRECTORY_SEPARATOR, $path);
		$controller = array_pop($pathTokens);
		$controller = join(DIRECTORY_SEPARATOR, $pathTokens) . DIRECTORY_SEPARATOR
		              . ucfirst($controller) . ".php";

		if (is_readable($controller))
		{
			return new RoutingTarget($controller, Application::DEFAULT_ACTION);
		}

		// Case 3: action
		$pathTokens = array();
		$pathTokens = split(DIRECTORY_SEPARATOR, $path);
		if (count($pathTokens) > 1) {
			$action = array_pop($pathTokens);
			$controller = array_pop($pathTokens);
			$controller = join(DIRECTORY_SEPARATOR, $pathTokens) . DIRECTORY_SEPARATOR
			              . ucfirst($controller) . ".php";

			if (is_readable($controller))
			{
				return new RoutingTarget($controller, $action);
			}
		}

		return null;
	}
}