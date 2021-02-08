<?php
require_once('../private/include/session_functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
    <head>
        <title>Inventory Management System - Password Reset</title>
        <?php
        require_once ('include_references.php');
        ?>
    </head>

    <body>
        <div class="gray-out-div"></div>
        <img class="progress-circle" src="images/ajax-loader.gif"/>
        <div id="password-reset-main-body-wrapper">
            <h1>Password Reset</h1>
            <p>Please enter your new password in below fields</p>
            <div><input id="password" type="password" placeholder="Password"/></div>
            <div><input id="password-repeat" type="password" placeholder="Password Repeat"/></div>
            <div><input type="hidden" id="email" value="<?php echo $_GET['email']?>"/></div>
            <div><input type="hidden" id="code" value="<?php echo $_GET['code']?>"/></div>
            <div><a class="button" onclick="resetPassword()">Send</a></div>
        </div>
        <div class="error-div" id="password-reset-error-div"></div>
    </body>
</html>

