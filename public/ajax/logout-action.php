<?php
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

