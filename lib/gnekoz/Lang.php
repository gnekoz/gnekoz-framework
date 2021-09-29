<?php

/**
 * Description of Lang
 *
 * @author gneko
 */
class Gnekoz_Lang {

    private static $langList = null;
    private static $langRes = null;

    private function __construct() {
    }


    public static function getAvailableLangs() {
        if (self::$langList == null) {
            $result = array();
            $langFiles = scandir(LANGS);

            for ($i = 0; $i < count($langFiles); $i++) {
                $curFile = LANGS.DIRECTORY_SEPARATOR.$langFiles[$i];
                if (is_file($curFile)) {
                    $fileParts = pathinfo($curFile);
                    if ($fileParts['extension'] == "ini") {
                        $result[count($result) - 1] = basename($curFile, ".ini");
                    }
                }
            }
            self::$langList = $result;
        }
        return self::$langList;
    }

    public static function getCurrentLang() {
        if (!isset($_SESSION['Gnekoz_lang'])) {
            self::setCurrentLang();
        }
        return $_SESSION['Gnekoz_lang'];

    }

    public static function setCurrentLang($lang = DEFAULT_LANG) {
        $_SESSION['Gnekoz_lang'] = $lang;
    }

    public static function isValidLang($lang) {
        return in_array($lang, (array) Gnekoz_Lang::getAvailableLangs());
    }

    public static function getResource($section, $name = null) {
        $lang = self::getCurrentLang();
        
        // Caricamento del file della lingua
        if (self::$langRes == null) {
            $langFile = LANGS.DIRECTORY_SEPARATOR.$lang.".ini";
            $parser = new Gnekoz_Utility_IniParser($langFile);
            self::$langRes = $parser->getContent();
        }

        if ($name != null) {
            if (!isset(self::$langRes[$section][$name])) {
                return "[risorsa $name inesistente]";
            }
            return self::$langRes[$section][$name];
        } else {
            if (!isset(self::$langRes[$section])) {
                throw new Exception("sezione $section inesistente");
            }
            return self::$langRes[$section];
        }
    }

    public static function getLangResource($lang, $section, $name = null) {

        // Caricamento del file della lingua
        $langFile = LANGS.DIRECTORY_SEPARATOR.$lang.".ini";
        $parser = new Gnekoz_Utility_IniParser($langFile);
        $langRes = $parser->getContent();

        if ($name != null) {
            if (!isset($langRes[$section][$name])) {
                return "[risorsa $name inesistente]";
            }
            return $langRes[$section][$name];
        } else {
            if (!isset($langRes[$section])) {
                throw new Exception("sezione $section inesistente");
            }
            return $langRes[$section];
        }
    }

    public static function getLangDesc($langCode) {
        return self::getResource("common", "lang_$langCode");
    }

    public static function getAllLangDesc() {
       $langs = self::getAvailableLangs();
       $result = array();
       foreach ($langs as $lang) {
           $result[$lang] = self::getResource("common", "lang_$lang");
       }
       return $result;
    }

    public static function setTemplateResources($engine, $section) {
        $textList = self::getResource($section);
        foreach($textList as $resName => $resValue) {
            if (substr($resName, 0, 3) == "tpl") {
                $tplResName = substr($resName, 3);
                $tplResName = strtolower(substr($tplResName, 0, 1))
                              . substr($tplResName, 1);
                $engine->assign($tplResName, $resValue);
            }
        }
    }
}

?>
