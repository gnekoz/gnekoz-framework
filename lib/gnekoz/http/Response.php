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

class Response
{
	private $output;

	private $app;

	public function __construct($app)
	{
		$this->app = $app;
	}

	public function addOutput($output)
	{
		$this->output .= $output;
	}

	public function getOutput()
	{
		return $this->output;
	}

	public function sendRedirect($url)
	{
		if (substr($url, 0, 1) == "/")
		{
			$url = $this->app->getWebRoot() . $url;
		}
		ob_clean();
		header("Location: $url");
	}
}