<?php

/* ================================================================ */
/* Created by Engin Yapici on 01/04/2015                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

$selected_room = $_SESSION['selected_room'];
$html_content = "";
$is_any_room_selected = 0;
$sql = "SELECT DISTINCT room_no FROM inventory WHERE deleted = 0 ORDER BY room_no DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $room_no = $row['room_no'];

    if ($selected_room == $room_no) {
        $html_content .= "<option selected class='selected'>$room_no</option>";
        $is_any_room_selected++;
    } else {
        $html_content .= "<option>$room_no</option>";
    }
}

if ($is_any_room_selected == 0) {
    echo "<select><option slected class='selected'>All</option>" . $html_content . "</select>";
} else {
    echo "<select><option>All</option>" . $html_content . "</select>";
}




?>