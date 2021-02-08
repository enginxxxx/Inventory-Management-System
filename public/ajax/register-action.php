<?php
// Below if statements prevents direct access to the file. It can only be accessed through "AJAX".
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require('../../private/include/include.php');
    require('../../private/include/session_functions.php');
    date_default_timezone_set('America/Chicago');

    // Getting the parameters passed through AJAX
    $email = trim($_POST['email']);
    $entered_password = trim($_POST['password']);
    $current_date = date("Y-m-d H:i:s");
    $array = explode("@", $email, 2);
    $username = $array[0];

    // Checking if the email already is in use
    $sql = "SELECT * FROM users ";
    $sql .= "WHERE email = ? ";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);

    $result = $stmt->execute();

    if ($result) {
        if ($stmt->rowCount() == 0) {
            // Password salt
            $password2 = generate_random_string(22);

            // Salted password
            $password1 = crypt($entered_password, $password2);

            $activation = generate_random_string(60);

            // Inserting the information to the database
            $sql1 = "INSERT INTO users (";
            $sql1 .= "email, password1, password2, last_login_date, activation, username";
            $sql1 .= ") VALUES (?,?,?,?,?,?)";

            $stmt1 = $db->prepare($sql1);

            $stmt1->bindValue(1, $email, PDO::PARAM_STR);
            $stmt1->bindValue(2, $password1, PDO::PARAM_STR);
            $stmt1->bindValue(3, $password2, PDO::PARAM_STR);
            $stmt1->bindValue(4, $current_date, PDO::PARAM_STR);
            $stmt1->bindValue(5, $activation, PDO::PARAM_STR);
            $stmt1->bindValue(6, $username, PDO::PARAM_STR);
            $result1 = $stmt1->execute();

            // If everything goes well, sending an email to the admin
            if ($result1) {
                $admin_email = 'enginyapici@gmail.com';
                $headers = "From: New IMS User Registration <enginyapici@gmail.com>" . "\r\n";
                $headers .= "enginyapici@gmail.com\r\n";

                //formatting the mail posting
                $subject = "New IMS User Registration";
                $message = "Dear Web Admin,
        
There is a new user registered in Inventory Management System.
        
Thank you";

                $user_email = $email;
                $headers2 = 'MIME-Version: 1.0' . "\r\n";
                $headers2 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers2 .= "From: Inventory Management System <enginyapici@gmail.com>" . "\r\n";
                $headers2 .= "Reply-to: enginyapici@gmail.com\r\n";

                //formatting the mail posting
                $subject2 = "Welcome to Inventory Management System";
                $message2 = "<html>
                                <body>
                                  <p>Thank you for your registration</p>
                                  <div>Please activate your account by clicking the following link:</div><br>
                                  <a href='" . DOMAIN_NAME . "/activation.php?activation_code=$activation&email=$email'>Activation Link</a><br><br>
                                  <div>Thank you,</div>
                                  <div>IMS Technical Support</div>
                                </body>
                            </html>";
                mail($user_email, $subject2, $message2, "$headers2");
                if (mail($admin_email, $subject, $message, "$headers")) {
                    echo "success";
                } else {
                    echo "mail_fail";
                }
            } else {
                echo "fail";
            }
        } else {
            echo "already_exists";
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
