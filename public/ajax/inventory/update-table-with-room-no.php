<?php

/* ================================================================ */
/* Created by Engin Yapici on 01/04/2015                            */
/* Last modified by Engin Yapici on 10/14/2015                      */
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
        // Getting the parameter passed through AJAX
        $room_no = html_entity_decode(trim($_GET['room_no']));
        if ($room_no == 'All') {
            $room_no = "%";
        }
        $_SESSION['selected_room'] = $room_no;
        $_SESSION['pagination_page_number'] = 1;
        $_SESSION['search_keywords'] = "";
        $_SESSION['sort_column_name'] = "";

        ob_start();
        require_once('../../../private/require/inventory-table-body-query.php');
        $json_response['html_tbody'] = ob_get_clean();
        $json_response['html_pagination'] = $pagination;
        $json_response['status'] = "success";
    }
    echo json_encode($json_response);
} else {
    echo "Direct access is not permitted";
}
?>
