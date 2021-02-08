<?php
require_once("../private/include/external/mpdf/mpdf.php");
require_once('../private/include/include.php');
$selected_cabinet_db;
$selected_room = trim($_GET['room']);
if ($selected_room == 'All') {
    $selected_room = "%";
}
$selected_cabinet_db = $selected_cabinet = html_entity_decode(trim($_GET['cabinet']));
if ($selected_cabinet == 'All') {
    $selected_cabinet_db = "%";
}
date_default_timezone_set('America/Chicago');

// Needed to create large pdf files.
ini_set("memory_limit", "256M");


//echo $selected_room . '/' . $selected_cabinet;
$mpdf = new mPDF('c', 'A4-L', 0, '', 6, 6, 6, 8, 0, 3);
$table = "<div id='header'>" . date('d-M-Y') . " - " . $selected_cabinet . " Inventory</div>";
$table .= "<table id='inventory-table'>";
// Header
$table .= "<thead>";
$table .= "<tr><td class='hidden'></td><td>Chemical Description</td>";
$table .= "<td>Room No</td>";
$table .= "<td>Cabinet/Asset No</td>";
$table .= "<td>Physical State</td>";
$table .= "<td>Volume/Size</td>";
$table .= "<td>CAS No</td>";
$table .= "<td>Storage Temp. (&deg;C)</td>";
$table .= "<td>Prep. Date</td>";
$table .= "<td>Aliquot Date</td>";
$table .= "<td>Receipt Date</td>";
$table .= "<td>Open Date</td>";
$table .= "<td>Exp. Date</td>";
$table .= "<td>Vendor</td>";
$table .= "<td>Catalog No</td>";
$table .= "<td>Lot No</td></tr></thead>";


// tbody start
$sql = "SELECT * FROM inventory WHERE deleted = 0 and cabinet_or_asset_no LIKE ? and room_no LIKE ? ORDER BY last_edit_date DESC";
$stmt = $db->prepare($sql);
$stmt->bindValue(1, $selected_cabinet_db, PDO::PARAM_STR);
$stmt->bindValue(2, $selected_room, PDO::PARAM_STR);
$stmt->execute();
$row_number = 1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $idItem = $row['id'];
    $itemName = $row['chemical_description'];
    $roomNo = $row['room_no'];
    $cabinetOrAssetNo = $row['cabinet_or_asset_no'];
    $physical_state = $row['physical_state'];
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

    $table .= "<tr id='tbody'>";
    $table .= "<td class='hidden'>$row_number</td>";
    $table .= "<td>$itemName</td>";
    $table .= "<td>$roomNo</td>";
    $table .= "<td>$cabinetOrAssetNo</td>";
    $table .= "<td>$physical_state</td>";
    $table .= "<td>$volumeOrSize</td>";
    $table .= "<td>$casNo</td>";
    $table .= "<td>$storageTemperature</td>";
    $table .= "<td>$preparation_date</td>";
    $table .= "<td>$aliquot_date</td>";
    $table .= "<td>$receipt_date</td>";
    $table .= "<td>$open_date</td>";
    $table .= "<td>$expiration_date</td>";
    $table .= "<td>$vendor</td>";
    $table .= "<td>$catalogNo</td>";
    $table .= "<td>$lotNo</td>";
    $table .= "</tr>";
    $row_number++;
}
// tbody end
// Footer
$table .= "<tfoot><tr><td class='hidden'></td>";
$table .= "<td>Chemical Description</td>";
$table .= "<td>Room No</td>";
$table .= "<td>Cabinet/Asset No</td>";
$table .= "<td>Physical State</td>";
$table .= "<td>Volume/Size</td>";
$table .= "<td>CAS No</td>";
$table .= "<td>Storage Temp. (&deg;C)</td>";
$table .= "<td>Prep. Date</td>";
$table .= "<td>Aliquot Date</td>";
$table .= "<td>Receipt Date</td>";
$table .= "<td>Open Date</td>";
$table .= "<td>Exp. Date</td>";
$table .= "<td>Vendor</td>";
$table .= "<td>Catalog No</td>";
$table .= "<td>Lot No</td></tr></tfoot></table>";

$footer = "<div id='footer'>{PAGENO} / {nb}</div>";


$stylesheet = file_get_contents('css/inventory-print.css');
$mpdf->SetHTMLFooter('
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;"><tr>
<td width="33%"><span style="font-weight: bold; font-style: italic;">Printed on ' . date('d-M-Y h:i:s a') . '</span></td>
<td width="33%" align="center" style="font-weight: bold; font-style: italic;">Page {PAGENO} of {nbpg}</td>
<td width="33%" style="text-align: right; ">' . date('d-M-Y') . ' - ' . $selected_cabinet . ' Inventory</td>
</tr></table>
');
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML($table, 2);
$filename = date('d/M/Y') . ' - ' . $selected_cabinet;
$mpdf->Output($filename, 'I');
exit;
?>