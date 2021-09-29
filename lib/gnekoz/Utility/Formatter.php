<?php

/**
 * Description of Formatter
 *
 * @author gneko
 */
class Gnekoz_Utility_Formatter {
    public static function formatMoney($number, $locale = 'it_IT') {
        setlocale(LC_MONETARY, $locale);
        $ret = money_format('%.2n', $number);        
        setlocale(LC_MONETARY, null);
        return $ret;
    }
    
    public static function formatNumber($number, $locale = 'it_IT') {
        setlocale(LC_ALL, $locale);
        $ret = number_format($number);        
        setlocale(LC_ALL, null);
        return $ret;        
    }
}

?>
