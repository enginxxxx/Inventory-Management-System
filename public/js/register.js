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
        if (currentValue === "@example.com" || currentValue === "TPI E-mail Address" || currentValue === "") {
            $(this)[0].setSelectionRange(0, 0);
        }
    });

    $("#email").blur(function() {
        var error_div = $('#register-error-div');
        error_div.html('&nbsp;');
        var currentValue = $(this).val();
        if (currentValue.indexOf('@') !== -1 && currentValue.indexOf('@example.com') < 0) {
            currentValue = currentValue.substring(0, currentValue.indexOf('@'));
            $(this).val(currentValue + "@example.com");
        } else if (currentValue.indexOf('@') === -1 && currentValue !== "TPI E-mail Address" && currentValue !== "") {
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

    $("#password").prop('type', 'text');
    $("#password").focus(function() {
        $(this).prop('type', 'password');
    }).blur(function() {
        if ($(this).val() === "Password" || $(this).val() === "") {
            $(this).prop('type', 'text');
        }
    }).blur();

    $("#password-repeat").prop('type', 'text');
    $("#password-repeat").focus(function() {
        $(this).prop('type', 'password');
    }).blur(function() {
        if ($(this).val() === "Password Repeat" || $(this).val() === "") {
            $(this).prop('type', 'text');
        }
    }).blur();
    
    $("#password-repeat").keyup(function(event) {
        if (event.keyCode === 13) {
            registerUser();
        }
    });
    
    $("#email").keyup(function(event) {
        if (event.keyCode === 13) {
            registerUser();
        }
    });

});

function registerUser() {
    var email = $("#email").val();
    var password = $("#password").val();
    var password_repeat = $("#password-repeat").val();
    var error_div = $('#register-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    if (email.length < 1
            || password.length < 1
            || password_repeat.length < 1) {
        error_div.html("Please fill all the fields properly");
    } else if (password !== password_repeat) {
        error_div.html("Passwords do not match");
    } else if (!isValidEmailAddress(email)) {
        error_div.html('Please enter a valid e-mail address.');
    } else {
        showProgressCircle();
        blockUI();
        $.ajax({
            url: "ajax/register-action.php",
            type: "POST",
            data: "email=" + email +
                    "&password=" + password,
            cache: false,
            dataType: "html",
            success: function(html_response) {
                if (html_response.trim() === "success") {
                    window.location = "/activation.php";
                } else {
                    error_div.html(html_response);
                }
                hideProgressCircle();
                unblockUI();
            }
        });
    }
}