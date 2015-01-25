<?php

class Date {

    protected static $month = array(
         1 => 'Januar',
         2 => 'Februar',
         3 => 'März',
         4 => 'April',
         5 => 'Mai',
         6 => 'Juni',
         7 => 'Juli',
         8 => 'August',
         9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Dezember',
    );

    public static function translate($mysqlDate) {
        $timestamp = strtotime($mysqlDate);
        $w         = (int)date('N', $timestamp);
        $d         = (int)substr($mysqlDate, 8, 2);
        $m         = (int)substr($mysqlDate, 5, 2);
        $y         = substr($mysqlDate, 0, 4);
        $time      = substr($mysqlDate, 11, 5);
        return Weekday::translate($w) . ', ' . $d . '. ' . self::$month[$m] . ' ' . $y . ', ' . $time . ' Uhr';
    }

    public static function asDate($mysqlDate) {
        $timestamp = strtotime($mysqlDate);
        $d         = (int)substr($mysqlDate, 8, 2);
        $m         = (int)substr($mysqlDate, 5, 2);
        $y         = substr($mysqlDate, 0, 4);
        return $d . '. ' . self::$month[$m] . ' ' . $y;
    }
    
}

