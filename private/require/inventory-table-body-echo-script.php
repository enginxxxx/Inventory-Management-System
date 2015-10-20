<?php

/* ================================================================ */
/* Created by Engin Yapici on 02/11/2015                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */

$row_number = $pagination_start_point + 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idItem = $row['id'];
    $itemName = $row['chemical_description'];
    $roomNo = $row['room_no'];
    $cabinetOrAssetNo = $row['cabinet_or_asset_no'];
    $volumeOrSize = $row['volume_or_size'];
    $casNo = $row['cas_no'];
    $storageTemperature = $row['storage_temperature'];
    $preparation_date = convert_mysql_date_to_php_date($row['preparation_date']);
    $aliquot_date = convert_mysql_date_to_php_date($row['aliquot_date']);
    $receipt_date = convert_mysql_date_to_php_date($row['receipt_date']);
    $open_date = convert_mysql_date_to_php_date($row['open_date']);
    $expiration_date = convert_mysql_date_to_php_date($row['expiration_date']);
    $vendor = $row['vendor'];
    $catalogNo = $row['catalog_no'];
    $lotNo = $row['lot_no'];
    $notes = $row['notes'];

    echo "<tr id='$idItem'>";

    echo "<td class='selector-cell noselect'>" . $row_number . ".</td>";

    /* Chemical Description */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$itemName' class='data-table-editable-text-input' id='item-chemical-description-$idItem' title='$itemName'/>"
    . "<span>$itemName</span></td>";
    /* Chemical Description */


    /* Room No */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$roomNo' class='data-table-editable-text-input' id='item-room-no-$idItem' title='$roomNo'/>"
    . "<span>$roomNo</span></td>";
    /* Room No */


    /* Cabinet/Asset No */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$cabinetOrAssetNo' class='data-table-editable-text-input' id='item-cabinet-or-asset-no-$idItem' title='$cabinetOrAssetNo'/>"
    . "<span>$cabinetOrAssetNo</span></td>";
    /* Cabinet/Asset No */


    /* Physical State */
    echo "<td class='selectable-cell'>"
    . "<select id='item-physical-state-$idItem' class='data-table-editable-text-input'>";
    if ($row['physical_state'] == 'Liquid') {
        echo "<option selected name='Liquid'>Liquid</option>";
        echo "<option name='Solid'>Solid</option>";
        echo "<option name='Gas'>Gas</option>";
        echo "<option name='Multiple Components'>Multiple Components</option>";
        echo "<option name='N/A'>N/A</option>";
        echo "</select><span>Liquid</span>";
    } else if ($row['physical_state'] == 'Solid') {
        echo "<option name='Liquid'>Liquid</option>";
        echo "<option selected name='Solid'>Solid</option>";
        echo "<option name='Gas'>Gas</option>";
        echo "<option name='Multiple Components'>Multiple Components</option>";
        echo "<option name='N/A'>N/A</option>";
        echo "</select><span>Solid</span>";
    } else if ($row['physical_state'] == 'Gas') {
        echo "<option name='Liquid'>Liquid</option>";
        echo "<option name='Solid'>Solid</option>";
        echo "<option selected name='Gas'>Gas</option>";
        echo "<option name='Multiple Components'>Multiple Components</option>";
        echo "<option name='N/A'>N/A</option>";
        echo "</select><span>Gas</span>";
    } else if ($row['physical_state'] == 'Multiple Components') {
        echo "<option name='Liquid'>Liquid</option>";
        echo "<option name='Solid'>Solid</option>";
        echo "<option name='Gas'>Gas</option>";
        echo "<option selected name='Multiple Components'>Multiple Components</option>";
        echo "<option name='N/A'>N/A</option>";
        echo "</select><span>Multiple Components</span>";
    } else {
        echo "<option name='Liquid'>Liquid</option>";
        echo "<option name='Solid'>Solid</option>";
        echo "<option name='Gas'>Gas</option>";
        echo "<option name='Multiple Components'>Multiple Components</option>";
        echo "<option selected name='N/A'>N/A</option>";
        echo "</select><span>N/A</span>";
    }
    echo "</td>";
    /* Physical State */


    /* Volume/Size */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$volumeOrSize' class='data-table-editable-text-input' id='item-volume-or-size-$idItem' title='$volumeOrSize'/>"
    . "<span>$volumeOrSize</span></td>";
    /* Volume/Size */


    /* CAS No */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$casNo' class='data-table-editable-text-input' id='item-cas-no-$idItem' title='$casNo'/>"
    . "<span>$casNo</span></td>";
    /* CAS No */


    /* Storage Temperature */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$storageTemperature' class='data-table-editable-text-input' id='item-storage-temperature-$idItem' title='$storageTemperature'/>"
    . "<span>$storageTemperature</span></td>";
    /* Storage Temperature */


    /* Preparation Date */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$preparation_date' id='item-preparation-date-$idItem' class='datepicker data-table-editable-text-input'/>"
    . "<span>$preparation_date</span></td>";
    /* Preparation Date */


    /* Aliquot Date */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$aliquot_date' id='item-aliquot-date-$idItem' class='datepicker data-table-editable-text-input'/>"
    . "<span>$aliquot_date</span></td>";
    /* Aliquot Date */


    /* Receipt Date */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$receipt_date' id='item-receipt-date-$idItem' class='datepicker data-table-editable-text-input'/>"
    . "<span>$receipt_date</span></td>";
    /* Receipt Date */


    /* Open Date */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$open_date' id='item-open-date-$idItem' class='datepicker data-table-editable-text-input'/>"
    . "<span>$open_date</span></td>";
    /* Open Date */


    /* Expiration Date */
    echo "<td class='selectable-cell";
    if (strtotime($row['expiration_date']) < time()) {
        echo " expired";
    } else if (strtotime($row['expiration_date']) - time() < (60 * 60 * 24 * 30)) {
        echo " about-to-expire";
    }
    echo "'>"
    . "<input type='text' value='$expiration_date' id='item-expiration-date-$idItem' class='datepicker data-table-editable-text-input'/>"
    . "<span>$expiration_date</span></td>";
    /* Expiration Date */


    /* Vendor */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$vendor' class='data-table-editable-text-input' id='item-vendor-$idItem' title='$vendor'/>"
    . "<span>$vendor</span></td>";
    /* Vendor */


    /* Catalog No */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$catalogNo' class='data-table-editable-text-input' id='item-catalog-no-$idItem' title='$catalogNo'/>"
    . "<span>$catalogNo</span></td>";
    /* Catalog No */


    /* Lot No */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$lotNo' class='data-table-editable-text-input' id='item-lot-no-$idItem' title='$lotNo'/>"
    . "<span>$lotNo</span></td>";
    /* Lot No */


    /* Notes */
    echo "<td class='selectable-cell'>"
    . "<input type='text' value='$notes' class='data-table-editable-text-input' id='item-notes-$idItem' title='$notes'/>"
    . "<span>$notes</span></td>";
    /* Notes */

    echo "</tr>";
    $zIndex = $zIndex - 1;
    $row_number++;
}
?>