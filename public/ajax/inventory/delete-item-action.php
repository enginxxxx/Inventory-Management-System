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
        $item_ids = trim($_POST['item_ids']);
        $user_id = $_SESSION['id'];
        $current_date = date("Y-m-d H:i:s");
        $delete_query_errors = 0;

        $item_ids_array = explode(',', $item_ids);
        foreach ($item_ids_array as $item) {
            $sql = "UPDATE inventory SET deleted = 1, deleted_date = ?, delete_user_id = ? WHERE id = ? ";

            $stmt = $db->prepare($sql);

            $stmt->bindValue(1, $current_date, PDO::PARAM_STR);
            $stmt->bindValue(2, $user_id, PDO::PARAM_STR);
            $stmt->bindValue(3, $item, PDO::PARAM_STR);
            $result = $stmt->execute();
            if (!$result) {
                $delete_query_errors++;
            }
        }

        if ($delete_query_errors == 0) {
            ob_start();
            require_once('../../../private/require/inventory-table-body-query.php');
            $json_response['html_tbody'] = ob_get_clean();
            $json_response['html_pagination'] = $pagination;
            $json_response['status'] = "success";
        } else {
            $json_response['status'] = "fail";
        }
    }
    echo json_encode($json_response);
} else {
    echo "Direct access is not permitted";
}
?>
