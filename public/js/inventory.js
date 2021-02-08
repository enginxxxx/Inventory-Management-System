var howDoesItWorkPageNumber = 0;
var isHowDoesItWorkActive = false;
var inClick = false;

var global_session_selected_room;
var global_session_selected_cabinet;

var ADD_NEW_ITEM_ACTION_FLAG = 1;
var COPY_ITEM_ACTION_FLAG = 2;
var DELETE_ITEM_ACTION_FLAG = 3;
var UPDATE_ITEM_INFO_ACTION_FLAG = 4;
var UPDATE_TABLE_WITH_ROOM_NO_ACTION_FLAG = 5;
var UPDATE_TABLE_WITH_CABINET_NAME_ACTION_FLAG = 6;
var UPDATE_TABLE_WITH_PAGE_NO_ACTION_FLAG = 7;
var UPDATE_TABLE_WITH_SORT_ACTION_FLAG = 8;
var UPDATE_TABLE_WITH_SEARCH_ACTION_FLAG = 9;
var expiredSessionActionItemFlag = 0;

var global_go_to_page = 1;
var global_update_table_with_room_no_room_number;
var global_update_table_with_room_no_element;
var global_update_table_with_cabinet_name;
var global_update_table_with_cabinet_name_element;
var global_search_action_type;
var global_update_table_with_sort_element;

var selectedRowIds = [];
var wasDeleteButtonClicked = false;
var fit_contents_to_window_ratio = 0;

$(document).ready(function() {
    // Add new item physical state drop down selector placeholder styling
    itemPhysicalStateSelectSetup();

    $("#header-cabinets-list-div select").on("change", function() {
        updateTableWithCabinetName($(this).val(), $(this));
    });

    $("#header-rooms-list-div select").on("change", function() {
        updateTableWithRoomNo($(this).val(), $(this));
    });

    document.onkeydown = function(e) {
        e = e || window.event;
        switch (e.keyCode) {
            case 39: // Right arrow key
                if (isHowDoesItWorkActive && !inClick) {
                    inClick = true;
                    howDoesItWorkNext();
                }
                break;
            case 27: // ESC key
                if (isHowDoesItWorkActive) {
                    inClick = false;
                    closeHowDoesItWork();
                }
                break;
        }
    };

    $("#inventory-search-input").keyup(function(event) {
        if (event.keyCode === 13) {
            searchAction('search');
        }
    });



    // Inventory table tbody cells hover effect
    $(".selectable-cell").hover(function() {
        $(this).css("background-color", "rgba(245, 245, 245, 0.5)");
    }, function() {
        $(this).css("background-color", "transparent");
    });


    $(document).on('change', '.selectable-cell input', function() {
        updateInfo();
    });

    $(document).on('change', '.selectable-cell select', function() {
        updateInfo();
    });

    $(document).on('change', '#edit-input', function() {
        updateInfo();
    });

    contextMenuFunctions();

    // Datepicker functions
    datepickerFunctions();

    /*-----------------------------------------------------------*/
    /*----------------- N/A checkbox functions ------------------*/
    // N/A checkbox click function
    $(":checkbox").click(function() {
        var input = $(this).parent('span').parent('td').find('input');
        if ($(this).is(":checked")) {
            input.val('N/A');
            input.css('color', '#1C4D6F');
        } else {
            if (input.val() === 'N/A') {
                input.val(input.attr('placeholder'));
                input.css('color', '#aaaaaa');
            }
        }
        $(this).parent('span').fadeOut();
    });
    // Showing the checkbox when the datepicker input is clicked.
    $("#add-new-item-row input").focus(function() {
        $(this).parent('td').find('span').fadeIn(200);
    });
    // Hiding the checkbox as soon as the datepicker input loses focus
    $("#add-new-item-row input").blur(function() {
        $(this).parent('td').find('span').fadeOut(200);
    });
    /*-----------------------------------------------------------*/

    $(document).on('mousedown', '#delete-confirmation-popup-window', function() {
        wasDeleteButtonClicked = true;
    });

    $(document).on('mousedown', '.gray-out-div', function() {
        wasDeleteButtonClicked = true;
    });

    $(document).on('focus', '#add-new-item-row input', function() {
        if ($(this).attr('type') !== 'checkbox') {
            $(this).parent().css('border-bottom', '2px solid #006DBD');
        }
    });

    $(document).on('blur', '#add-new-item-row input', function() {
        if ($(this).attr('type') !== 'checkbox') {
            $(this).parent().css('border-bottom', '2px solid #aaaaaa');
        }
    });

    // dataTable initialization
    //initDataTable();
    // Table column resize
    resizableTable();
    // Assigning tabindex values to all the inputs in the page.
    assignTabindex();

    selectRowFunctions();
    editFieldFunctions();

    popupLoginWindowInputFunctions();

    if ($("body").width() > $(window).width()) {
        $("#fit-to-contents-button").show();
    }
});

