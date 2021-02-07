var SERVER_FAIL_RESPONSE = 'Something went wrong with the server, please try again.';

$(document).ready(function() {
    $('[placeholder]').focus(function() {
        var input = $(this);
        if (input.val() === input.attr('placeholder')) {
            input.val('');
            input.removeClass('placeholder');
        }
    }).blur(function() {
        var input = $(this);
        if (input.val() === '' || input.val() === input.attr('placeholder')) {
            input.addClass('placeholder');
            input.val(input.attr('placeholder'));
            input.css('color', '#aaaaaa');
        } else {
            input.css('color', '#1C4D6F');
        }
    }).blur();

    $('input').on('input', function() {
        $(this).css('color', '#1C4D6F');
    });

    $(document).unbind('keydown').bind('keydown', function(event) {
        var doPrevent = false;
        if (event.keyCode === 8) {
            var d = event.srcElement || event.target;
            if ((d.tagName.toUpperCase() === 'INPUT' &&
                    (
                            d.type.toUpperCase() === 'TEXT' ||
                            d.type.toUpperCase() === 'PASSWORD' ||
                            d.type.toUpperCase() === 'FILE' ||
                            d.type.toUpperCase() === 'EMAIL' ||
                            d.type.toUpperCase() === 'SEARCH' ||
                            d.type.toUpperCase() === 'DATE')
                    ) ||
                    d.tagName.toUpperCase() === 'TEXTAREA') {
                doPrevent = d.readOnly || d.disabled;
            }
            else {
                doPrevent = true;
            }
        }

        if (doPrevent) {
            event.preventDefault();
        }
    });
});

function logoutAction() {
    var error_div = $('#add-new-item-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    showProgressCircle();
    blockUI();
    $.ajax({
        url: "ajax/logout-action.php",
        type: "POST",
        cache: false,
        dataType: "html",
        success: function(html_response) {
            if (html_response.trim() === 'success') {
                window.location = "/index.php";
            } else {
                error_div.html('Something went wrong. Please try again.');
            }
            hideProgressCircle();
            unblockUI();
        }
    });
}

function blockUI() {
    var block_ui_div = $(".gray-out-div");
    block_ui_div[0].style.visibility = "visible";
}

function unblockUI() {
    var block_ui_div = $(".gray-out-div");
    block_ui_div[0].style.visibility = "hidden";
}

function showProgressCircle() {
    var progress_circle = $(".progress-circle");
    progress_circle.show();
    progress_circle[0].style.opacity = 1;
    progress_circle.css('z-index', '9999');
}

function hideProgressCircle() {
    var progress_circle = $(".progress-circle");
    progress_circle[0].style.opacity = 0;
    setTimeout(progress_circle.hide, 500);
    progress_circle.css('z-index', '9');
}

// Replace characters in strings
function replaceAll(find, replace, str) {
    return str.replace(new RegExp(find, 'g'), replace);
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
