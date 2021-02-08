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
        $chemical_description = trim($_POST['chemical_description']);
        $room_no = trim($_POST['room_no']);
        $cabinet_or_asset_no = trim($_POST['cabinet_or_asset_no']);
        $physical_state = trim($_POST['physical_state']);
        $volume_or_size = trim($_POST['volume_or_size']);
        $cas_no = trim($_POST['cas_no']);
        $storage_temperature = trim($_POST['storage_temperature']);
        $preparation_date = convert_str_date_to_mysql_date(trim($_POST['preparation_date']));
        $aliquot_date = convert_str_date_to_mysql_date(trim($_POST['aliquot_date']));
        $receipt_date = convert_str_date_to_mysql_date(trim($_POST['receipt_date']));
        $open_date = convert_str_date_to_mysql_date(trim($_POST['open_date']));
        $expiration_date = convert_str_date_to_mysql_date(trim($_POST['expiration_date']));
        $vendor = trim($_POST['vendor']);
        $catalog_no = trim($_POST['catalog_no']);
        $lot_no = trim($_POST['lot_no']);
        $notes = trim($_POST['notes']);
        $user_id = $_SESSION['id'];
        $current_date = date("Y-m-d H:i:s");

        // Inserting the information to the database
        $sql = "INSERT INTO inventory (";
        $sql .= "chemical_description, room_no, cabinet_or_asset_no, physical_state, volume_or_size, cas_no, storage_temperature, preparation_date, aliquot_date";
        $sql .= ", receipt_date, open_date, expiration_date, vendor, catalog_no, lot_no, user_id, data_enter_date, last_edit_date, notes";
        $sql .= ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(1, $chemical_description, PDO::PARAM_STR);
        $stmt->bindValue(2, $room_no, PDO::PARAM_STR);
        $stmt->bindValue(3, $cabinet_or_asset_no, PDO::PARAM_STR);
        $stmt->bindValue(4, $physical_state, PDO::PARAM_STR);
        $stmt->bindValue(5, $volume_or_size, PDO::PARAM_STR);
        $stmt->bindValue(6, $cas_no, PDO::PARAM_STR);
        $stmt->bindValue(7, $storage_temperature, PDO::PARAM_STR);
        $stmt->bindValue(8, $preparation_date, PDO::PARAM_STR);
        $stmt->bindValue(9, $aliquot_date, PDO::PARAM_STR);
        $stmt->bindValue(10, $receipt_date, PDO::PARAM_STR);
        $stmt->bindValue(11, $open_date, PDO::PARAM_STR);
        $stmt->bindValue(12, $expiration_date, PDO::PARAM_STR);
        $stmt->bindValue(13, $vendor, PDO::PARAM_STR);
        $stmt->bindValue(14, $catalog_no, PDO::PARAM_STR);
        $stmt->bindValue(15, $lot_no, PDO::PARAM_STR);
        $stmt->bindValue(16, $user_id, PDO::PARAM_STR);
        $stmt->bindValue(17, $current_date, PDO::PARAM_STR);
        $stmt->bindValue(18, $current_date, PDO::PARAM_STR);
        $stmt->bindValue(19, $notes, PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result) {
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
