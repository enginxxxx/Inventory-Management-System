<?php
// Below if statement prevents direct access to the file. It can only be accessed through "AJAX".
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    require('../../../private/include/include.php');
    require('../../../private/include/session_functions.php');

    if (isset($_SESSION['selected_cabinet'])) {
        $json_response['selected_cabinet'] = $_SESSION['selected_cabinet'];
    }

    if (isset($_SESSION['selected_room'])) {
        $json_response['selected_room'] = $_SESSION['selected_room'];
    }

    if (!is_session_valid()) {
        $json_response['status'] = "no_session";
    } else {
        date_default_timezone_set('America/Chicago');
        // Getting the parameters passed through AJAX
        $item_id = trim($_POST['item_id']);
        $field_name = trim($_POST['field_name']);
        $value = trim($_POST['value']);
        $user_id = $_SESSION['id'];
        $is_date = $_POST['is_date'];
        $is_undo = $_POST['is_undo'];
        $current_date = date("Y-m-d H:i:s");

        if ($is_date == 1) {
            $value = convert_str_date_to_mysql_date($value);
        }

        // Getting the old value first before update, I use this for the 'undo' functionality
        $sql1 = "SELECT * FROM inventory WHERE id = ? ";

        $stmt1 = $db->prepare($sql1);

        $stmt1->bindValue(1, $item_id, PDO::PARAM_STR);
        $result1 = $stmt1->execute();
        if ($result1) {
            while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                if ($is_date == 1) {
                    $old_value = convert_mysql_date_to_php_date($row[$field_name]);
                } else {
                    $old_value = $row[$field_name];
                }
            }

            if ($old_value != $value) {
                // Updating the information in the database
                $sql = "UPDATE inventory SET $field_name = ?, last_edit_user_id = ?, last_edit_date = ? WHERE id = ? ";

                $stmt = $db->prepare($sql);

                $stmt->bindValue(1, $value, PDO::PARAM_STR);
                $stmt->bindValue(2, $user_id, PDO::PARAM_STR);
                $stmt->bindValue(3, $current_date, PDO::PARAM_STR);
                $stmt->bindValue(4, $item_id, PDO::PARAM_STR);
                $result = $stmt->execute();

                if ($result) {
                    if ($is_undo == 'false') {
                        $item = array(
                            "item_id" => $item_id,
                            "field_name" => $field_name,
                            "old_value" => $old_value
                        );
                        array_push($_SESSION['undo_items_array'], $item);
                    }
                    $json_response['status'] = "success";
                } else {
                    $json_response['status'] = "fail";
                }
            } else {
                $json_response['status'] = "values_are_same";
            }
        } else {
            $json_response['status'] = "fail";
        }
    }
    echo json_encode($json_response);
} else {
    echo "Direct access is not permitted";
}
?>