function fitScreen() {
    if (fit_contents_to_window_ratio === 0) {
        fit_contents_to_window_ratio = $(window).width() / $('body').width();
        $('body').css('zoom', fit_contents_to_window_ratio);
    } else {
        $('body').css('zoom', 1.0);
        fit_contents_to_window_ratio = 0;
    }

}

const prepDate = (dateString) => {
    const date = moment(dateString).format("YYYY-MM-DD");
    if (date === "Invalid date") return "0";
    return date;
}

function undoAction() {
    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/undo-action.php",
        type: "GET",
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
            } else if (json_data.status === 'success') {
                var item_id = json_data.items_array['item_id'];
                var field_name = json_data.items_array['field_name'];
                field_name = 'item-' + field_name.replace(/_/g, '-') + '-' + item_id;
                updateInfoForUndo(field_name, json_data.items_array['old_value'], json_data.items_array['last_item_was_used']);
                unblockUI();
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function updateInfoForUndo(inputId, oldValue, lastItemInUndoList) {
    var arr = inputId.split('-');
    var itemId = arr[arr.length - 1];
    var fieldName = "";
    var isDate = 0;
    var value = encodeURIComponent(oldValue);
    for (var i = 1; i < arr.length - 1; i++) {
        fieldName += arr[i] + '_';
    }
    fieldName = fieldName.slice(0, -1);
    if ($('#' + inputId).hasClass('datepicker')) {
        isDate = 1;
        value = prepDate(value);
    }

    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    $.ajax({
        url: "ajax/inventory/update-item-info-action.php",
        type: "POST",
        data: "item_id=" + itemId +
                "&field_name=" + fieldName +
                "&value=" + value +
                "&is_date=" + isDate +
                "&is_undo=true",
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
            } else if (json_data.status === 'success') {
                $('#' + inputId).val(oldValue);
                if (lastItemInUndoList === 'true') {
                    $('#undo-button').hide(200);
                }
                $('#edit-input').val('');
                $('#' + inputId).css('background-color', "rgba(204, 229, 255, 1)");
                setTimeout(function() {
                    $('#' + inputId).css('background-color', "rgba(245, 245, 245, 0)");
                }, 2000);
                unblockUI();
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function pdfprint() {
    showProgressCircle();
    blockUI();
    var selected_room = $("#header-rooms-list-div select").val();
    var selected_cabinet = $("#header-cabinets-list-div select").val();
    window.location = "print-pdf.php?room=" + selected_room + '&cabinet=' + encodeURIComponent(selected_cabinet);
}

function howDoesItWork() {
    blockUI();
    isHowDoesItWorkActive = true;
    $('#how-does-it-work-close-button').show();
    $('#how-does-it-work-next-button').show();

    // Taking a snapshot of the add-new-item row
    takeSnapshot($("#add-new-item-row"));
    $('#how-does-it-work-informative-text-holder').html('You can add a new item to the inventory in this section. <br> Simply fill all the sections and...');
}

function closeHowDoesItWork() {
    unblockUI();
    isHowDoesItWorkActive = false;
    $('#how-does-it-work-close-button').hide();
    $('#how-does-it-work-next-button').hide();
    $("#how-does-it-work-snapshot-holder").html('');
    $('#how-does-it-work-informative-text-holder').html('');
    $('#header-menu-items p').css('color', '#1C4D6F');
    howDoesItWorkPageNumber = 0;
}

function howDoesItWorkNext() {
    switch (howDoesItWorkPageNumber) {
        case 0:
            takeSnapshot($("#add-new-item-row td:first-child"));
            $('#how-does-it-work-informative-text-holder').html('...click this button. You can click more than once to add the item multiple times without needing to enter the information again.');
            howDoesItWorkPageNumber++;
            break;
        case 1:
            takeSnapshot($("#inventory-table tbody"));
            $('#how-does-it-work-informative-text-holder').html('You can view all the items in this table and edit them as you would edit a spreadsheet. <br> Changes will be automatically saved; no need to click a button.');
            howDoesItWorkPageNumber++;
            break;
        case 2:
            takeSnapshot($("#inventory-table thead tr:last-child"));
            $('#how-does-it-work-informative-text-holder').html('You can reorder the items by clicking on the headers or resize the columns by click-dragging them.');
            //$("#inventory-table_filter input").val('Search');
            howDoesItWorkPageNumber++;
            break;
        case 3:
            takeSnapshot($("#inventory-search-input"));
            $('#how-does-it-work-informative-text-holder').html('You can search the items in the current table by using this search bar.');
            howDoesItWorkPageNumber++;
            break;
        case 4:
            $('#header-menu-items p').css('color', '#ffffff');
            takeSnapshot($("#header-menu-items"));
            $('#how-does-it-work-informative-text-holder').html('You can choose the room and the cabinet number to narrow down the item list in the table.');
            howDoesItWorkPageNumber++;
            break;
        case 5:
            takeSnapshot($("#inventory-table tbody > tr"));
            $('#how-does-it-work-informative-text-holder').html('You can delete items or make copies of them by selecting rows via clicking on this column. <br> You can click-drag or ctrl-click to select multiple items. Give it a try!');
            howDoesItWorkPageNumber++;
            break;
        default:
            closeHowDoesItWork();
    }

}

function takeSnapshot(element) {
    $("#how-does-it-work-snapshot-holder").html('');
    if (howDoesItWorkPageNumber === 5) {
        $("#how-does-it-work-snapshot-holder").css('width', 20);
        $('#inventory-table tbody > tr').each(function() {
            html2canvas($(this).find('td:first-child')[0], {
                onrendered: function(canvas) {
                    document.getElementById("how-does-it-work-snapshot-holder").appendChild(canvas);
                }
            });
        });
    } else {
        html2canvas(element[0], {
            onrendered: function(canvas) {
                document.getElementById("how-does-it-work-snapshot-holder").appendChild(canvas);
            }
        });
    }

    var position = element.offset();
    $("#how-does-it-work-snapshot-holder").css(position);
    $('#how-does-it-work-informative-text-holder').css('left', position.left);
    if (howDoesItWorkPageNumber === 3 || howDoesItWorkPageNumber === 4) { // Search bar and header highlight
        $('#how-does-it-work-informative-text-holder').css('top', position.top + 80);
    } else if (howDoesItWorkPageNumber === 2) {
        $('#how-does-it-work-informative-text-holder').css('top', position.top - 55);
    } else {
        $('#how-does-it-work-informative-text-holder').css('top', position.top - 80);
    }
    setTimeout(function() {
        inClick = false;
    }, 200);
}

function updateTableWithRoomNo(roomNo, element) {
    global_update_table_with_room_no_room_number = roomNo;
    global_update_table_with_room_no_element = element;
    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/update-table-with-room-no.php",
        type: "GET",
        data: "room_no=" + encodeURIComponent(roomNo),
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);
                element.parent('div').find('span').removeClass('selected');
                element.addClass('selected');
                $('#inventory-search-input').val("");
                unblockUI();
            } else if (json_data.status === 'no_session') {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_TABLE_WITH_ROOM_NO_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function updateTableWithCabinetName(cabinetName, element) {
    global_update_table_with_cabinet_name = cabinetName;
    global_update_table_with_cabinet_name_element = element;
    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/update-table-with-cabinet-name.php",
        type: "GET",
        data: "cabinet_name=" + encodeURIComponent(cabinetName),
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);
                element.parent('div').find('span').removeClass('selected');
                element.addClass('selected');
                $('#inventory-search-input').val("");
                unblockUI();
            } else if (json_data.status === 'no_session') {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_TABLE_WITH_CABINET_NAME_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function updateInfo() {
    var inputId = $('#invisible-input-id-holder').html();
    var arr = inputId.split('-');
    var itemId = arr[arr.length - 1];
    var fieldName = "";
    var isDate = 0;
    var value = encodeURIComponent($('#' + inputId).val());
    for (var i = 1; i < arr.length - 1; i++) {
        fieldName += arr[i] + '_';
    }
    fieldName = fieldName.slice(0, -1);
    if ($('#' + inputId).hasClass('datepicker')) {
        isDate = 1;
        value = prepDate(value);
    }

    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    $.ajax({
        url: "ajax/inventory/update-item-info-action.php",
        type: "POST",
        data: "item_id=" + itemId +
                "&field_name=" + fieldName +
                "&value=" + value +
                "&is_date=" + isDate +
                "&is_undo=false",
        cache: false,
        dataType: "json",
        success: function(json_data) {
            //alert(json_data.status);
            if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_ITEM_INFO_ACTION_FLAG;
            } else if (json_data.status === 'success') {
                $('#undo-button').show(200);
                unblockUI();
            } else if (json_data.status === 'values_are_same') {
                //error_div.html(json_data.status);
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function deleteItem() {
    $('#delete-confirmation-popup-window').hide();
    wasDeleteButtonClicked = true;
    var itemIds = '';
    for (var i = 0; i < selectedRowIds.length; i++) {
        itemIds += selectedRowIds[i] + ",";
    }
    itemIds = itemIds.slice(0, -1);

    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/delete-item-action.php",
        type: "POST",
        data: "item_ids=" + itemIds,
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);
                unblockUI();
            } else if (json_data.status === 'no_session') {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = DELETE_ITEM_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function copyItem() {
    wasDeleteButtonClicked = true;
    var itemIds = '';
    for (var i = 0; i < selectedRowIds.length; i++) {
        itemIds += selectedRowIds[i] + ",";
    }
    itemIds = itemIds.slice(0, -1);

    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/copy-item-action.php",
        type: "POST",
        data: "item_ids=" + itemIds,
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);
                unblockUI();
            } else if (json_data.status === 'no_session') {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = COPY_ITEM_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function itemPhysicalStateSelectSetup() {
    var physical_state_input = $("#add-new-item-physical-state-input");
    physical_state_input.css('color', '#aaaaaa');
    physical_state_input.change(function() {
        var value = $(this).val();
        if (value === 'Multiple Components') {
            $('#add-new-item-volume-or-size-input').val('Multiple Components');
            $('#add-new-item-volume-or-size-input').css('color', '#1C4D6F');
            physical_state_input.css('color', '#1C4D6F');
        } else if (value !== 'Physical State') {
            physical_state_input.css('color', '#1C4D6F');
        } else {
            physical_state_input.css('color', '#aaaaaa');
        }
    });
}

function saveNewItem() {
    var chemical_description = encodeURIComponent($("#add-new-item-chemical-description-input").val());
    var room_no = encodeURIComponent($("#add-new-item-room-no-input").val());
    var cabinet_or_asset_no = encodeURIComponent($("#add-new-item-cabinet-or-asset-no-input").val());
    var physical_state = $("#add-new-item-physical-state-input").val();
    var volume_or_size = encodeURIComponent($("#add-new-item-volume-or-size-input").val());
    var cas_no = encodeURIComponent($("#add-new-item-cas-no-input").val());
    var storage_temperature = encodeURIComponent($("#add-new-item-storage-temperature-input").val());
    var preparation_date = prepDate($("#add-new-item-preparation-date-input").val());
    var aliquot_date = prepDate($("#add-new-item-aliquot-date-input").val());
    var receipt_date = prepDate($("#add-new-item-receipt-date-input").val());
    var open_date = prepDate($("#add-new-item-open-date-input").val());
    var expiration_date = prepDate($("#add-new-item-expiration-date-input").val());
    var vendor = encodeURIComponent($("#add-new-item-vendor-input").val());
    var catalog_no = encodeURIComponent($("#add-new-item-catalog-no-input").val());
    var lot_no = encodeURIComponent($("#add-new-item-lot-no-input").val());
    var notes = $("#add-new-item-notes-input").val();
    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');
    if (chemical_description === 'Chemical Description' || room_no === 'Room No' || cabinet_or_asset_no === 'Cabinet/Asset No'
            || physical_state === 'Physical State' || volume_or_size === 'Volume/Size' || cas_no === 'CAS No'
            || storage_temperature === 'Storage Temp. (°C)' || preparation_date === 'Prep. Date' || aliquot_date === 'Aliquot Date'
            || receipt_date === 'Receipt Date' || open_date === 'Open Date' || expiration_date === 'Exp. Date'
            || vendor === 'Vendor' || catalog_no === 'Cat. No' || lot_no === 'Lot No' || physical_state === null) {
        error_div.html("Please fill all the fields");
    } else {
        if (notes === "Notes") {
            notes = "";
        }
        showProgressCircle();
        blockUI();
        $.ajax({
            url: "ajax/inventory/save-new-item-action.php",
            type: "POST",
            data: "chemical_description=" + chemical_description +
                    "&room_no=" + room_no +
                    "&cabinet_or_asset_no=" + cabinet_or_asset_no +
                    "&physical_state=" + physical_state +
                    "&volume_or_size=" + volume_or_size +
                    "&cas_no=" + cas_no +
                    "&storage_temperature=" + storage_temperature +
                    "&preparation_date=" + preparation_date +
                    "&aliquot_date=" + aliquot_date +
                    "&receipt_date=" + receipt_date +
                    "&open_date=" + open_date +
                    "&expiration_date=" + expiration_date +
                    "&vendor=" + vendor +
                    "&catalog_no=" + catalog_no +
                    "&lot_no=" + lot_no +
                    "&notes=" + notes,
            cache: false,
            dataType: "json",
            success: function(json_data) {
                if (json_data.status === 'success') {
                    $('#inventory-table tbody').html(json_data.html_tbody);
                    $("#pagination-holder-td").html(json_data.html_pagination);
                    unblockUI();
                } else if (json_data.status === "no_session") {
                    showLoginPopupWindow();
                    global_session_selected_room = json_data.selected_room;
                    global_session_selected_cabinet = json_data.selected_cabinet;
                    expiredSessionActionItemFlag = ADD_NEW_ITEM_ACTION_FLAG;
                } else {
                    error_div.html(SERVER_FAIL_RESPONSE);
                    unblockUI();
                }
                hideProgressCircle();
            }
        });
    }
}

function go_to_page(page_number) {
    global_go_to_page = page_number;
    var error_div = $('#add-new-item-error-div');
    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/update-table-with-page-number.php",
        type: "GET",
        data: "page_number=" + page_number,
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);
                unblockUI();
            } else if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_TABLE_WITH_PAGE_NO_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function searchAction(action) {
    global_search_action_type = action;
    var error_div = $('#add-new-item-error-div');
    var keywords;
    if (action === 'search') {
        keywords = $('#inventory-search-input').val();
    } else {
        keywords = "";
    }
    
    // Going through each keyword, check if any one of them is a date,
    // if it is, replacing it with the properly formatted date string;
    // i.e., YYYY-MM-DD.
    keywords_array = keywords.split(" ");
    keywords_array.forEach((item, index) => {
        const date = moment(item).format("YYYY-MM-DD");
        const dateCast = new Date(date);
        const isDate = dateCast instanceof Date && !isNaN(dateCast.valueOf());
        if (isDate) keywords_array[index] = date;
    });
    keywords = keywords_array.join(" ");

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/update-table-with-search.php",
        type: "GET",
        data: "search_keywords=" + keywords,
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);

                if (action === 'search' && keywords !== "") {
                    $('#inventory-search-cancel-button').show();
                } else {
                    $('#inventory-search-cancel-button').hide();
                    $('#inventory-search-input').val('');
                }
                unblockUI();
            } else if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_TABLE_WITH_SEARCH_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function sortByColumn(element) {
    global_update_table_with_sort_element = element;
    var error_div = $('#add-new-item-error-div');
    var column_name = element.html().substring(0, 3);
    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/inventory/update-table-with-sort.php",
        type: "GET",
        data: "column_name=" + column_name,
        cache: false,
        dataType: "json",
        success: function(json_data) {
            if (json_data.status === 'success') {
                $('#inventory-table tbody').html(json_data.html_tbody);
                $("#pagination-holder-td").html(json_data.html_pagination);

                $('#inventory-table thead tr:nth-child(5) td a').html('&#9650;');
                $('#inventory-table thead tr:nth-child(5) td a').css('opacity', '0.5');

                var up_or_down = json_data.up_or_down;

                if (up_or_down === 'up') {
                    element.find('a').html('&#9660;');
                } else {
                    element.find('a').html('&#9650;');
                }
                element.find('a').css('opacity', '1');
                unblockUI();
            } else if (json_data.status === "no_session") {
                showLoginPopupWindow();
                global_session_selected_room = json_data.selected_room;
                global_session_selected_cabinet = json_data.selected_cabinet;
                expiredSessionActionItemFlag = UPDATE_TABLE_WITH_SORT_ACTION_FLAG;
            } else {
                error_div.html(SERVER_FAIL_RESPONSE);
                unblockUI();
            }
            hideProgressCircle();
        }
    });
}

function popupLoginUser() {
    var email = $("#popup-login-email").val();
    var password = $("#popup-login-password").val();
    var error_div = $('#popup-login-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    if (email.length < 1 || password.length < 1) {
        error_div.html("Please fill all the fields properly");
    } else if (!isValidEmailAddress(email)) {
        error_div.html('Please enter a valid e-mail address.');
    } else {
        $('#login-popup-window').css('z-index', '9');
        showProgressCircle();
        $.ajax({
            url: "ajax/login-action.php",
            type: "POST",
            data: "email=" + email +
                    "&password=" + password +
                    "&selected_room=" + global_session_selected_room +
                    "&selected_cabinet=" + global_session_selected_cabinet,
            cache: false,
            dataType: "html",
            success: function(html_response) {
                if (html_response.trim() === "success") {
                    hideLoginPopupWindow();
                } else if (html_response.trim() === "invalid_info"
                        || html_response.trim() === "wrong_combination") {
                    error_div.html("Information you entered does not match with our records.");
                } else if (html_response.trim() === "no_activation") {
                    window.location = "/activation";
                } else {
                    error_div.html("Something went wrong with the servers. Please try again later.");
                }
                $('#login-popup-window').css('z-index', '99999');
                hideProgressCircle();
            }
        });
    }
}

function popupLoginWindowInputFunctions() {
    var wasTrailingTextAdded = false;
    $("#popup-login-email").click(function() {
        var currentValue = $(this).val();
        if (currentValue.indexOf('@') !== -1 && currentValue.indexOf('@example.com') < 0) {
            currentValue = currentValue.substring(0, currentValue.indexOf('@'));
        }

        if (currentValue.indexOf('@example.com') < 0 && currentValue !== "TPI E-mail Address") {
            $(this).val(currentValue + "@example.com");
            wasTrailingTextAdded = true;
        }
        if (!wasTrailingTextAdded) {
            $(this)[0].setSelectionRange(0, 0);
        }
        if (currentValue === "@example.com" || currentValue === "" || currentValue === "TPI E-mail Address") {
            $(this).val('@example.com');
            $(this)[0].setSelectionRange(0, 0);
        }
    });

    $("#popup-login-email").blur(function() {
        var error_div = $('#popup-login-error-div');
        error_div.html('&nbsp;');
        var currentValue = $(this).val();
        var trimmedValue = currentValue.substring(0, currentValue.indexOf('@'));
        if (currentValue.indexOf('@') < 0 && currentValue !== "TPI E-mail Address" && currentValue !== "") {
            $(this).val(currentValue + "@example.com");
            $(this).css('color', '#000000');
        } else if (currentValue.indexOf('@') !== -1 && currentValue.indexOf('@example.com') < 0) {
            currentValue = currentValue.substring(0, currentValue.indexOf('@'));
            $(this).val(currentValue + "@example.com");
            $(this).css('color', '#000000');
        } else if (currentValue.indexOf('@example.com') !== 0 && trimmedValue !== "") {
            $(this).css('color', '#000000');
        }

        if ($(this).val() === "@example.com") {
            $(this).addClass('placeholder');
            $(this).val($(this).attr('placeholder'));
            $(this).css('color', '#aaaaaa');
        }

        if (!isValidEmailAddress($(this).val())) {
            error_div.css('color', '#cc0000');
            error_div.html('Please enter a valid e-mail address.');
        }
    });

    $("#popup-login-password").keyup(function(event) {
        if (event.keyCode === 13) {
            popupLoginUser();
        }
    });
}

function showLoginPopupWindow() {
    $('#login-popup-window').fadeIn();
    blockUI();
}

function hideLoginPopupWindow() {
    $('#login-popup-window').fadeOut();
    $("#popup-login-email").val('');
    $("#popup-login-password").val('');
    $('#undo-button').hide();
    //alert (expiredSessionActionItemFlag);

//    global_update_table_with_cabinet_name_element.parent('div').find('span').removeClass('selected');
//    global_update_table_with_cabinet_name_element.parent('div').find('span:first-child').addClass('selected');
//    global_update_table_with_room_no_element.parent('div').find('span').removeClass('selected');
//    global_update_table_with_room_no_element.parent('div').find('span:first-child').addClass('selected');
    switch (expiredSessionActionItemFlag) {
        case ADD_NEW_ITEM_ACTION_FLAG:
            saveNewItem();
            break;
        case COPY_ITEM_ACTION_FLAG:
            copyItem();
            break;
        case DELETE_ITEM_ACTION_FLAG:
            deleteItem();
            break;
        case UPDATE_ITEM_INFO_ACTION_FLAG:
            updateInfo();
            break;
        case UPDATE_TABLE_WITH_ROOM_NO_ACTION_FLAG:
            updateTableWithRoomNo(global_update_table_with_room_no_room_number,
                    global_update_table_with_room_no_element);
            break;
        case UPDATE_TABLE_WITH_CABINET_NAME_ACTION_FLAG:
            updateTableWithCabinetName(global_update_table_with_cabinet_name,
                    global_update_table_with_cabinet_name_element);
            break;
        case UPDATE_TABLE_WITH_PAGE_NO_ACTION_FLAG:
            go_to_page(global_go_to_page);
            break;
        case UPDATE_TABLE_WITH_SORT_ACTION_FLAG:
            sortByColumn(global_update_table_with_sort_element);
            break;
        case UPDATE_TABLE_WITH_SEARCH_ACTION_FLAG:
            searchAction(global_search_action_type);
            break;
        default:
            unblockUI();
    }
}

function cancelDelete() {
    wasDeleteButtonClicked = true;
    $('#delete-confirmation-popup-window').fadeOut(200);
    unblockUI();
}

function contextMenuFunctions() {
    $('.selector-cell').bind("contextmenu", function(event) {
        event.preventDefault();
        $('.selector-cell').parent('tr').removeClass('selected');
        $(this).parent('tr').addClass('selected');

        // Show contextmenu
        $(".custom-menu").finish().show(100).css({
            top: event.pageY - 20 + "px",
            left: event.pageX - 20 + "px"
        });
    });

    // If the document is clicked somewhere
    $(document).on("mousedown", function(e) {
        // If the clicked element is not the menu
        if (!$(e.target).parents(".custom-menu").length > 0) {

            hideContextMenu();
        }
    });


    // If the menu element is clicked
    $(document).on('mousedown', '.custom-menu li', function() {
        wasDeleteButtonClicked = true;
    });
    $(".custom-menu li").click(function() {
        $(".custom-menu").hide(0);
        switch ($(this).attr("data-action")) {
            case "delete":
                wasDeleteButtonClicked = true;
                if (selectedRowIds.length === 1) {
                    $('#delete-confirmation-popup-window-text-div').html('Are you sure to delete the selected item?');
                } else {
                    $('#delete-confirmation-popup-window-text-div').html('Are you sure to delete the selected items?');
                }
                $('#delete-confirmation-popup-window').fadeIn();
                blockUI();
                break;
            case "copy":
                copyItem();
                break;
            case "etc":
                alert("etc");
                break;
        }
    });
}

function hideContextMenu() {
    $(".custom-menu").hide(100);
}

function datepickerFunctions() {
    $(function() {
        $(document).on('focus', '.datepicker', function() {
            $(this).datepicker({
                dateFormat: 'd-M-y',
                showButtonPanel: true,
                beforeShow: function(input) {
                    setTimeout(function() {
                        var buttonPane = $(input)
                                .datepicker("widget")
                                .find(".ui-datepicker-buttonpane");

                        var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">N/A</button>');
                        btn.unbind("click")
                                .bind("click", function() {
                                    $(input).datepicker("hide");
                                    $(input).val("N/A");
                                    $(input).change();
                                });

                        btn.appendTo(buttonPane);
                    }, 1);
                },
                onSelect: function(dateText) {
                    var input = $(this);
                    input.css('color', '#1C4D6F');
                    // Uncheck and hide the N/A checkbox (if there is one for that datepicker input)
                    var na_checkbox = $(this).parent('td').find('span').find(':checkbox');
                    if (na_checkbox.length) {
                        na_checkbox.prop('checked', false);
                        na_checkbox.parent('span').fadeOut(200);
                    }
                    input.change();
                }
            });

        });
    });
}

function assignTabindex() {
    $(":input").each(function(i) {
        if ($(this).attr('type') !== 'checkbox') {
            $(this).attr('tabindex', i + 1);
        }
    });
}

function selectRowFunctions() {
    var wasSelected = false;
    var isMouseDown = false;
    var mouseUpInsideTheFirstRow = false;

    // Needed for 'unselect' the selected table rows
    $(document).mouseup(function(e) {
        if (!wasSelected || !mouseUpInsideTheFirstRow) {
            if (!wasDeleteButtonClicked) {
                $(".selector-cell").parent('tr').removeClass('selected');
                selectedRowIds.length = 0;
                $("#inventory-table").removeClass('noselect');
                $("#inventory-table input").prop('disabled', false);
                $("#inventory-table select").prop('disabled', false);
                $(".selectable-cell").hover(function() {
                    $(this).css("background-color", "rgba(245, 245, 245, 0.5)");
                }, function() {
                    $(this).css("background-color", "transparent");
                });
                hideContextMenu();
            }
        }
        wasDeleteButtonClicked = false;
        mouseUpInsideTheFirstRow = false;
        wasSelected = false;
        isMouseDown = false;

    });

    $(function() {
        var isDragFinished = false;
        var dragStartId = 0;
        var dragEndId = 0;
        var selectorCell = $(".selector-cell");
        var isMetaKeyDown = false;
        var wasDragged = false;

        $(document).on('mousedown', '.selector-cell', function(e) {
            if (e.which !== 1)
                return false;
            hideContextMenu();
            wasSelected = true;
            isMouseDown = true;
            wasDragged = false;
            if (e.metaKey || e.ctrlKey) {
                isMetaKeyDown = true;
            } else {
                selectedRowIds.length = 0;
                $(".selector-cell").parent('tr').removeClass('selected');
            }
            dragStartId = $(".selector-cell").index($(this));
            return false;
        });

        $(document).on('mouseover', '.selector-cell', function(e) {
            if (e.which !== 1)
                return false;
            if (isMouseDown) {
                isDragFinished = false;
                wasDragged = true;
                dragEndId = $(".selector-cell").index($(this));
                selectRange();
            }
        });

        $(document).on('mouseup', '.selector-cell', function(e) {
            if (e.which !== 1) {
                return false;
            }
            mouseUpInsideTheFirstRow = true;
            isDragFinished = true;
            isMouseDown = false;
            dragEndId = $(".selector-cell").index($(this));
            selectRange();
        });

        function selectRange() {
            if (dragEndId > -1) {
                if (!isMetaKeyDown) {
                    $(".selector-cell").parent('tr').removeClass('selected');
                }
                if (dragStartId < dragEndId) {
                    for (var i = dragStartId; i < dragEndId + 1; i += 1) {
                        adjustIdArray(i);
                    }
                } else if (dragStartId > dragEndId) {
                    for (var i = dragStartId; i > dragEndId - 1; i -= 1) {
                        adjustIdArray(i);
                    }
                } else if (dragStartId === dragEndId) {
                    var row = $(".selector-cell:eq(" + dragStartId + ")").parent('tr');
                    var rowId = row.attr('id');
                    if (selectedRowIds.indexOf(rowId) > -1) {
                        selectedRowIds.splice(selectedRowIds.indexOf(rowId), 1);
                        row.removeClass('selected');
                    } else {
                        selectedRowIds.push(rowId);
                        row.addClass('selected');
                    }

                    // Show contextmenu
                    $(".custom-menu").finish().fadeIn(100).css({
                        top: event.pageY - 20 + "px",
                        left: event.pageX - 20 + "px"
                    });

                }

            }

            if (selectedRowIds.length !== 0) {
                $("#inventory-table").addClass('noselect');
                $("#inventory-table input").prop('disabled', true);
                $("#inventory-table select").prop('disabled', true);
                $(".selectable-cell").hover(function() {
                    $(this).css("background-color", "transparent");
                }, function() {
                    $(this).css("background-color", "transparent");
                });
            } else {
                $("#inventory-table").removeClass('noselect');
                $("#inventory-table input").prop('disabled', false);
                $("#inventory-table select").prop('disabled', false);
                $(".selectable-cell").hover(function() {
                    $(this).css("background-color", "rgba(245, 245, 245, 0.5)");
                }, function() {
                    $(this).css("background-color", "transparent");
                });
            }

            function adjustIdArray(i) {
                var row = $(".selector-cell:eq(" + i + ")").parent('tr');
                row.addClass('selected');
                if (isDragFinished) {
                    if (selectedRowIds.indexOf(row.attr('id')) === -1) {
                        selectedRowIds.push(row.attr('id'));
                    }
                    // Show delete contextmenu
                    $(".custom-menu").finish().fadeIn(100).css({
                        top: event.pageY - 20 + "px",
                        left: event.pageX - 20 + "px"
                    });
                }
            }
        }
    });
}

function editFieldFunctions() {
    var selected_input;
    $(document).on('focus', '.data-table-editable-text-input', function() {
        selected_input = $(this);
        var edit_input = $("#edit-input");
        $('#invisible-input-id-holder').html(selected_input.attr('id'));
        if (!(selected_input.hasClass("datepicker") || selected_input.is("select") || selected_input.is("[readonly]"))) {
            var value = $(this).val();
            edit_input.val(value);
            edit_input[0].selectionStart = edit_input[0].selectionEnd = edit_input.val().length;
        } else {
            edit_input.val("");
        }
        edit_input.attr('tabindex', selected_input.attr('tabindex'));
    });
    $(document).on('click', '.data-table-editable-text-input', function() {
        selected_input = $(this);
        var edit_input = $("#edit-input");
        $('#invisible-input-id-holder').html(selected_input.attr('id'));
        if (!(selected_input.hasClass("datepicker") || selected_input.is("select") || selected_input.is("[readonly]"))) {
            var value = $(this).val();
            edit_input.val(value);
        } else {
            edit_input.val("");
        }
        edit_input.attr('tabindex', selected_input.attr('tabindex'));
    });
    $(document).on('keyup', '.data-table-editable-text-input', function() {
        selected_input = $(this);
        if (!(selected_input.hasClass("datepicker") || selected_input.is("select") || selected_input.is("[readonly]"))) {
            var value = $(this).val();
            $("#edit-input").val(value);
        } else {
            edit_input.val("");
        }
    });
    $(document).on('keyup', '#edit-input', function() {
        if (!(selected_input.hasClass("datepicker") || selected_input.is("select") || selected_input.is("[readonly]"))) {
            var value = $(this).val();
            selected_input.val(value);
        }
    });
    $(document).on('focus', '#add-new-item-row input', function() {
        selected_input = null;
        $("#edit-input").val('');
    });
    $("input[type='search']").focus(function() {
        selected_input = null;
        $("#edit-input").val('');
    });
}

// dataTable initialization
function initDataTable() {
    $('#inventory-table').dataTable({
        stateSave: true,
        "pageLength": 50,
        "autoWidth": true,
        "oLanguage": {
            "sSearch": ""
        },
        aLengthMenu: [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ]
//"scrollX": true
    });
    $('#inventory-table_filter label input').attr('Placeholder', 'Search');
//    var htmlContent = $('#inventory-table_filter label').html();
//    htmlContent += "<span>Search</span>";
//    $('#inventory-table_filter label').html(htmlContent);
//    $('#inventory-table_filter label input').focus(function() {
//        $('#inventory-table_filter label span').hide();
//    }).blur(function() {
//        if ($(this).val() === '') {
//            $('#inventory-table_filter label span').show();
//        }
//    }).blur();

    hideProgressCircle();
    unblockUI();
}

function resizableTable() {
    var pressed = false;
    var start = undefined;
    var startX, startWidth;
    $("table .noselect td").mousedown(function(e) {
        start = $(this);
        pressed = true;
        startX = e.pageX;
        startWidth = $(this).width();
        $(start).addClass("resizing");
    });
    $(document).mousemove(function(e) {
        if (pressed) {
            $(start).width(startWidth + (e.pageX - startX));
        }
    });
    $(document).mouseup(function() {
        if (pressed) {
            $(start).removeClass("resizing");
            pressed = false;
        }
    });
}
