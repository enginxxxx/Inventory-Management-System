<?php
require_once('../private/include/session_functions.php');

if (is_logged_in()) {
    $target = 'inventory.php';
    header("Location: /$target");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
    <head>
        <title>Inventory Management System - Register</title>
        <?php
        require_once ('include_references.php');
        ?>
    </head>

    <body>
        <div class="gray-out-div"></div>
        <img class="progress-circle" src="images/ajax-loader.gif"/>
        <div id="register-page-main-body-wrapper">
            <h1>Register</h1>
            <div><input id="email" type="text" placeholder="E-mail Address"/></div>
            <div><input id="password" type="password" placeholder="Password"/></div>
            <div><input id="password-repeat" type="password" placeholder="Password Repeat"/></div>
            <div><a class="button" onclick="registerUser()">Submit</a></div>
        </div>
        <div class="error-div" id="register-error-div"></div>
    </body>
</html>

