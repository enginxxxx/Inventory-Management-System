<?php

/* ================================================================ */
/* Created by Engin Yapici on 11/03/2014                            */
/* Last modified by Engin Yapici on 10/14/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

$db_server = DB_SERVER;
$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASS;

try {
    $db = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>