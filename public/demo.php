<?php
    require('../private/include/include.php');
    require('../private/include/session_functions.php');
    date_default_timezone_set('America/Chicago');

    // Getting the parameters passed through AJAX
    $email = "test@example.com";
    $entered_password = "password";
    $is_session_expired_login = "no";
    $selected_room = '%';
    $selected_cabinet = '%';
    if (isset($_POST['selected_room'])) {
        $selected_room = trim($_POST['selected_room']);
    }
    if (isset($_POST['selected_cabinet'])) {
        $selected_cabinet = trim($_POST['selected_cabinet']);
    }
    $current_date = date("Y-m-d H:i:s");

    $sql = "SELECT * FROM users ";
    $sql .= "WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);

    $result = $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $password1 = $row['password1'];
    $password2 = $row['password2'];
    $password_check = crypt($entered_password, $password2);
    if ($password1 == $password_check) {
        after_successful_login();
        $_SESSION['id'] = $row['id'];
        $_SESSION['email'] = $email;

        $sql = "UPDATE users SET ";
        $sql .= "last_login_date = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $current_date, PDO::PARAM_STR);
        $stmt->bindValue(2, $row['id'], PDO::PARAM_STR);
        $result = $stmt->execute();

        $_SESSION['selected_room'] = $selected_room;
        $_SESSION['selected_cabinet'] = $selected_cabinet;
        unset($_SESSION['undo_items_array']);
        $_SESSION['undo_items_array'] = array();

        header('Location: /inventory');
    } else {
        header('Location: /');
    }
?>

