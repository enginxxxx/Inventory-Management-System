<?php

/* ================================================================ */
/* Created by Engin Yapici on 11/03/2014                            */
/* Last modified by Engin Yapici on 10/14/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

function convert_mysql_date_to_php_date($date) {
    if ($date == "0000-00-00") {
        $date = "N/A";
    } else {
        $date = date('d-M-y', strtotime($date));
    }
    return $date;
}

function convert_str_date_to_mysql_date($date) {
    if ($date == "N/A") {
        $date = "0";
    } else {
        try {
            $date = date('Y-m-d', strtotime($date));
        } catch (Exception $e) {
            $date = "0";
        }
    }
    return $date;
}

?>
