<?php

/* ================================================================ */
/* Created by Engin Yapici on 12/07/2014                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

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
        $query_errors = 0;

        $item_ids_array = explode(',', $item_ids);
        foreach ($item_ids_array as $item) {
            $sql = "SELECT * FROM inventory WHERE id = ? ";

            $stmt = $db->prepare($sql);

            $stmt->bindValue(1, $item, PDO::PARAM_STR);
            $result = $stmt->execute();
            if (!$result) {
                $query_errors++;
            } else {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $chemical_description = $row['chemical_description'];
                    $room_no = $row['room_no'];
                    $cabinet_or_asset_no = $row['cabinet_or_asset_no'];
                    $physical_state = $row['physical_state'];
                    $volume_or_size = $row['volume_or_size'];
                    $cas_no = $row['cas_no'];
                    $storage_temperature = $row['storage_temperature'];
                    $preparation_date = convert_str_date_to_mysql_date($row['preparation_date']);
                    $aliquot_date = convert_str_date_to_mysql_date($row['aliquot_date']);
                    $receipt_date = convert_str_date_to_mysql_date($row['receipt_date']);
                    $open_date = convert_str_date_to_mysql_date($row['open_date']);
                    $expiration_date = convert_str_date_to_mysql_date($row['expiration_date']);
                    $vendor = $row['vendor'];
                    $catalog_no = $row['catalog_no'];
                    $lot_no = $row['lot_no'];
                    $notes = $row['notes'];
                    $user_id = $_SESSION['id'];
                    $current_date = date("Y-m-d H:i:s");

                    // Inserting the information to the database
                    $sql1 = "INSERT INTO inventory (";
                    $sql1 .= "chemical_description, room_no, cabinet_or_asset_no, physical_state, volume_or_size, cas_no, storage_temperature, preparation_date, aliquot_date";
                    $sql1 .= ", receipt_date, open_date, expiration_date, vendor, catalog_no, lot_no, user_id, data_enter_date, last_edit_date, notes";
                    $sql1 .= ") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                    $stmt1 = $db->prepare($sql1);

                    $stmt1->bindValue(1, $chemical_description, PDO::PARAM_STR);
                    $stmt1->bindValue(2, $room_no, PDO::PARAM_STR);
                    $stmt1->bindValue(3, $cabinet_or_asset_no, PDO::PARAM_STR);
                    $stmt1->bindValue(4, $physical_state, PDO::PARAM_STR);
                    $stmt1->bindValue(5, $volume_or_size, PDO::PARAM_STR);
                    $stmt1->bindValue(6, $cas_no, PDO::PARAM_STR);
                    $stmt1->bindValue(7, $storage_temperature, PDO::PARAM_STR);
                    $stmt1->bindValue(8, $preparation_date, PDO::PARAM_STR);
                    $stmt1->bindValue(9, $aliquot_date, PDO::PARAM_STR);
                    $stmt1->bindValue(10, $receipt_date, PDO::PARAM_STR);
                    $stmt1->bindValue(11, $open_date, PDO::PARAM_STR);
                    $stmt1->bindValue(12, $expiration_date, PDO::PARAM_STR);
                    $stmt1->bindValue(13, $vendor, PDO::PARAM_STR);
                    $stmt1->bindValue(14, $catalog_no, PDO::PARAM_STR);
                    $stmt1->bindValue(15, $lot_no, PDO::PARAM_STR);
                    $stmt1->bindValue(16, $user_id, PDO::PARAM_STR);
                    $stmt1->bindValue(17, $current_date, PDO::PARAM_STR);
                    $stmt1->bindValue(18, $current_date, PDO::PARAM_STR);
                    $stmt1->bindValue(19, $notes, PDO::PARAM_STR);
                    $result1 = $stmt1->execute();
                    if (!$result1) {
                        $query_errors++;
                    }
                }
            }
        }

        if ($query_errors == 0) {
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
