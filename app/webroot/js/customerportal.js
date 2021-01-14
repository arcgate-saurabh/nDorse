
$(document).ready(function () {

    //========set image
    $('#org_upload_photo ').bind("click", function () {
        $('#photo').click();
    });


    $("#photo").change(function () {
        $("#validImageError").text("");
        var imageformat = $(this).val().split(".")[1];
        if (trimAndLowerCaseString(imageformat) == "png" || trimAndLowerCaseString(imageformat) == "jpg" || trimAndLowerCaseString(imageformat) == "jpeg" || trimAndLowerCaseString(imageformat) == "gif") {
            readURL(this, 'org_image');
        } else {
            $(this).val("");
            $("#validImageError").text("Select valid images. Only jpg/jpeg/png/gif images are allowed.");
            //alertbootbox("File type of uploaded image is not allowed, Please upload jpg/png/gif image format.")
        }
        //
    });

//    $("#OrgphotoSetorgimageForm").change(function () {
//        $("#OrgphotoSetorgimageForm").submit();
//    });
    $("#OrgphotoSetorgcpImageForm").ajaxForm({
        url: orgcpuploadimage,
        success: function (data) {
            var parseData = JSON.parse(data);
            if (parseData.status) {

                //$("#img_msg").html("image has been updated successfully");
                $('#org_image').attr('src', orgimgurl + "small/" + parseData.imageloc);
                $('#org_image_name').val(parseData.imageloc);
            } else {
                $("#img_msg").html(parseData.error);
                return false;
            }
        },
        error: function (msg) {
            return false;
        }
    });

    //======removing image

    $("#org_remove_photo").click(function () {
        $("#org_image").attr("src", siteurl + "img/comp_pic.png");
        $("#photo").val("");
    })



//    $("#org_remove_photo").click(function () {
//        var current_image = $('#org_image_name').val();
//        if (current_image == "") {
//            $("#img_msg").html("No image selected");
//        } else {
//            $.ajax({
//                url: siteurl + 'users/deleteorgimage',
//                type: "POST",
//                data: {image_name: current_image},
//                success: function (data, textStatus, jqXHR) {
//                    var parseData = JSON.parse(data);
//                    if (parseData.status) {
//                        //$("#img_msg").html("image has been removed successfully");
//                        $('#org_image').attr('src', siteurl + parseData.imageloc);
//                        $('#org_image_name').val("");
//                        $('#photo').val("");
//                    } else {
//                        $("#img_msg").html(parseData.error);
//                        // buttonclick.removeAttr('disabled');
//                        return false;
//                    }
//                }, error: function (jqXHR, textStatus, errorThrown) {
//
//                }
//            });
//
//        }
//    });

    //==saving org
    $("#orgformsubmit").click(function () {
        $("#OrgCreateorgForm").submit();
    });

    //Create org validations starts here
    $("#OrgCreateorgForm").validate({
        errorPlacement: function (error, element) {
            if (element.attr("name") == "data[Org][corevalues][]") {
                error.insertAfter("#corevaluesdropdown .select-style");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            'data[Org][name]': {
                required: true,
            },
            'data[Org][corevalues][]': {
                required: true,
            },
        },
        messages: {
            'data[Org][name]': {
                required: "Organization Name is required",
            },
            'data[Org][corevalues][]': {
                required: "Atleast a Core Value is required",
            },
        },
        submitHandler: function (form) {
            var finalcheck = new Array();
            var validate = false;
            $("#addcoretable tr").each(
                    function () {
                        if (typeof $(this).attr("id") != "undefined") {
                            if ($(this).find("#cvactive").val() == 'active' && $(this).find("#saveunsave").val() == "save") {
                                validate = true;
                            }
                        }
                    }
            )
            finalcheck.push({'value': "corevalue", 'validate': validate});
            if (finalcheck[0]["validate"] == true) {
                form.submit();
            } else {
                alertbootbox("Atleast a Value needs to be save and active for Core Value");
                return false;
            }

        },
    });
});

//===============to go one page back with cancel
$(document).on("click", "#clientformcancel, #orgformcancel", function () {
    console.log("test");
    window.history.back();
})

$(document).on("click", ".js_checkTerms", function (e) {
//    console.log( $(this).attr('href'));
    e.preventDefault();
    if ($('#congratsAcceptTnc').is(":checked")) {
        var redirectUrl = $(this).attr('href');
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: siteurl + 'cajax/acceptTnC',
            success: function (response) {
                if (response.success) {
                    window.location.href = redirectUrl;
                } else {
                    alertbootbox(response.msg);
                }

            }
        });

    } else {
        alertbootbox("Accept End User License Agreement");
    }
});

$("#OrganizationCustomerportalsettingForm").validate({
    rules: {
        'data[User][email]': {
            required: true,
            email: true,
        },
        'data[User][fname]': {
            required: true,
        },
        'data[User][lname]': {
            required: true,
        },
        'data[User][password]': {
            required: true,
            minlength: 8,
        },
        'data[User][confirm_password]': {
            equalTo: '#UserPassword'
        },
    },
    messages: {
        'data[User][email]': {
            required: "Email is required",
            email: "Invalid email"
        },
        'data[User][fname]': {
            required: "First Name is required",
        },
        'data[User][lname]': {
            required: "Last Name is required",
        },
        'data[User][password]': {
            required: "Password is required",
            minlength: "Password Must be of atleast 8 characters"
        },
        'data[User][confirm_password]': {
            equalTo: "Confirm Password do not match",
        },
    }
});

$("#CustomerportalsettingFormSubmit").click(function () {
    //alert("test");
    console.log("TEST");
    return false;
    $("#OrganizationCustomerportalsettingForm").submit();
})