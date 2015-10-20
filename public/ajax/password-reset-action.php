<?php
/* ================================================================ */
/* Created by Engin Yapici on 09/11/2015                            */
/* Last modified by Engin Yapici on 10/19/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

// Below if statements prevents direct access to the file. It can only be accessed through "AJAX".
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require('../../private/include/include.php');
    date_default_timezone_set('America/Chicago');

    // Getting the parameters passed through AJAX
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $entered_password = trim($_POST['password']);

    $sql = "SELECT * FROM users ";
    $sql .= "WHERE email = ? AND password_reset = 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);

    $result = $stmt->execute();


    if ($result) {
        if ($stmt->rowCount() == 1) {

            $sql = "SELECT * FROM users ";
            $sql .= "WHERE email = ? AND forgot_password = ?";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $email, PDO::PARAM_STR);
            $stmt->bindValue(2, $code, PDO::PARAM_STR);

            $result = $stmt->execute();
            if ($result) {
                if ($stmt->rowCount() == 1) {
                    // Password salt
                    $password2 = generate_random_string(22);

                    // Salted password
                    $password1 = crypt($entered_password, $password2);

                    // Inserting the information to the database
                    $sql1 = "UPDATE users SET ";
                    $sql1 .= "password_reset = 0, password1 = ?, password2 = ? WHERE email = ? AND forgot_password = ?";

                    $stmt1 = $db->prepare($sql1);

                    $stmt1->bindValue(1, $password1, PDO::PARAM_STR);
                    $stmt1->bindValue(2, $password2, PDO::PARAM_STR);
                    $stmt1->bindValue(3, $email, PDO::PARAM_STR);
                    $stmt1->bindValue(4, $code, PDO::PARAM_STR);
                    $result1 = $stmt1->execute();

                    // If everything goes well, sending an email to the admin
                    if ($result1) {
                        echo "success";
                    } else {
                        echo "fail";
                    }
                } else {
                    echo "wrong_password_reset_code";
                }
            } else {
                echo 'fail';
            }
        } else {
            echo "no_password_reset_request_found_for_this_email";
        }
    } else {
        echo "fail";
    }
} else {
    echo "Direct access is not permitted"

    ;
}

// Random number generation function
function generate_random_string($name_length) {
    $alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    return substr(str_shuffle($alpha_numeric), 0, $name_length);
}

?>
