<?php

/**
 * Base authenticator interface
 *
 * @author gneko
 */
interface Gnekoz_Authenticator {
    
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
