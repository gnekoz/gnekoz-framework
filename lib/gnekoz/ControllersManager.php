<?php

class Gnekoz_ControllersManager {
    
    private function __construct() {
    }

    public static function getController($controllerName) {
        $controllerList = self::getControllerList();
        //var_dump($compList);
        if (!in_array($controllerName, (array) $controllerList)) {
            throw new Exception("Il controller $controllerName non esiste");
        }
        include_once(CONTROLLERS.DIRECTORY_SEPARATOR.$controllerName.".php");
        $controllerName = basename($controllerName);
        return new $controllerName();
    }

    public static function getControllerList() {
        if (!isset($_SESSION['Gnekoz_Controllers'])) {
            $controllerList = array();
            self::getControllers(CONTROLLERS, $controllerList);
            $_SESSION['Gnekoz_Controllers'] = $controllerList;
        }
        return $_SESSION['Gnekoz_Controllers'];
    }

    private static function getControllers($path, &$result, $package = "" ) {
        if (!file_exists($path)) {
            throw new Exception("Il percorso indicato non esiste ($path)");
        }

        if (is_dir($path)) {
            $children = scandir($path);
            for ($i = 0; $i < count($children); $i++) {

                if ($children[$i] == "." || $children[$i] == "..") {
                    continue;
                }
                $childrenRealpath = $path.DIRECTORY_SEPARATOR.$children[$i];
                $childPackage = $package;
                if (is_dir($childrenRealpath)) {
                    $childPackage .= $children[$i]."/";
                }
                self::getControllers($childrenRealpath, $result, $childPackage);
            }
        } else {
            $pathParts = pathinfo($path);
            $fileExt = $pathParts['extension'];
            if (strtolower($fileExt) == "php") {
                $className = basename($pathParts['basename'], ".php");
                $result[count($result) - 1] = $package.$className;
            }
        }
    }
}

?>