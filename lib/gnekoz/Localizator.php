<?php

/**
 * Description of Localizator
 *
 * @author gneko
 */
class Gnekoz_Localizator {
    
    private $section = "";

    public function __construct($section) {
        $this->section = $section;
    }

    public function getResource($name) {
        return Gnekoz_Lang::getResource($this->section, $name);
    }
}

?>
