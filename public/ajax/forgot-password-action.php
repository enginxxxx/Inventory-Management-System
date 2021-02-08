<?php
// Below if statements prevents direct access to the file. It can only be accessed through "AJAX".
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require('../../private/include/include.php');
    date_default_timezone_set('America/Chicago');

    // Getting the parameters passed through AJAX
    $email = trim($_POST['email']);

    $forgot_password = generate_random_string(60);

    // Inserting the information to the database
    $sql1 = "UPDATE users SET ";
    $sql1 .= "forgot_password = ?, password_reset = 1 WHERE email = ?";

    $stmt1 = $db->prepare($sql1);

    $stmt1->bindValue(1, $forgot_password, PDO::PARAM_STR);
    $stmt1->bindValue(2, $email, PDO::PARAM_STR);
    $result1 = $stmt1->execute();

    // If everything goes well, sending an email to the admin
    if ($result1) {
        $user_email = $email;
        $headers2 = 'MIME-Version: 1.0' . "\r\n";
        $headers2 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers2 .= "From: Inventory Management System <engin.yapici@example.com>" . "\r\n";
        $headers2 .= "Reply-to: engin.yapici@example.com\r\n";

        //formatting the mail posting
        $subject2 = "Inventory Management System Password Reset";
        $message2 = "<html>
                                <body>
                                  <p>Dear User,</p>
                                  <div>Please follow the below link to reset your password:</div><br>
                                  <a href='" . DOMAIN_NAME . "/password-reset.php?code=$forgot_password&email=$email'>Password Reset Link</a><br><br>
                                  <div>Thank you,</div>
                                  <div>IMS Technical Support</div>
                                </body>
                            </html>";
        if (mail($user_email, $subject2, $message2, "$headers2")) {
            echo "success";
        } else {
            echo "mail_fail";
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
