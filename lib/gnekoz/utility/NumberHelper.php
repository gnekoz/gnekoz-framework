<?php
/*
 * Gnekoz Framework for PHP applications
 * Copyright (C) 2012  Luca Stauble
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace gnekoz\utility;

/**
 * Description of Formatter
 *
 * @author gneko
 */
class NumberHelper
{
    public static function formatMoney($number, $locale = 'it_IT')
    {
        setlocale(LC_MONETARY, $locale);
        $ret = money_format('%.2n', $number);
        setlocale(LC_MONETARY, null);
        return $ret;
    }

    public static function formatNumber($number,
    															      $decimals = 0,
    																		$locale = 'it_IT')
    {
        setlocale(LC_ALL, $locale);
        $ret = number_format($number, $decimals);
        setlocale(LC_ALL, null);
        return $ret;
    }

    public static function deformatNumber($number, $locale = 'it_IT') {
    	setlocale(LC_ALL, $locale);
    	$ret = floatval($number);
    	setlocale(LC_ALL, null);
    	return $ret;
    }

    public static function deformatMoney($number, $locale = 'it_IT') {
    	setlocale(LC_MONETARY, $locale);
    	$ret = floatval($number);
    	setlocale(LC_MONETARY, null);
    	return $ret;
    }

    public static function convertBtyeSize($size)
    {
      $unit = array('B','KB','MB','GB','TB','PB');
      return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

?>
