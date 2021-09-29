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

abstract class AbstractHtmlRenderer implements HtmlRenderer
{
	private $css = array();

	private $js = array();

	private $title;

	public function addCssRef($cssRef)
	{
		$this->css[] = $cssRef;
	}

	public function addJsRef($cssRef)
	{
		$this->js[] = $jsRef;
	}

	public function getJsRef()
	{
		return $this->js;
	}

	public function getCssRef()
	{
		return $this->css;
	}

	public function getJsRefTags()
	{
		$res = "";
		foreach ($this->js as $j)
		{
			$res .= "<script type=\"text/javascript\" src=\"$j\"></script>\n";
		}
		return $result;
	}

	public function getCssRefTags()
	{
		$res = "";
		foreach ($this->css as $c)
		{
			$res .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$c\">\n";
		}
		return $result;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
}