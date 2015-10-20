<?php
/* ================================================================ */
/* Created by Engin Yapici on 11/04/2014                            */
/* Last modified by Engin Yapici on 10/16/2015                      */
/* Copyright Engin Yapici, 2015.                                    */
/* enginyapici@gmail.com                                            */
/* ================================================================ */
require_once('../private/include/session_functions.php');
require_once('../private/include/include.php');
$target = 'index.php';
if (!is_session_valid()) {
    header("Location: /$target");
} else {
    unset($_SESSION['undo_items_array']);
    $_SESSION['undo_items_array'] = array();
}
//if (!last_login_is_recent()) {
//    after_successful_logout();
//    header("Location: /$target");
//}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
    <head>
        <title>Inventory Management System</title>
        <?php require_once ('include_references.php'); ?>
    </head>

    <body>
        <div class="gray-out-div"></div>
        <img class="progress-circle" src="images/ajax-loader.gif"/>
        <ul class='custom-menu'>
            <li data-action="delete"><img src='images/trash-icon.png'/><span>Delete</span></li>
            <li data-action="copy"><img src='images/copy-icon.png'/><span>Make a Copy</span></li>
        </ul>
        <div id='delete-confirmation-popup-window'>
            <h1>Confirm Delete</h1>
            <div id='delete-confirmation-popup-window-text-div'>Are you sure to delete the selected item?</div>
            <div><a class='button' onclick='deleteItem()'>Yes</a>
                <a class='button' onclick='cancelDelete()'>Cancel</a></div>
        </div>
        <div id='login-popup-window'>
            <h1>Session Expired</h1>
            <div id='login-popup-window-text-div'>Your session has expired. Please log in again.</div>
            <div><input id="popup-login-email" type="text" placeholder="E-mail Address"/></div>
            <div><input id="popup-login-password" type="password" placeholder="Password"/></div>
            <div><a class="button" onclick="popupLoginUser()">Submit</a></div>
            <div class="error-div" id="popup-login-error-div">&nbsp;</div>
        </div>
        <div id='invisible-input-id-holder'></div>
        <?php require_once ('header.php');
        ?>
        <div id="inventory-main-body-wrapper" class='noselect'>
            <span><img src='images/logout-button.png' id='logout-button' title='Log Out' onclick='logoutAction();'></img></span>
            <a class="button" id="fit-to-contents-button" onclick="fitScreen()" style="display: none;">Fit Contents to Window</a>
            <div class="search-elements-wrapper">
                <input class='search-input' id="inventory-search-input" placeholder="Search"
                <?php
                if (isset($_SESSION['search_keywords'])) {
                    echo "value='" . $_SESSION['search_keywords'] . "'";
                }
                ?>
                       />

                <a class='search-cancel-button' id='inventory-search-cancel-button' <?php
                if (!isset($_SESSION['search_keywords']) || $_SESSION['search_keywords'] == "" || $_SESSION['search_keywords'] == "Search") {
                    echo "style='display: none;'";
                }
                ?>onclick='searchAction("clear")'>Clear</a>
                <a class="button search-button" onclick='searchAction("search")'><img src="images/search_icon.png"/></a>
            </div>
            <div id='inventory-list-wrapper-div'>
                <table id='inventory-table'>
                    <?php
                    // I need to echo the tbody before thead because I get the pagination html script by using the body query.
                    // tbody start
                    echo "<tbody>";
                    require_once('../private/require/inventory-table-body-query.php');
                    echo "</tbody>";
                    // tbody end
                    ?>
                    <thead>
                        <!-- Add new item row -->
                        <tr id='add-new-item-row'>
                            <td id='add-new-item-save-button' onclick="saveNewItem()">+</td>
                            <td><input type='text' id='add-new-item-chemical-description-input' placeholder='Chemical Description' maxlength='300'/>
                                <div id="add-new-item-error-div" class="error-div">&nbsp;</div>
                            </td>
                            <td><input type='text' id='add-new-item-room-no-input' placeholder='Room No' maxlength='30'/></td>
                            <td><input type='text' id='add-new-item-cabinet-or-asset-no-input' placeholder='Cabinet/Asset No' maxlength='200'/></td>
                            <td>
                                <select id='add-new-item-physical-state-input'>
                                    <option value="" disabled selected>Physical State</option>
                                    <option name='Liquid'>Liquid</option>
                                    <option name='Solid'>Solid</option>
                                    <option name='Gas'>Gas</option>
                                    <option name='Multiple Components'>Multiple Components</option>
                                    <option name='N/A'>N/A</option>
                                </select>
                            </td>
                            <td><input type='text' id='add-new-item-volume-or-size-input' placeholder='Volume/Size' maxlength='20'/></td>
                            <td><input type='text' id='add-new-item-cas-no-input' placeholder='CAS No' maxlength='100'/>
                                <span>
                                    <input type="checkbox" id="cas-na-checkbox"/>
                                    <label for="cas-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td><input type='text' id='add-new-item-storage-temperature-input' placeholder='Storage Temp. (&deg;C)' maxlength='20'/></td>
                            <td>
                                <input type='text' id='add-new-item-preparation-date-input' class='datepicker' placeholder='Prep. Date' maxlength='100' readonly/>
                                <span>
                                    <input type="checkbox" id="prep-date-na-checkbox"/>
                                    <label for="prep-date-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td>
                                <input type='text' id='add-new-item-aliquot-date-input' class='datepicker' placeholder='Aliquot Date' maxlength='100' readonly/>
                                <span>
                                    <input type="checkbox" id="aliquot-date-na-checkbox"/>
                                    <label for="aliquot-date-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td>
                                <input type='text' id='add-new-item-receipt-date-input' class='datepicker' placeholder='Receipt Date' maxlength='100' readonly/>
                                <span>
                                    <input type="checkbox" id="receipt-date-na-checkbox"/>
                                    <label for="receipt-date-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td>
                                <input type='text' id='add-new-item-open-date-input' class='datepicker' placeholder='Open Date' maxlength='100' readonly/>
                                <span>
                                    <input type="checkbox" id="open-date-na-checkbox"/>
                                    <label for="open-date-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td><input type='text' id='add-new-item-expiration-date-input' class='datepicker' placeholder='Exp. Date' maxlength='100' readonly/></td>
                            <td><input type='text' id='add-new-item-vendor-input' placeholder='Vendor' maxlength='200'/></td>
                            <td><input type='text' id='add-new-item-catalog-no-input' placeholder='Cat. No' maxlength='100'/>
                                <span>
                                    <input type="checkbox" id="catalog-no-na-checkbox"/>
                                    <label for="catalog-no-na-checkbox" class='noselect'>N/A</label>
                                </span>
                            </td>
                            <td><input type='text' id='add-new-item-lot-no-input' placeholder='Lot No' maxlength='200'/></td>
                            <td><input type='text' id='add-new-item-notes-input' placeholder='Notes' maxlength='100'/></td>
                        </tr>
                        <!-- Add new item row -->

                        <?php
                        /* Below row is used as a gap between the header row and the 'add new item' row. */
                        echo "<tr><td id='pagination-holder-td' colspan='17'>"
                        . "$pagination</td></tr>";
                        echo "<tr><td colspan='17'><div><input id='edit-input' type='text'></input></div></td></tr>";
                        echo "<tr><td colspan='17'></td></tr>";
                        echo "<tr class='noselect'><td onclick='sortByColumn($(this))'><a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Chemical Description <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Room No <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Cabinet/Asset No <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Physical State <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Volume/Size <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>CAS No <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Storage Temp. (&deg;C) <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Prep. Date <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Aliquot Date <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Receipt Date <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Open Date <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Exp. Date <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Vendor <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Catalog No <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Lot No <a>&#9650;</a></td>";
                        echo "<td onclick='sortByColumn($(this))'>Notes <a>&#9650;</a></td></tr></thead>";


                        echo "<tfoot><tr class='noselect'><td></td><td>Chemical Description</td>";
                        echo "<td>Room No</td>";
                        echo "<td>Cabinet/Asset No</td>";
                        echo "<td>Physical State</td>";
                        echo "<td>Volume/Size</td>";
                        echo "<td>CAS No</td>";
                        echo "<td>Storage Temp. (&deg;C)</td>";
                        echo "<td>Prep. Date</td>";
                        echo "<td>Aliquot Date</td>";
                        echo "<td>Receipt Date</td>";
                        echo "<td>Open Date</td>";
                        echo "<td>Exp. Date</td>";
                        echo "<td>Vendor</td>";
                        echo "<td>Catalog No</td>";
                        echo "<td>Lot No</td>";
                        echo "<td>Notes</td></tr></tfoot>";
                        ?>
                </table>

                <a class='button' id='print-button' onclick='pdfprint()'>Print</a>
            </div>
        </div>
    </body>
</html>

