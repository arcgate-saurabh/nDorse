//Forgot password
$(document).on("click", "#forgotPassword", function (e) {
    e.preventDefault();
    var module = $(this).attr("module");
    var url = siteurl + "client/forgotPassword";
    $.ajax({
        type: "GET",
        url: url,
        success: function (response) {
            $("#commonModal .modal-title").html("Forgot Password");
            $("#commonModal .modal-body").html(response);
            $("#commonModal").modal("show");
            bindForgotPassword(module);
            bindResetPassword(module);
        },
        error: function (response) {
            alertbootbox(response);
        }
    });
});
function bindForgotPassword(module) {
    $("#forgotPasswordForm").ajaxForm({
        url: siteurl + module + "/forgotPassword",
        dataType: 'json',
        beforeSubmit: function () {
            return $("#forgotPasswordForm").valid(); // TRUE when form is valid, FALSE will cancel submit
        },
        success: function (response) {
            alertbootbox(response.msg);
            if (response.success == true) {
//                $("#commonModal").modal("hide");
            }
        }
    });
    $("#forgotPasswordForm").validate({
        rules: {
            'email': {
                required: true,
                email: true
            }
        },
        messages: {
            'email': {
                required: "Email is required",
                email: "Invalid email"
            }
        }
    });
}

function bindResetPassword(module) {
    $("#resetPasswordForm").ajaxForm({
        url: siteurl + module + "/setPassword",
        dataType: 'json',
        beforeSubmit: function () {
            return $("#resetPasswordForm").valid(); // TRUE when form is valid, FALSE will cancel submit
        },
        success: function (response) {
            alertbootbox(response.msg);
            if (response.success == true) {
                $("#commonModal").modal("hide");
            }
        }
    });
    $("#resetPasswordForm").validate({
        rules: {
            'verification_code': {
                required: true,
            },
            'password': {
                required: true,
                minlength: 8
            },
            'confirm_password': {
                equalTo: '#re_password',
            }
        },
        messages: {
            'verification_code': {
                required: "Enter secret code",
            },
            'password': {
                required: "Password is required",
                minlength: "Atleast 8 characters are required",
            },
            'confirm_password': {
                equalTo: "Confirm Password do not match",
            }
        }
    });
}

$(document).on("click", ".activeDirectorySubmit", function (e) {
    $('.error').hide();
    var username = $('#ldap-username').val();
    var password = $('#ldap-pass').val();
    var error = false;
    if ($.trim(username).length < 1) {
        $('#ldap-username-error').html('Please enter username.').show();
        error = true;
    }
    if ($.trim(password).length < 1) {
        $('#ldap-password-error').html('Please enter password.').show();
        error = true;
    }
    if (!error) {
        $("#LdapLoginForm").submit();
    }
});
$(document).on("keyup", "#adfs-org-short-code", function (e) {
    $('#adfs-org-short-code-error').html('');
    $(".adfs_login_button").attr('value', 'Login').addClass('hidden');
    var orgCode = $(this).val();
    //console.log("Org Short code : " + orgCode);
    $('.error').hide();
    var error = false;
    if ($.trim(orgCode).length < 1) {
        $('#adfs-org-short-code-error').html('Please enter organization short code..').show();
        error = true;
    } else { //Check for organization link and show the link
        var url = siteurl + "client/getOrgShortCode";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: {'short_code': orgCode},
            success: function (response) {
                console.log(response);
                if (response.success == true) {
                    console.log(response.adfs_link);
                    $("#adfs_login_link").attr('href', response.adfs_link);
                    $(".adfs_login_button").attr('value', 'Login to ' + orgCode.toUpperCase()).removeClass('hidden');
                } else {
                    //$('#adfs-org-short-code-error').html(response.msg).show();
                }
                return false;
            },
            error: function (response) {

            }
        });
        return false;
    }
    if ($.trim(password).length < 1) {
        $('#ldap-password-error').html('Please enter password.').show();
        error = true;
    }
    if (!error) {
        $("#LdapLoginForm").submit();
    }
});
