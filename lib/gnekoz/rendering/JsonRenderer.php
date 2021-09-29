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

use gnekoz\rendering\TextRenderer;

/**
 * Wrapper for json_encode function.
 * @author gneko
 *
 */
class JsonRenderer implements TextRenderer
{
	private $options = null;

	/**
	 * Return content type for json data
	 * @see gnekoz\rendering.Renderer::getContentType()
	 */
	public function getContentType()
	{
		return "application/json";
	}

	/**
	 * Return charset for json data.
	 * Note: json_encode function only supports utf-8 charset
	 * @see gnekoz\rendering.TextRenderer::getCharset()
	 */
	public function getCharset()
	{
		return "utf-8";
	}

	/**
	 * Set options for json_encode function
	 * @param numeric $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}

	/**
	 * Render data into json string
	 * @see gnekoz\rendering.Renderer::render()
	 */
	public function render($data)
	{
		return json_encode($data, $this->option);
	}
}