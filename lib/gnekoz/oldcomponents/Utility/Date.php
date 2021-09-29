<?php

/**
 * La classe Gnekoz_Utility_Date espone una serie di metodi di utilita'
 * per manipolare le date.
 *
 * @author gneko
 */
class Gnekoz_Utility_Date {

    /**
     * Converte una data in formato latino (giorno, mese, anno) nel timestamp
     * corrispondente.
     *
     * Esempi:
     *   20/04/2007
     *   20-04-2007
     *   20.04.2007
     *   20042007
     *
     * @param string $date Data in formato latino
     * @return integer timestamp
     */
    public static function tsFromLatin($date) {
        if ($date == "") {
            return null;
        }

        $day   = 0;
        $month = 0;
        $year  = 0;
        $hour  = 0;
        $min   = 0;
        $sec   = 0;

        // Data senza separatori
        if (is_numeric($date)) {
            $day   = substr($date, 0, 2);
            $month = substr($date, 2, 2);
            $year  = substr($date, 6, 4);
        } else {
            //list($data, $ora) = explode(" ", $date);
            //list($hour, $min, $sec) = explode(":", $ora);
            list($day, $month, $year) = preg_split("/[-\.\/ ]/", $date);
        }
        return mktime($hour, $min, $sec, $month, $day, $year);
    }

    /**
     * Converte una data in formato latino (giorno, mese, anno) nel formato
     * iso corrispondente.
     *
     * @param string $date
     * @return string data in formato iso
     */
    public static function isoFromLatin($date) {
        if ($date == "") {
            return "";
        }
        return date("Y-m-d", self::tsFromLatin($date));
    }


    /**
     * Converte una data formato iso in formato latino (giorno, mese, anno)
     *
     * @param string $date data in formato iso
     * @return string data in formato latino
     */
    public static function latinFromIso($date) {
        if ($date == "") {
            return "";
        }
        return date("d-m-Y", self::getTimestamp($date));
    }

    /**
     * Restituisce la differenza in giorni tra le due date indicate
     * @param date (or date formatted string) $date1
     * @param date (or date formatted string) $date2
     * @return integer
     */
    public static function getDaysDiff($date1, $date2) {
        $date1 = self::getTimestamp($date1);
        $date2 = self::getTimestamp($date2);
        $dateDiff = $date1 - $date2;
        $fullDays = floor($dateDiff/(60*60*24));
        return $fullDays;
    }


    /**
     * Restituisce un array associativo di 53 settimane a partire
     * dalla data indicata. Ogni elemento dell'array contiene 2 elementi:<br/>
     * 'from' : indica la data iniziale della settimana
     * 'to' : indica la data finale della settimana
     * Le settimane cominciano e finiscono il sabato
     * @param date (or date formatted string) $from
     * @param boolean $formatted - indica se le date devono essere espresse come
     * timestamp o come stringa formattata iso
     * @return array
     */
    public static function getCalendar($from, $iso = false, $limit = null) {
        $from = self::getTimestamp($from);
        if ($limit != null) {
            $limit = self::getTimestamp($limit);
        }
        $result = array();        
        for ($i = 0; $i < 53; $i++) {
            $result[$i]['from']    = $from;            
            if ($iso) {
                $result[$i]['from'] = date("Y-m-d", $result[$i]['from']);
            }
            $to = self::addDays($from, 7);
            $result[$i]['to'] = $to;
            if ($iso) {
                $result[$i]['to'] = date("Y-m-d", $result[$i]['to']);
            }
            if ($limit != null && $limit == $to) {
                break;
            }
            $from = $to;
        }
        return $result;
    }

    /**
     * Aggiunge il numero di giorni indicati in <code>$days</code> alla data
     * indicata
     * @param date/integer $date La data alla quale aggiungere i giorni.
     * Pu&ograve; essere una stringa o un timestamp
     * @param integer $days
     * @return integer Timestamp risultante
     */
    public static function addDays($date, $days) {
        $date = self::getTimestamp($date);
        $parts = getdate($date);
        return mktime(0,                     
                      0,                     
                      0,                     
                      $parts['mon'],         
                      $parts['mday'] + $days,
                      $parts['year']);       
    }


    /**
     * Ritorna il primo sabato dell'anno
     * @param integer $year
     * @return integer timestamp
     */
    public static function getFirstDay($year) {
        // Calcolo del primo sabato dell'anno
        $day = 1;
        $firstYearDay = mktime(0, 0, 0, 1, $day, $year);
        return self::getNextSaturday($firstYearDay);
    }


    /**
     * Ritorna il primo inizio settimana dalla data corrente
     */
    public static function getNextSaturday($from = null) {
        if ($from == null) {
            $from = time();
        }
        while (date("w", $from) != 6 ) {
            $from = self::addDays($from, 1);
        }
        return $from;
    }


    /**
     * Restituisce il timestamp corrispondente alla data indicata.
     * Se la data e' numerica viene considerata come timestamp e viene
     * restituita tale e quale,
     *
     * Questo metodo serve per poter manipolare uniformemente timestamp e
     * date in formato iso
     *
     * @param mixed $date
     * @return integer
     */
    public static function getTimestamp($date) {
        if (!is_integer($date)) {
            $date = strtotime($date);
        }
        return $date;
    }

    /**
     * Restituisce la data iso a partire da un timestamp o una stringa
     * che rappresenti una data (qualsiasi stringa accettata dalla funzione
     * <code>strtotime</code>)
     * @param mixed $date
     * @return string data in formato iso
     */
    public static function getIsoDate($date) {
        if (!is_integer($date)) {
            $date = strtotime($date);
        }
        return date("Y-m-d", $date);
    }
}

?>
