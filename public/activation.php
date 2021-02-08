<?php
require_once('../private/include/session_functions.php');
require('../private/include/include.php');

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
        <title>Inventory Management System</title>
        <?php
        require_once ('include_references.php');
        ?>
    </head>

    <body>
        <div id="index-main-body-wrapper">
            <?php
            if (isset($_GET['activation_code']) && isset($_GET['email'])) {
                $code = trim($_GET['activation_code']);
                $email = trim($_GET['email']);
                after_successful_login();
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $email;


                $sql = "SELECT * FROM users ";
                $sql .= "WHERE email = ? and activation = ?";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(1, $email, PDO::PARAM_STR);
                $stmt->bindValue(2, $code, PDO::PARAM_STR);

                $result = $stmt->execute();

                if ($result) {
                    if ($stmt->rowCount() == 0) {
                        echo "<h1>Invalid Activation Code</h1>"
                        . "<div>The activation code is invalid. Please make sure not to modify the information in the activation link.</div>";
                    } else {

                        $sql = "UPDATE users SET ";
                        $sql .= "account_status = 1 WHERE email = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(1, $email, PDO::PARAM_STR);
                        $result = $stmt->execute();
                        if ($result) {
                            echo "<h1>Account Activated</h1>"
                            . "<div>Thank you for activating your account. You will be redirected to the inventory page in 10 seconds.</div>"
                            . "<br>"
                            . "<div>Please click the following link if you are not redirected</div><br>"
                            . "<div><a href='" . DOMAIN_NAME . "/inventory.php'>Inventory</a></div>";
                            echo "<meta http-equiv='refresh' content='8;url=/inventory.php'>";
                        } else {
                            echo "<h1>Something Went Wrong</h1>"
                            . "<div>Your account could not be activated. Please contact the webmaster.</div>";
                        }
                    }
                } else {
                    echo "<h1>Something Went Wrong</h1>"
                    . "<div>Your account could not be activated. Please contact the webmaster.</div>";
                }
            } else {
                ?>
                <h1>Please Check Your E-mail</h1>
                <div>An activation link was sent to the e-mail address you provided. Please activate your account by clicking on the link.</div>
                <?php
            }
            ?>
        </div>
    </body>
</html>

