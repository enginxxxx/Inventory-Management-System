<?php

/* ================================================================ */
/* Created by Engin Yapici on 01/04/2015                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

$selected_cabinet = $_SESSION['selected_cabinet'];
$html_content = "";
$is_any_cabinet_selected = 0;
$sql = "SELECT DISTINCT cabinet_or_asset_no FROM inventory WHERE deleted = 0 ORDER BY cabinet_or_asset_no DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cabinet_or_asset_no = $row['cabinet_or_asset_no'];

    if ($selected_cabinet == $cabinet_or_asset_no) {
        $html_content .= "<option selected class='selected'>$cabinet_or_asset_no</option>";
        $is_any_cabinet_selected++;
    } else {
        $html_content .= "<option>$cabinet_or_asset_no</option>";
    }
}

if ($is_any_cabinet_selected == 0) {
    echo "<select><option selected class='selected'>All</option>" . $html_content . "</select>";
} else {
    echo "<select><option>All</option>" . $html_content . "</select>";
}
?>