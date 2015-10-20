$(document).ready(function() {
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
            resetPassword();
        }
    });

});

function resetPassword() {
    var code = $("#code").val();
    var email = $("#email").val();
    var password = $("#password").val();
    var password_repeat = $("#password-repeat").val();
    var error_div = $('#password-reset-error-div');
    error_div.html('&nbsp;');
    error_div.css('color', '#cc0000');

    if (password.length < 1
            || password_repeat.length < 1) {
        error_div.html("Please fill both password fields properly");
    } else if (password !== password_repeat) {
        error_div.html("Passwords do not match");
    } else {
        showProgressCircle();
        blockUI();
        $.ajax({
            url: "ajax/password-reset-action.php",
            type: "POST",
            data: "email=" + email +
                    "&code=" + code +
                    "&password=" + password,
            cache: false,
            dataType: "html",
            success: function(html_response) {
                if (html_response.trim() === "success") {
                    window.location = "/";
                } else if (html_response.trim() === "wrong_password_reset_code"){
                    error_div.html("The password reset code coudln't be found for this e-mail. Please use the link in the e-mail as it is without modifying it. If it still doesn't work, please contact webmaster.");
                } else {
                    alert(html_response);
                }
                hideProgressCircle();
                unblockUI();
            }
        });
    }
}