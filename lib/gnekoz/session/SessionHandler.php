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
namespace gnekoz\session;

use \SessionHandler as DefaultHandler;
use \SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{
	private $defaultHandler;

	private $listener;

	public function __construct($sessionListener)
	{
		$this->defaultHandler = new DefaultHandler();
		$this->listener = $sessionListener;
	}

	public function close()
	{
		// FIXME
		return $this->defaultHandler->close();
	}

	public function destroy($sessionID)
	{
		$this->listener->onSessionDestroy($sessionID);
		return $this->defaultHandler->destroy($sessionID);
	}

	public function gc($maxlifetime)
	{
		return $this->defaultHandler->gc($maxlifetime);
	}

	public function open($save_path, $name)
	{
		$new = false;
		if (session_status() == PHP_SESSION_NONE) {
			$new = true;
		}
		if ($this->defaultHandler->open($save_path, $name)) {
			$id = session_id();
			$new ? $this->listener->onSessionCreate($id)
			     : $this->listener->onSessionRestore($id);

			return true;
		}
		return false;
	}

	public function read($session_id)
	{
		return $this->defaultHandler->read($session_id);
	}


	public function write($session_id, $session_data)
	{
		return $this->defaultHandler->write($session_id, $session_data);
	}
}