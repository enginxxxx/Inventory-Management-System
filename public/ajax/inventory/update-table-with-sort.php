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
        // Getting the parameter passed through AJAX
        $column_name = trim($_GET['column_name']);
        $_SESSION['pagination_page_number'] = 1;

        switch ($column_name) {
            case "Che":
                if ($_SESSION['sort_column_name'] == 'chemical_description' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'chemical_description';
                }
                break;
            case "Roo":
                if ($_SESSION['sort_column_name'] == 'room_no' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'room_no';
                }
                break;
            case "Cab":
                if ($_SESSION['sort_column_name'] == 'cabinet_or_asset_no' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'cabinet_or_asset_no';
                }
                break;
            case "Phy":
                if ($_SESSION['sort_column_name'] == 'physical_state' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'physical_state';
                }
                break;
            case "Vol":
                if ($_SESSION['sort_column_name'] == 'volume_or_size' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'volume_or_size';
                }
                break;
            case "CAS":
                if ($_SESSION['sort_column_name'] == 'cas_no' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'cas_no';
                }
                break;
            case "Sto":
                if ($_SESSION['sort_column_name'] == 'storage_temperature' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'storage_temperature';
                }
                break;
            case "Pre":
                if ($_SESSION['sort_column_name'] == 'preparation_date' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'preparation_date';
                }
                break;
            case "Ali":
                if ($_SESSION['sort_column_name'] == 'aliquot_date' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'aliquot_date';
                }
                break;
            case "Rec":
                if ($_SESSION['sort_column_name'] == 'receipt_date' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'receipt_date';
                }
                break;
            case "Ope":
                if ($_SESSION['sort_column_name'] == 'open_date' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'open_date';
                }
                break;
            case "Exp":
                if ($_SESSION['sort_column_name'] == 'expiration_date' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'expiration_date';
                }
                break;
            case "Ven":
                if ($_SESSION['sort_column_name'] == 'vendor' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'vendor';
                }
                break;
            case "Cat":
                if ($_SESSION['sort_column_name'] == 'catalog_no' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'catalog_no';
                }
                break;
            case "Lot":
                if ($_SESSION['sort_column_name'] == 'lot_no' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'lot_no';
                }
                break;
            case "Not":
                if ($_SESSION['sort_column_name'] == 'notes' && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = 'notes';
                }
                break;
            case "<a>":
                if (($_SESSION['sort_column_name'] == '' || $_SESSION['sort_column_name'] = "last_edit_date") && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = "last_edit_date";
                }
                break;
            case "<a":
                if (($_SESSION['sort_column_name'] == '' || $_SESSION['sort_column_name'] = "last_edit_date") && $_SESSION['sort_up_or_down'] == 'up') {
                    $_SESSION['sort_up_or_down'] = 'down';
                } else {
                    $_SESSION['sort_up_or_down'] = 'up';
                    $_SESSION['sort_column_name'] = "last_edit_date";
                }
                break;
            case "":
                $_SESSION['sort_column_name'] = "";
                break;
        }

        ob_start();
        require_once('../../../private/require/inventory-table-body-query.php');
        $json_response['html_tbody'] = ob_get_clean();
        $json_response['html_pagination'] = $pagination;
        $json_response['status'] = 'success';
        $json_response['up_or_down'] = $_SESSION['sort_up_or_down'];
    }
    echo json_encode($json_response);
} else {
    echo "Direct access is not permitted";
}
?>
