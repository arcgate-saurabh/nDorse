//document ready start


var dateparameters = {
    showOn: "button",
    buttonImage: siteurl + "img/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    required: true,
    dpDate: true,
    maxDate: '0d',
    showAnim: "fadeIn",
    yearRange: "-100:+0",
    //dateFormat: 'yy-mm-dd'
    dateFormat: 'mm-dd-yy'
};

$(document).ready(function () {
    //
    $("#UserCountry").change(function () {

        var optionSelected = $("option:selected", this);
        var countryId = this.value;
        contentobj = eval("countrydata." + countryId);
        optionsHtml = '<option value="">' + 'Select State' + '</option>';
        $.each(contentobj, function (index, val) {

            optionsHtml += '<option value="' + val + '">' + val + '</option>';


        });

        $('#UserState').html(optionsHtml);

    });
    //
    jQuery.validator.addMethod("lettersonly", function (value, element) {
        return this.optional(element) || /^[a-z\s]+$/i.test(value);
    }, "Only alphabetical characters");
    //
    $(' #UserSkills, #UserHobbies').change(function () {
        var divselectid = $(this).attr("id");

        var selval1 = String($(this).val());

        var selval = selval1.split(",");

        if (selval.indexOf("other") > 0)
        {

            for (var i = 0, len = selval.length; i < len; i++)
            {
                if (selval[i] != "other")
                {
                    $('#' + divselectid + ' option[value=' + selval[i] + ']').attr('selected', false);
                }
            }
            $("#other_" + divselectid).show();
        } else if (selval == "other")
        {
            $("#other_" + divselectid).show();
        } else
        {
            $("#other_" + divselectid).hide();
        }

    });
    //
    $("#datepicker_dob").datepicker(dateparameters);

    $('#user_upload_photo').bind("click", function () {
        $('#photo').click();
    });
    $("#photo").change(function (e) {
        $("#validImageError").text("");
        //  $("#UserphotoSetimageForm").submit();
        var imageformat = $(this).val().split(".")[1];
        if (trimAndLowerCaseString(imageformat) == "png" || trimAndLowerCaseString(imageformat) == "jpg" || trimAndLowerCaseString(imageformat) == "jpeg" || trimAndLowerCaseString(imageformat) == "gif") {
            //
            readURL(this, 'client_image');
            
            



        } else {
            $(this).val("");
            $("#validImageError").text("Select valid images. Only jpg/jpeg/png/gif images are allowed.");
            //alertbootbox("File type of uploaded image is not allowed.")
        }
    });
    $("#UserphotoSetimageForm").ajaxForm({
        url: userprofile,
        success: function (data) {
            var parseData = JSON.parse(data);
            if (parseData.status) {
                //$("#img_msg").html("image has been updated successfully");
                $('#client_image').attr('src', imgurl + "small/" + parseData.imageloc);
                $('#client_image_name').val(parseData.imageloc);
            } else {
                // $("#img_msg").html(parseData.error);
                alertbootbox(parseData.error);
                return false;
            }
        },
        error: function (msg) {
            alertbootbox(msg);
            return false;
        }
    });
    $("#user_remove_photo").click(function () {

        $("#client_image").attr("src", siteurl + "img/p_pic.png");
        $("#client_image_name").val("");

        $("#photo").val("");
        //$.ajax({
        //    url: siteurl + 'users/deleteimage',
        //    type: "POST",
        //    data: {image_name: current_image},
        //    success: function (data, textStatus, jqXHR) {
        //        //data - response from server
        //        //window.location = siteurl+'courses/progress';
        //        var parseData = JSON.parse(data);
        //        if (parseData.status) {
        //
        //            //$("#img_msg").html("image has been removed successfully");
        //            //alert(parseData.imageloc);
        //            $('#client_image').attr('src', siteurl + parseData.imageloc);
        //            $('#client_image_name').val("");
        //            $('#photo').val("");
        //        } else {
        //            $("#img_msg").html(parseData.error);
        //            // buttonclick.removeAttr('disabled');
        //            return false;
        //        }
        //    }, error: function (jqXHR, textStatus, errorThrown) {
        //
        //    }
        //});


    });
    // form submit validation

    $("#userprofilesubmit").on("click", function () {
        $("#UserEditprofileForm").submit();
    });

    $("#UserEditprofileForm").validate({
        rules: {
            'data[User][fname]': {
                required: true,
                lettersonly: true,
            },
            'data[User][lname]': {
                required: true,
                lettersonly: true,
            },
            'data[User][mobile]': {
                required: true,
                digits: true,
            },
//            'data[User][street]': {
//                required: true,
//            },
//            'data[User][city]': {
//                required: true,
//            },
//            'data[User][zip]': {
//                required: true,
//            },
//            'data[User][country]': {
//                required: true,
//            },
//            'data[User][state]': {
//                required: true,
//            },
        },
        messages: {
            'data[User][fname]': {
                required: "First name required",
            },
            'data[User][lname]': {
                required: "Last name required",
            },
            'data[User][mobile]': {
                required: "Mobile number required",
                digits: "only number allowed",
            },
//            'data[User][street]': {
//                required: "Street name required",
//            },
//            'data[User][city]': {
//                required: "City name required",
//            },
//            'data[User][zip]': {
//                required: "Zip required",
//            },
//            'data[User][country]': {
//                required: "County name required",
//            },
//            'data[User][state]': {
//                required: "State name required",
//            },
        }
    });

}); //document ready end
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
