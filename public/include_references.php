<?php
/* ================================================================ */
/* Created by Engin Yapici on 11/03/2014                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"/>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script type="text/javascript" src="js/external/jquery.min.js"></script>
<script type="text/javascript" src="js/external/jquery-ui.js"></script>
<script src="js/main.js"></script>
<script src="js/external/spin.min.js"></script>
<script src="js/external/html2canvas.js"></script>
<link href="css/main.css" rel="stylesheet">

<style type="text/css">
<?php
/* Getting the current file's name without the extension and checking whether
 * there is a style file with the same name. If there is, it is included in that php file.
 */
$name = basename($_SERVER["SCRIPT_FILENAME"], '.php');
$generic_name = substr($name, strrpos($name, '-') + 1);
$css_file_path = dirname(__FILE__) . "/css/" . $name . ".css";
$css_file_path_generic = dirname(__FILE__) . "/css/" . $generic_name . ".css";
if (file_exists($css_file_path)) {
    require_once ($css_file_path);
}

if (file_exists($css_file_path_generic)) {
    require_once ($css_file_path_generic);
}
?>
</style>
<?php
/* Getting the current file's name without the extension and checking whether
 * there is a JS file with the same name. If there is, it is included in that php file.
 */
$js_file_path = dirname(__FILE__) . "/js/" . $name . ".js";
$js_file_path_generic = dirname(__FILE__) . "/js/" . $generic_name . ".js";
if (file_exists($js_file_path)) {
    echo "<script type='text/javascript' src='js/" . $name . ".js'></script>";
}
if (file_exists($js_file_path_generic)) {
    echo "<script type='text/javascript' src='js/" . $generic_name . ".js'></script>";
}
?>