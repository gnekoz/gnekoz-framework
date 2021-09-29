<?php

/**
 * Description of Path
 *
 * @author gneko
 */
class Gnekoz_Utility_Path {

    public static function getWebRootAbsolutePath($path, $webroot) {
        if (substr($path, 0, strlen($webroot)) != $webroot) {
            throw new Exception("Il percorso indicato $path non appartiene alla webroot $webroot");
        }
        $absolutePath = substr($path, strlen($webroot));
        if (substr($absolutePath, 0, 1) != DIRECTORY_SEPARATOR ) {
            $absolutePath = DIRECTORY_SEPARATOR."$absolutePath";
        }
        return $absolutePath;
    }


    public static function normalizePath($path) {
        if (DIRECTORY_SEPARATOR == "/") {
            return str_replace("\\", DIRECTORY_SEPARATOR, $path);
        } else {
            return str_replace("/", DIRECTORY_SEPARATOR, $path);
        }
    }

}
?>
