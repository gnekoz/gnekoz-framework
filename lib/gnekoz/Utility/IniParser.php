<?php

/**
 * Description of IniParser
 *
 * @author gneko
 */
class Gnekoz_Utility_IniParser {

    private $content = array();

    public function __construct($file) {
        if (!file_exists($file)) {
            throw new Exception("Il file $file non esiste");
        }

        $lines = split("\n", file_get_contents($file));        
        $section = "";
        foreach ($lines as $line) {
            $line = trim($line);

            // Commenti o righe vuote
            if ($line == "" || substr($line, 0, 1) == ";") {
                continue;
            }

            // Sezioni
            if (substr($line, 0, 1) == "[") {
                $section = substr($line, 1, strpos($line, "]", 1) - 1);
                continue;
            }
            
            $key = trim(substr($line, 0, strpos($line, "=")));
            $val = trim(substr($line, strpos($line, "=") + 1));

            // Array
            if (substr($key, strlen($key) - 2) == "[]") {
                $key = substr($key, 0, strlen($key) - 2);
                if (!isset($this->content[$section][$key])) {
                    $this->content[$section][$key] = array();
                }
                $this->content[$section][$key][count($this->content[$section][$key])] = $val;
            } else { // Valori singoli
                $this->content[$section][$key] = $val;
            }            
        }
        //var_dump($this->content);
    }

    public function getContent() {
        return $this->content;
    }

}
?>
