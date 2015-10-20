<?php

/* ================================================================ */
/* Created by Engin Yapici on 12/07/2014                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */



$pagination_page_number = 1;
$pagination_start_point = 0;
$number_of_items_per_page = 25;

if (isset($_SESSION['selected_cabinet'])) {
    $selected_cabinet = $_SESSION['selected_cabinet'];
} else {
    $selected_cabinet = '%';
}

if (isset($_SESSION['selected_room'])) {
    $selected_room = $_SESSION['selected_room'];
} else {
    $selected_room = '%';
}

if (isset($_SESSION['pagination_page_number'])) {
    $pagination_page_number = $_SESSION['pagination_page_number'];
    $pagination_start_point = ($pagination_page_number - 1) * $number_of_items_per_page;
}

$sort_sql_string = "ORDER BY last_edit_date DESC ";
if (isset($_SESSION['sort_column_name']) && $_SESSION['sort_column_name'] != "") {
    $sort_sql_string = "ORDER BY " . $_SESSION['sort_column_name'];
    if ($_SESSION['sort_up_or_down'] == 'up') {
        $sort_sql_string .= " ASC ";        
    } else {
        $sort_sql_string .= " DESC ";
    }
}

/* ############################################################ Search ############################################################# */
/* | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | */
/* V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V */
$columns = ['chemical_description',
    'room_no',
    'cabinet_or_asset_no',
    'physical_state',
    'volume_or_size',
    'cas_no',
    'storage_temperature',
    'preparation_date',
    'aliquot_date',
    'receipt_date',
    'open_date',
    'expiration_date',
    'vendor',
    'catalog_no',
    'lot_no',
    'notes'];
$search_sql_string = "";
$search_keywords_array = array();
if (isset($_SESSION['search_keywords']) && $_SESSION['search_keywords'] != "" && $_SESSION['search_keywords'] != "Search") {
    $search_keywords_string = $_SESSION['search_keywords'];
    $search_keywords_array = preg_split('/[\s]+/', $search_keywords_string);
    for ($i = 0; $i < count($search_keywords_array); $i++) {
        $search_sql_string .= "AND (";
        for ($k = 0; $k < count($columns); $k++) {
            $search_sql_string .= $columns[$k] . " LIKE :keyword" . $i . $k . " OR ";
        }
        $search_sql_string = substr_replace($search_sql_string, "", -3); // to remove the 'OR' at the end.
        $search_sql_string .= ") ";
    }
} else {
    $search_keywords = "%";
}
/* ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ */
/* | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | */
/* ############################################################ Search ############################################################# */

$sql = "SELECT * FROM inventory WHERE deleted = 0 AND "
        . "cabinet_or_asset_no LIKE :cabinet AND "
        . "room_no LIKE :room "
        . $search_sql_string
        . $sort_sql_string
        . "LIMIT :start, :item_number";

$stmt = $db->prepare($sql);
$stmt->bindValue(':cabinet', $selected_cabinet, PDO::PARAM_STR);
$stmt->bindValue(':room', $selected_room, PDO::PARAM_STR);
for ($i = 0; $i < count($search_keywords_array); $i++) {
    $keyword = $search_keywords_array[$i];
    for ($k = 0; $k < count($columns); $k++) {
        $param_name = ":keyword" . $i . $k;
        $col_name = substr($columns[$k], -4);
        if ($col_name == "date") {
            $date_keyword = convert_str_date_to_mysql_date($keyword);
            $stmt->bindValue($param_name, $date_keyword, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($param_name, "%$keyword%", PDO::PARAM_INT);
        }
    }
}
$stmt->bindValue(':start', $pagination_start_point, PDO::PARAM_INT);
$stmt->bindValue(':item_number', $number_of_items_per_page, PDO::PARAM_INT);

$stmt->execute();
$total_number_of_items = getTotalNumberOfItems($db, $selected_cabinet, $selected_room, $search_sql_string, $search_keywords_array, $columns);

require_once('inventory-table-body-echo-script.php');

/* ########################################################## Pagination ########################################################### */
/* | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | */
/* V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V V */
require_once('inventory-table-pagination.php');
/* ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ ^ */
/* | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | | */
/* ########################################################## Pagination ########################################################### */

function getTotalNumberOfItems($db, $selected_cabinet, $selected_room, $search_sql_string, $search_keywords_array, $columns) {
    $sql = "SELECT * FROM inventory WHERE deleted = 0 AND "
            . "cabinet_or_asset_no LIKE :cabinet AND "
            . "room_no LIKE :room "
            . $search_sql_string
            . " ORDER BY last_edit_date DESC ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':cabinet', $selected_cabinet, PDO::PARAM_STR);
    $stmt->bindValue(':room', $selected_room, PDO::PARAM_STR);
    for ($i = 0; $i < count($search_keywords_array); $i++) {
        $keyword = $search_keywords_array[$i];
        for ($k = 0; $k < count($columns); $k++) {
            $param_name = ":keyword" . $i . $k;
            $stmt->bindValue($param_name, "%$keyword%", PDO::PARAM_INT);
        }
    }
    $stmt->execute();
    return $stmt->rowCount();
}

?>