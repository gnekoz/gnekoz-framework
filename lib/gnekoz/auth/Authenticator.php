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
namespace gnekoz\auth;

/**
 * Base authenticator interface
 *
 * @author gneko
 */
interface Authenticator {
    
		/**
		 * Returns the login page url.
		 * @return string
		 */
    public function getLoginPage();
    
    /**
     * Get the authentication status for current session
     * @return boolean
     */
    public function isAuthenticated();
    
    /**
     * Return current user information 
     * @return mixed
     */
    public function getCurrentUser();

    /**
     * Do the login iter
     * @param string $username
     * @param string $password
     * @return boolean - TRUE if login was successful, FALSE otherwise
     */
    public function login($username, $password);
}

?>
