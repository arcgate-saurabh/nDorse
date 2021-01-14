/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
    $("#contactussubmit").click(function () {
        $("#zdeskContactForm").submit();
    });



    $("#zdeskContactForm").validate({
        rules: {
            'uname': {
                required: true,
            },
            'uemail': {
                required: true,
            },
            'usubject': {
                required: true,
            },
            'umessage': {
                required: true,
            }
        },
        message: {
            'uname': {
                required: "Name cannot be empty.",
            },
            'uemail': {
                required: "Email cannot be empty.",
            },
            'usubject': {
                required: "Subject cannot be empty.",
            },
            'umessage': {
                required: "Message cannot be empty.",
            }
        },
        submitHandler: function (form) {
            var name = $.trim($("input[name='uname']").val());
            var email = $.trim($("input[name='uemail']").val());
            var subject = $.trim($("input[name='usubject']").val());
            var message = $.trim($("textarea[name='umessage']").val());
            $("#contactussubmit").attr("disabled", "disabled");
            $("#contactussubmit").html('Sending <i class="fa fa-refresh fa-spin"></i>')
            $.ajax({
                type: "POST",
                url: siteurl + 'cajax/StaticFormContactus',
                data: {name: name, email: email, subject: subject, message: message},
                success: function (data, xhr) {
                    $("#contactussubmit").prop("disabled", false);
                    $("#contactussubmit").html('Send');
                    if (data == "Success") {
                        $('#zdeskContactForm')[0].reset();
                        msg = "Your request is successfully submitted. We will contact you soon.";
                    } else {
                        msg = "Something went wrong."
                    }
                    bootbox.alert({
                        closeButton: false,
                        "message": msg,
                        "className": "bootboxalertclass",
                        "callback": function () {
                            console.log("successfull");
                        }
                    });
//                var jsonparse = $.parseJSON(data);
//                var url = siteurl + 'xlsxfolder/' + jsonparse.filename;
//                window.open(url, '_blank');

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
            return false;
        }
    });
})


