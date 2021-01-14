//document ready start
$(document).ready(function () {
    $("#VerificationRegisterForm").validate({
         rules: {
             'data[Verification][verification_code]': "required"
         },
         messages: {
             'data[Verification][verification_code]': "Please enter verification code"
         }
     });
     
    $( "#VerificationRegisterForm" ).submit(function( event ) {
        
        if ($('input#VerificationAcceptTnc').prop('checked') != true) {
            alertbootbox("Accept End User License Agreement");
            return false;
        }
    });
    
    $("#UserRegisterForm").validate({
         rules: {
             'data[User][email]': {
                required: true,
                email: true
            },
            'data[User][password]': {
                required: true,
                minlength: 8
            },
             'data[User][confirm_password]': {
                equalTo: '#UserPassword',
            },
         },
         messages: {
             'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
             'data[User][password]': {
                required: "Password is required",
                minlength: "Atleast 8 characters are required",
            },
            'data[User][confirm_password]': {
                equalTo: "Confirm Password do not match",
            },
         }
     });
     
      
 
 
}); //document ready end
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

$(document).on("click", "#sendTnc", function (e) {
     $.ajax({
                type: "POST",
                url: siteurl + 'cajax/sendTnC',
                dataType: 'json',
                success: function (response) {
                    if(response.result.status) {
                        alertbootbox(response.result.msg);
                    } else {
                        alertbootboxcb(response.result.msg, function() {
                            window.location.href = siteurl + "client/register";
                        });
                    }
                },
                error: function (response) {
                    alertbootbox(response);
                }
     });
});

//$(document).on("click", "#showFaqs", function (e) {
//    e.preventDefault();
//    var href = $(this).attr("rel");
//    $("#iframeModal .modal-body iframe").attr("src", href);
//    $('#iframeModal').modal({backdrop: 'static'});
//});

//$(document).on("click", "#forgotPassword", function (e) {
//    e.preventDefault();
//    var url = $(this).attr("href");
//   $.ajax({
//                type: "GET",
//                url: url,
//                success: function (response) {
//                    $("#commonModal .modal-title").html("Forgot Password");
//                    $("#commonModal .modal-body").html(response);
//                    $("#commonModal").modal("show");
//                    bindForgotPassword();
//                    bindResetPassword();
//                },
//                error: function (response) {
//                    alertbootbox(response);
//                }
//     });
//});
//
//function bindForgotPassword() {
//    $("#forgotPasswordForm").ajaxForm({
//         url: siteurl + "client/forgotPassword",
//        dataType: 'json',
//        beforeSubmit: function () {
//            return $("#forgotPasswordForm").valid();// TRUE when form is valid, FALSE will cancel submit
//        },
//        success: function (response) {
//            alertbootbox(response.msg);
//            
//            if(response.success == true) {
////                $("#commonModal").modal("hide");
//            }
//        }
//     });
//     
//     $("#forgotPasswordForm").validate({
//         rules: {
//             'email': {
//                required: true,
//                email: true
//            }
//         },
//         messages: {
//             'email': {
//                required: "Email is required",
//                email: "Invalid email"
//            }
//         }
//     });
//}
//
//function bindResetPassword() {
//    $("#resetPasswordForm").ajaxForm({
//         url: siteurl + "client/setPassword",
//        dataType: 'json',
//        beforeSubmit: function () {
//            return $("#resetPasswordForm").valid();// TRUE when form is valid, FALSE will cancel submit
//        },
//        success: function (response) {
//            alertbootbox(response.msg);
//            
//            if(response.success == true) {
//                $("#commonModal").modal("hide");
//            }
//        }
//     });
//     
//     $("#resetPasswordForm").validate({
//         rules: {
//             'verification_code': {
//                required: true,
//            },
//            'password': {
//                required: true,
//                minlength: 8
//            },
//             'confirm_password': {
//                equalTo: '#re_password',
//            }
//         },
//         messages: {
//             'verification_code': {
//                required: "Enter secret code",
//            },
//            'password': {
//                required: "Password is required",
//                minlength: "Atleast 8 characters are required",
//            },
//            'confirm_password': {
//                equalTo: "Confirm Password do not match",
//            }
//         }
//     });
//}

$(document).on("click", "#recoverUsername", function (e) {
    e.preventDefault();
    var url = $(this).attr("href");
   $.ajax({
                type: "GET",
                url: url,
                success: function (response) {
                    $("#commonModal .modal-title").html("Recovery Username");
                    $("#commonModal .modal-body").html(response);
                    $("#commonModal").modal("show");
                    bindRecoverUsername();
                },
                error: function (response) {
                    alertbootbox(response);
                }
     });
});

function bindRecoverUsername() {
    $("#recoverUsernameForm").ajaxForm({
         url: siteurl + "client/recoverUsername",
        dataType: 'json',
        beforeSubmit: function () {
            return $("#recoverUsernameForm").valid();// TRUE when form is valid, FALSE will cancel submit
        },
        success: function (response) {
            alertbootbox(response.msg);
            
            if(response.success == true) {
                $("#commonModal").modal("hide");
            }
        }
     });
     
     $("#recoverUsernameForm").validate({
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

