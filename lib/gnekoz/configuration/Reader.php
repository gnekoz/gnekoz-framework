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
namespace Gnekoz\Configuration;

use \DOMDocument;
use \DOMXPath;

/**
 * Classe generica per la lettura di file di configurazione in formato XML
 * @author gneko
 *
 */
class Reader {

	/**
	 * Nome della variabile d'ambiente relativa al profilo selezionato
	 * @var string
	 */
	const PROFILE_ENV_VAR_NAME = "GNEKOZ_PROFILE";

	/**
	 * Documento DOM
	 * @var DOMDOcument
	 */
	private $doc = null;

	/**
	 * Valutatore query XPath
	 * @var DOMXPath
	 */
	private $xpath = null;

	/**
	 * Profilo selezionato
	 * @var string
	 */
	private $activeProfile;

	/**
	 * Variabili utilizzabili nel file
	 * @var unknown
	 */
	private $vars = array();

	/**
	 * Inizializza l'oggetto con il file indicato e rileva
	 * l'eventuale profilo impostato
	 * @param string $file - Path del file XML di configurazione
	 */
	public function __construct($file) {
		$this->doc = new DOMDocument();
		$this->doc->load($file);
		$this->xpath = new DOMXPath($this->doc);

		if (isset($_SERVER[self::PROFILE_ENV_VAR_NAME])) {
			$this->activeProfile = $_SERVER[self::PROFILE_ENV_VAR_NAME];
		}
	}

	/**
	 * Aggiunge una variabile all'elenco
	 * @param unknown $name
	 * @param unknown $val
	 */
	public function addVar($name, $val)
	{
	  $this->vars[$name] = $val;
	}

	public function getActiveProfile()
	{
		return $this->activeProfile;
	}

	/**
	 * Ritorna il valore della proprietà indicata
	 * @param string $propName - query XPath
	 * @return string
	 */
	private function query($propName) {
		if (substr($propName, 0, 1) != "/") {
			$propName = "/$propName";
		}
		$res = $this->xpath->query("/configuration$propName");
		if (!$res) {
			return "";
		}

		$unknownNode = false;
		if (substr($propName, strlen($propName) - 1) == "*")
		{
			$unknownNode = true;
		}

		if ($res->length == 0) {
			return "";
		} else if ($res->length == 1) {
			if ($unknownNode)
				return array($res->item(0)->nodeName => $res->item(0)->nodeValue);
			else
				return $res->item(0)->nodeValue;
		} else {
			$ret = array();

			$nameIndexed = !$this->isHeterogeneous($res);
			foreach ($res as $node) {
				if ($nameIndexed || $unknownNode)
				{
					$ret[$node->nodeName] = $node->nodeValue;
				} else
				{
					$ret[] = $node->nodeValue;
				}

			}
			return $ret;
		}
	}


	private function isHeterogeneous($nodeList)
	{
		$name = false;
		foreach ($nodeList as $node)
		{
			if (!$this->isSimpleNode($node)) {
				continue;
			}

			if ($name != false && $name != $node->nodeName) {
				return false;
			}
		}
		return true;
	}


	private function isSimpleNode($node) {
		if (!$node->hasChildNodes())
		{
			return true;
		}

		if ($node->childNodes->item(0)->nodeType != XML_ELEMENT_NODE) {
			return true;
		}

		return false;
	}


	private function getNodeAttribute($node, $attributeName) {
		return $node->attributes->getNamedItem($attributeName);
	}

	public function getProperty($propName) {
		$query = "/profiles/profile[@id='{$this->activeProfile}']$propName";
		$profileResult = $this->query($query);
		if ($profileResult != "") {
			return $this->resolveVars($profileResult);
		}
		return $this->resolveVars($this->query($propName));
	}

	private function resolveVars($props)
	{
	  $returnArray = true;

	  if (!is_array($props))
	  {
	    $returnArray = false;
	    $props = array($props);
	  }

	  $result = array();
	  foreach ($props as $name => $value)
	  {
	    $pattern = '/(\\$\\{.*\\}/';
      $props[$name] = preg_replace_callback('/\$\{([a-zA-Z0-9\.-_]+)\}/', array($this, 'replaceMatches'), $value);
	  }

	  if ($returnArray)
	  {
	    return $props;
	  }
	  return $props[0];
	}

	private function replaceMatches($matches)
	{
	  return isset($this->vars[$matches[1]]) ? $this->vars[$matches[1]] : "";
	}
}