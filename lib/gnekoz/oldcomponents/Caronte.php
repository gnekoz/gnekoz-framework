<?php

class Gnekoz_Caronte {
    
    private static $currentUrl = "";
    private static $currentPage = "";
    private static $isAjaxRequest = false;

    private function __construct() {
    }

    public static function getCurrentUrl() {
        return self::$currentUrl;
    }

    public static function getCurrentPage() {
        return self::$currentPage;
    }

    public static function isAjaxRequest() {
        return self::$isAjaxRequest;
    }
    

    public static function ferry($requestUrl = null) {
    		//echo $requestUrl . " - " . $_SERVER; exit;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest") {
            self::$isAjaxRequest = true;
        } else {
            self::$isAjaxRequest = false;
        }

        /*
         * Check the base url. In case of the current application ins't the 
         * root application (eg. http://localhost/) but resides on a 
         * subdirectory (eg. http://localhost/myapp/) the url parts must to
         * be shifted to left. 
         */
        $subdirLevel = count(split("/", $_SERVER['SCRIPT_NAME'])) - 2;
        
        if ($requestUrl == null) {
            $requestUrl = $_SERVER['REQUEST_URI'];
        }
        $requestUrl = parse_url($requestUrl, PHP_URL_PATH);
        $urlParts = split("/", $requestUrl);
        
       
        // Shift url pieces by subdir count (plus 1 for root /) 
        for ($i = 0; $i < $subdirLevel + 1; $i++) {
        	array_shift($urlParts);
        }
        $lang = "";
        $page = "";
        $action = "";
				        

        // Controllo della lingua
        if (count($urlParts) > 0) {
            $lang = $urlParts[0];
            if (!Gnekoz_Lang::isValidLang($lang)) {
                $lang = Gnekoz_Lang::getCurrentLang();
            } else {
                Gnekoz_Lang::setCurrentLang($lang);
                array_shift($urlParts);
            }
        }

        
        // Lettura della pagina e dell'eventuale path
        $pathElement = count($urlParts);
        while($pathElement >= 0) {
            $tempPath = "";
            for ($i = 0; $i < $pathElement; $i++) {
                $tempPath .= "/".$urlParts[$i];
            }
            $realTempPath = CONTROLLERS.$tempPath;
            if (file_exists($realTempPath)
                || file_exists($realTempPath.".php") ) {
                $page = $tempPath;
                if (is_dir(CONTROLLERS.DIRECTORY_SEPARATOR.$tempPath)) {
                    if ($page != "/") {
                        $page .= "/";
                    }
                    $page .= DEFAULT_PAGE;
                }
                $classFilePath = CONTROLLERS.DIRECTORY_SEPARATOR.$page.".php";
                $className = basename($classFilePath, ".php");
                include_once($classFilePath);
                if (!class_exists($className)) {
                	throw new Exception("La pagina $className non esiste");
//                 	header('HTTP/1.0 404 Not Found');
//                     exit();
                }

                if ($pathElement <= count($urlParts)  - 1) {
                    $action = $urlParts[$pathElement];
                } else {
                    $action = DEFAULT_ACTION;
                }

                if (self::$isAjaxRequest) {
                    $action = "ajax".ucfirst($action);
                }


                if (!in_array($action, (array) get_class_methods($className))) {
                	throw new Exception("L'action $action non esiste");
//                 	header('HTTP/1.0 404 Not Found');
//                     exit();
                }
            }

            if ($lang != "" && $page != "" && $action != "") {
                self::setCurrentUrl($requestUrl);
                $destinationClass = new $className();
                $destinationClass->init();
                self::$currentPage = $page;

                // TODO : sistemare sta porcheria
                $output = $destinationClass->$action();
                if ($destinationClass->isRenderable()) {
                    echo $destinationClass->render(null);
                } else {
                    echo $output;
                }
                return;
            }
            $pathElement--;
        }
        if ($lang == "" || $page == "" || $action == "") {
        	header('HTTP/1.0 404 Not Found');
        	exit();        	
        }
    }


    public static function redirectTo($url) {
        header("Location: " . $url);        
        exit();
    }


    public static function setCurrentUrl($url) {
        
    }


    public static function getRequestPar($parName, $method = null) {
//        var_dump($_GET);
//        var_dump($_POST);
        $method = strtolower($method);
        $postVal = null;
        $getVal  = null;
        if (isset($_GET[$parName])) {
            $getVal = $_GET[$parName];
        }
        if (isset($_POST[$parName])) {
            $postVal = $_POST[$parName];
        }


        if ($method == null) {
            if (isset($_GET[$parName]) && !isset($_POST[$parName])) {
                return $getVal;
            } else if (!isset($_GET[$parName]) && isset($_POST[$parName])) {
                return $postVal;
            } else {
                return $getVal;
            }
        } else if ($method == "post") {
            return $postVal;
        } else if ($method == "get") {
            return $getVal;
        }
    }
}

?>