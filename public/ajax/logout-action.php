<?php

/* ================================================================ */
/* Created by Engin Yapici on 02/11/2015                            */
/* Last modified by Engin Yapici on 10/14/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

// Below if statements prevents direct access to the file. It can only be accessed through "AJAX".
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require('../../private/include/include.php');
    require('../../private/include/session_functions.php');

    after_successful_logout();
    echo 'success';
} else {
    echo "Direct access is not permitted";
}
?>

