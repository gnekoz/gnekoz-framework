<?php

/**
 * Description of Gnekoz_Html_Helper
 *
 * @author gneko
 */
class Gnekoz_Html_Helper {

    public static function getOptions($values, $selectedKey) {
        $result = "";        
        foreach($values as $key => $value) {
            $selected = "";
            if ($selectedKey == $key) {
                $selected = " selected=\"selected\"";
            }
            $result .= "<option value=\"{$key}\"$selected>\n";
            $result .= htmlentities($value);
            $result .= "</option>\n";

        }
        return $result;
    }

}

?>
