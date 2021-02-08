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
        if (isset($_SESSION['undo_items_array'])) {
            $num_of_items = count($_SESSION['undo_items_array']);
            if ($num_of_items != 0) {
                // Getting the id of the last inserted item
                end($_SESSION['undo_items_array']);
                $last_id = key($_SESSION['undo_items_array']);
                $array_to_send = $_SESSION['undo_items_array'][$last_id];
                $array_to_send['last_item_was_used'] = 'false';
                array_pop($_SESSION['undo_items_array']);
                if (count($_SESSION['undo_items_array']) == 0) {
                    $array_to_send['last_item_was_used'] = 'true';
                }
                $json_response['items_array'] = $array_to_send;
                $json_response['status'] = "success";
            } else {
                $json_response['status'] = "empty";
            }
        } else {
            $json_response['status'] = "empty";
        }
    }
    echo json_encode($json_response);
} else {
    echo "Direct access is not permitted";
}
?>
