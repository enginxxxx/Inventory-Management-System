$(document).ready(function() {
    var wasTrailingTextAdded = false;
    $("#email").click(function() {
        var currentValue = $(this).val();
        if (currentValue.indexOf('@') !== -1 && currentValue.indexOf('@example.com') < 0) {
            currentValue = currentValue.substring(0, currentValue.indexOf('@'));
        }

        if (currentValue.indexOf('@example.com') < 0) {
            $(this).val(currentValue + "@example.com");
            wasTrailingTextAdded = true;
        }
        if (!wasTrailingTextAdded) {
            $(this)[0].setSelectionRange(0, 0);
        }
        if (currentValue === "@example.com" || currentValue === "E-mail Address" || currentValue === "") {
            $(this)[0].setSelectionRange(0, 0);
        }
    });

    $("#email").blur(function() {
        var error_div = $('#forgot-password-error-div');
        error_div.html('&nbsp;');
        var currentValue = $(this).val();
        if (currentValue.indexOf('@') !== -1 && currentValue.indexOf('@example.com') < 0) {
            currentValue = currentValue.substring(0, currentValue.indexOf('@'));
            $(this).val(currentValue + "@example.com");
        } else if (currentValue.indexOf('@') === -1 && currentValue !== "E-mail Address" && currentValue !== "") {
            $(this).val(currentValue + "@example.com");
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

    
    $("#email").keyup(function(event) {
        if (event.keyCode === 13) {
            forgotPassword();
        }
    });

});

function forgotPassword() {
    var email = $("#email").val();
    var error_div = $('#forgot-password-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    if (email.length < 1) {
        error_div.html("Please enter your e-mail address.");
    } else if (!isValidEmailAddress(email)) {
        error_div.html('Please enter a valid e-mail address.');
    } else {
        showProgressCircle();
        blockUI();
        $.ajax({
            url: "ajax/forgot-password-action.php",
            type: "POST",
            data: "email=" + email,
            cache: false,
            dataType: "html",
            success: function(html_response) {
                if (html_response.trim() === "success") {
                    error_div.html('A password reset link has been sent to you e-mail address. Please follow the instructions in the link to reset your password.');
                } else {
                    error_div.html(html_response);
                }
                hideProgressCircle();
                unblockUI();
            }
        });
    }
}