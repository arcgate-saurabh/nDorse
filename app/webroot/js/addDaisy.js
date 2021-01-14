//document ready start
$(document).ready(function () {
    $("#endorsementDaisyForm").ajaxForm({
        dataType: 'json',
        beforeSubmit: function () {
            var endorseefirstname =$.trim($("#daisySearchKey").val());
            var addMsg = $.trim($("textarea.add-msg").val());
            var addMsgOptional = $("textarea.add-msg").attr('data-optional');
            var error = false;
            var added = false;

            var userMsgMinChar = $(document).find("#user_msg").attr('data-min');
            $(".err").hide();


            $("#validImageError").html("");
//            console.log(endorseefirstname.length);
            if (endorseefirstname.length == 0) {
                //                selectedValues.each(function(){
                //                   var endorsementfor = $(this).attr("data-endorsementfor");
                //                    var endorsedId = $(this).attr("data-endorsedid");
                //                    $( "#endorsementAddForm" ).append('<input type="hidden" name="endorsee['+endorsementfor+'][]" value="'+endorsedId+'">');
                //console.log('added');
                //added = true;
                //                });
                //            return false;
                //            } else {
                 $("#searchError").html("Please enter first name to nominate.");
                error = true;
            } else {
                $("#searchError").html("");
            }
            if (addMsgOptional == 0) {

                if (userMsgMinChar != 0 && addMsg.length < 1) {
                    $("#messageError").html("Message cannot be empty.");
                    error = true;
                } else if (addMsg.length < userMsgMinChar) {
                    $("#messageError").html('Please enter minimum ' + userMsgMinChar + ' characters in your comment.');
                    error = true;
                } else {
                    $("#messageError").html("");
                }
            } else {
                $("#messageError").html("");
            }

            var daisy_show_core_values = $("#daisy_show_core_values").val();
            var checked = $(".js_coreValue:checked").length > 0;
            if (!checked) {
                if (daisy_show_core_values == 1) {
                    $("#coreValueError").html("Please select Core Value(s) to nDorse.");
                    error = true;
                }

            } else {
                $("#coreValueError").html("");
            }

            if (error) {
                window.scrollTo(0, 0);
                return false;
            } else {
                $("#endorseSubmit").prop("disabled", true);
                $(".js_Loader").removeClass('hidden');
            }
        },
        success: function (response) {
            if (response.success == true) {
                uploadDaisyAttachments(response.endorsementIds, 0, response.msg);
            } else {
                alertbootbox(response.msg);
            }

        }
    });


    $('.js_emojis').on('hide.bs.modal', function () {
        $(this).find(".switchbutton").remove();
    });
});//document ready end

var totalFileSize = parseInt(0);
var endorseSelected = {
    "user": [],
    "department": [],
    'entity': []
};
$(document).on("keyup", "#daisySearchKey", function () {
    //    console.log(endorseSelected);
    removeAutoFillParams();
    $("#searchError").html('');
    if ($(this).val().length >= 2) {
        var keyword = $(this).val();
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/searchInOrgDaisy',
            data: {
                keyword: keyword,
                endorseSelected: endorseSelected,
                limit: 100
            },
            success: function (response) {
//                $("#nDorse-search").html(response);
//                $("#nDorse-search").removeClass('hidden');
                $("#daisy-search").html(response);
                $("#daisy-search").removeClass('hidden');
            }
        });
    } else {
        $("#daisy-search").addClass('hidden');
    }
});


$(document).on("click", ".js_searched", function () {
    var endorsementfor = 'user';
    var endorsedId = $(this).attr("data-endorsedid");
    var name = $(this).find(".js_searchedName").html();
    var endorseType = 'daisy';
    var firstname = $(this).find("#firstname").val();
    var lastname = $(this).find("#lastname").val();
    var department_id = $(this).find("#department_id").val();
    var department_name = $(this).find("#department_name").val();

//    console.log(firstname);
//    console.log(lastname);
//    console.log(department_id);
//    console.log(department_name);

    $("#daisySearchKey").val(firstname);
    $("#endorse_lastname").val(lastname);
    $("#endorsementDepartmentUnitId").val(department_id);
    $("#department_unit").val(department_name);
    $("#endorseID").val();
//    endorseSelected[endorsementfor].push(endorsedId);

//    var addHtml = '<span class="js_selectedValue" data-endorsedid="' + endorsedId + '" data-endorsementfor="' + endorsementfor + '">' + name + ' <a href="javascript:void(0);" class="js_removeSelected" rel="' + endorsementfor + "_" + endorsedId + '">X</a></span>';

//    $("#selectedValues").append(addHtml);
    $('.js_endorsee').remove();
    $("#endorsementDaisyForm").append('<input type="hidden" class="js_endorsee" name="endorsee[' + endorsementfor + '][]" value="' + endorsedId + '" id="' + endorsementfor + "_" + endorsedId + '">');
//    $(".selected-values ").removeClass('hidden');

    $("#daisy-search").addClass('hidden');

    $("#endorsementSearchKey").val("");
});

$(document).on("click", ".js_closeSearch", function () {
    $("#daisy-search").html("");
    $("#endorsementSearchKey").val("");
    $("#daisy-search").addClass('hidden');
});

$(document).on("click", ".js_removeSelected", function () {
    var id = $(this).attr('rel');
    var splitted = id.split("_");
    var endorsementFor = splitted[0];
    var endorsedId = splitted[1];
    var index = endorseSelected[endorsementFor].indexOf(endorsedId);
    endorseSelected[endorsementFor].splice(index, 1);
    $("#" + id).remove();
    $(this).closest(".js_selectedValue").remove();
    if ($("#selectedValues").find(".js_selectedValue").length == 0) {
        $('.selected-values').addClass('hidden');
    }
});

$(document).on("click", ".js_clearAll", function () {
    endorseSelected = {
        "user": [],
        "department": [],
        'entity': []
    };
    $("#selectedValues").html("");
    $("input.js_endorsee").remove();
    $('.selected-values').addClass('hidden');
});

fileList = [];
$(document).on("change", "#endorseImages", function () {
    //$("input:file").change(function (){
    //       console.log($(this));
    //       $(".filename").html(fileName);
    var imageError = false;
    var imageUploaded = false;

    for (var i = 0; i < $(this).get(0).files.length; ++i) {
        if (isValidImage($(this).get(0).files[i])) {
            var nextCount = fileList.length;
            fileList.push($(this).get(0).files[i]);
            readURL($(this).get(0).files[i], nextCount);
            imageUploaded = true;
        } else {
            imageError = true;
        }
    }

    if (imageError) {
        $("#validImageError").html("Select valid images. Only jpg/jpeg/png/gif images are allowed.")
    }

    if (imageUploaded) {
        $('#imagePanel').removeClass('hidden');
    }

});

function isValidImage(file) {
    var validMime = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg'];
    var validExtension = ["jpg", "jpeg", "gif", "png"];
    var fileName = file.name;
    var extension = fileName.split('.').pop();
    extension = extension.toLowerCase();
    var fileMime = file.type;
    fileMime = fileMime.toLowerCase();

    if (validExtension.indexOf(extension) == -1) {
        return false
    } else {
        if (validMime.indexOf(fileMime) == -1) {
            return false;
        } else {
            return true;
        }
    }

}

function readURL(input, index) {
    if (input) {
        var reader = new FileReader();

        reader.onload = function (e) {
            var imageView = ' <div class="col-md-2 js_thumbDiv">' +
                    '<div class="onefive">' +
                    '<div class="img-close"  >' +
                    '<button class="btn btn-default btn-xs js_removeThumb" rel ="' + index + '" type="button">X</button>' +
                    '</div>' +
                    '<img src="' + e.target.result + '" class="attached-item" alt="" width="100" height="100" id="thumb_' + index + '" />' +
                    '                  </div>' +
                    '</div>';
            $("#imagePanel").prepend(imageView);
            //                $('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input);
    }
}

function uploadDaisyAttachments(endorsement_id, index, msg) {

    var validMime = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    if (index < fileList.length) {
        if (typeof fileList[index] == 'undefined') {
            index++;
            uploadDaisyAttachments(endorsement_id, index, msg);
        } else {
            var extension = fileList[index].name.split('.').pop();
            extension = extension.toLowerCase();
            console.log(fileList[index]);
            var fd = new FormData();
            fd.append('file', fileList[index]);
            fd.append('endorsement_ids', endorsement_id);
            fd.append('file_extension', extension);
            console.log(fd);

            if (validMime.indexOf(fileList[index].type) == -1) {
//                $.ajax({
//                    'url': siteurl + 'endorse/sendAttachments',
//                    'type': 'POST',
//                    'data': fd,
//                    'contentType': false,
//                    'processData': false,
//                    //            'xhr': function() {  
//                    //               var xhr = $.ajaxSettings.xhr();
//                    //               if(xhr.upload){ 
//                    //                 xhr.upload.addEventListener('progress', progressbar, false);
//                    //               }
//                    //               return xhr;
//                    //             },
//                    'success': function () {
//                        index++;
//                        uploadDaisyAttachments(endorsement_id, index, msg);
//                        //               count++;
//                        //               $this.trigger('ajax');
//                    }
//                });
            } else {
                var uploadFileType = '';
                if (fileList[index].type == 'application/pdf') {
                    uploadFileType = 'PDF';
                } else if (fileList[index].type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || fileList[index].type == 'application/vnd.ms-excel') {
                    uploadFileType = 'XLS';
                } else if (fileList[index].type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || fileList[index].type == 'application/vnd.ms-powerpoint') {
                    uploadFileType = 'PPT';
                } else if (fileList[index].type == 'application/msword' || fileList[index].type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    uploadFileType = 'DOC';
                }
                fd.append('file_type', uploadFileType);
                $.ajax({
                    'url': siteurl + 'endorse/sendAttachedFiles',
                    'type': 'POST',
                    'data': fd,
                    'contentType': false,
                    'processData': false,
                    //Before 1.5.1 you had to do this:
                    beforeSend: function (x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("multipart/form-data");
                        }
                    },
                    mimeType: 'multipart/form-data',
                    'success': function () {
                        index++;
                        uploadDaisyAttachments(endorsement_id, index, msg);
                    }
                });
            }


        }
    } else {
        //redirect
        alertbootboxcb("DAISY nDorsement submitted.", function () {
            //var endorseType = $("#endorseType").val();
            //endorseType = (endorseType == 'standard') ? "Public" : endorseType.charAt(0).toUpperCase() + endorseType.slice(1);
//            var endorseeList = "";
//
//            $("input.js_endorsee").each(function () {
//                var endorseeId = $(this).attr('id');
//                var res = endorseeId.split("_");
//                var endorseeType = res[0];
//
//                endorseeList += endorseeType.charAt(0).toUpperCase() + endorseeType.slice(1) + ",";
//            });
//
//            endorseeList = endorseeList.slice(0, -1);

            //ga('send', 'event', 'nDorsement Done', endorseType, endorseeList);
            window.location.href = siteurl + "endorse/daisy";
        });

    }
}

$(document).on("click", ".js_removeThumb", function () {
    console.log(fileList);
    //     var id = $(this).attr('id');
    //     console.log(id);
    //     var res = id.split("_");
    var index = $(this).attr('rel');
    delete fileList[index];
    $(this).closest(".js_thumbDiv").remove();

    if ($("#imagePanel").find(".js_thumbDiv").length == 0) {
        $('#imagePanel').addClass('hidden');
        $("#validImageError").html("")
    }
});

var stickerList = [];
$(document).on("click", ".js_addSticker", function () {
    var image = $(this).attr('rel');
    //add some class on it
    if ($(this).hasClass('js_stickerAdded')) {
        $(this).removeClass('js_stickerAdded');
        $(this).find(".switchbutton").remove();
        delete stickerList[image];
    } else {
        var totalSelectedStickers = $(".js_stickerAdded").length;
        if (totalSelectedStickers > 4) {
            alert("Maximum of 5 stickers");
            return false;
        }
        $(this).addClass('js_stickerAdded');
        $(this).append(' <div class="switchbutton"><img class="defaultorg" alt="" src="' + siteurl + '/img/selected-org.png"></div>');
        stickerList[image] = 1;
    }
});

$(document).on("click", ".js_selectEmojis", function () {
    $("#stickerPanel").html("");
    $("input.js_emojiInput").remove();
    $(".js_emojis").modal('hide');
    for (i in stickerList) {
        console.log(i);
        var imageView = ' <div class="col-md-2 js_thumbDiv">' +
                '<div class="onefive">' +
                '<div class="img-close"  >' +
                '<button class="btn btn-default btn-xs js_removeSticker" rel ="' + i + '" type="button">X</button>' +
                '</div>' +
                '<img src="' + emojiUrl + i + '" class="attached-item" alt="" width="100" height="100"  />' +
                '                  </div>' +
                '</div>';
        $("#stickerPanel").append(imageView);
        $("#stickerPanel").removeClass('hidden');
        $("#endorsementDaisyForm").append('<input type="hidden" class="js_emojiInput" name="emojis[]" value="' + i + '" >');
    }
    $("#stickerPanel").append('<div class="clearfix"></div>');
    stickerList = [];
});



$(document).on("click", ".js_removeSticker", function () {
    var name = $(this).attr('rel');
    delete stickerList[name];
    $(this).closest(".js_thumbDiv").remove();
    $("input.js_emojiInput[value='" + name + "']").remove();
    if ($("input.js_emojiInput").length == 0) {
        $('#stickerPanel').addClass('hidden');
    }
});


$(document).on("click", ".js_noAdd", function () {
    var endorseLimit = $("#endorseLimit").val();
    alertbootbox("You cannot ndorse more then " + endorseLimit + " in a month.");
});


/* Upload post files/Dcouments */
fileList = [];
$(document).on("change", "#postFiles", function () {

    //$("input:file").change(function (){
    //       console.log($(this));
    //       $(".filename").html(fileName);
    var imageError = false;
    var imageUploaded = false;

    for (var i = 0; i < $(this).get(0).files.length; ++i) {
        if (isValidFile($(this).get(0).files[i])) {
            var nextCount = fileList.length;
            fileList.push($(this).get(0).files[i]);
            readFilesURL($(this).get(0).files[i], nextCount);
            imageUploaded = true;
        } else {
            imageError = true;
        }

    }
    return false;
    if (imageError) {
        $("#validImageError").html("Select valid images. Only jpg/jpeg/png/gif images are allowed.")
    }


});

function isValidFile(file) {
    console.log(file.size);
    //5242880 5MB
    if (file.size > 5242880) {
        $("#validFileError").html("Please select file upto 5 mb.");
        return false;
    }

    totalFileSize = parseInt(totalFileSize) + parseInt(file.size);
    console.log("Total files size : " + totalFileSize);

    if (parseInt(totalFileSize) > parseInt(10480000)) { // Total files size upto 10mb
        $("#validTotalFileError").html("Selected file's total size should be upto 10 mb.");
        $(document).find("#endorseSubmit").attr('disabled', true);
    } else {
        $(document).find("#endorseSubmit").attr('disabled', false);
    }

    var validMime = ['application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint'];
    var validExtension = ["xls", "doc", "ppt", "pdf"];
    var fileName = file.name;
    var extension = fileName.split('.').pop();
    extension = extension.toLowerCase();
    var fileMime = file.type;
    fileMime = fileMime.toLowerCase();
    if (validExtension.indexOf(extension) == -1) {
        console.log("In Valid File : " + extension);
        $("#validTotalFileError").html("Please select valid files. (Allowed files: doc, pdf, ppt, xls)");
        return false
    } else {
        if (validMime.indexOf(fileMime) == -1) {
            console.log("In Valid File 2, File : " + file);
            console.log("In Valid File 2 FileMine : " + fileMime);
            return false;
        } else {
            console.log("Valid File");
            return true;
        }
    }

}


function readFilesURL(input, index) {
    if (input) {
        console.log(input.type);
        var filetype = input.type;
        var filename = input.name;
        var uploadFileType = '';
        var validType = false;
        var fileTypeIcon = '';
        if (filetype == 'application/pdf') {
            uploadFileType = 'PDF';
            fileTypeIcon = siteurl + "/img/pdf.png";
            validType = true;
//        } else if (filetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || filetype == 'application/vnd.ms-excel') {
        } else if (filetype == 'application/vnd.ms-excel') {
            uploadFileType = 'XLS';
            fileTypeIcon = siteurl + "/img/xls.png";
            validType = true;
        } else if (filetype == 'application/vnd.ms-powerpoint') {
            uploadFileType = 'PPT';
            fileTypeIcon = siteurl + "/img/ppt.png";
            validType = true;
        } else if (filetype == 'application/msword') {
            uploadFileType = 'DOC';
            fileTypeIcon = siteurl + "/img/doc.png";
            validType = true;
        }
        if (validType) {
            var imageView = ' <div class="col-md-3 attach-files js_thumbDiv">' +
                    '<div class="attach-files-inner onefive">' +
                    '<div class="attach-files-close"  >' +
                    '<button class="btn btn-default btn-xs js_removeFiles" rel ="' + index + '" type="button">X</button>' +
                    '</div>' +
                    '<span><img class="no-hand" src="' + fileTypeIcon + '"  align="" width="" alt=""/></span>' +
                    '<span class="file-name">' + filename + '</span>' +
                    '</div></div>' +
                    '</div>';
            $("#filesPanel").prepend(imageView);
        }
//        if (validType) {
//            var imageView = ' <div class="col-md-2 js_thumbDiv">' +
//                    '<div class="onefive">' +
//                    '<div class="img-close"  >' +
//                    '<button class="btn btn-default btn-xs js_removeFiles" rel ="' + index + '" type="button">X</button>' +
//                    '</div>' +
//                    '<span style="color:red;">' + filename + ' : ' + uploadFileType + '</span>' +
//                    '</div></div>' +
//                    '</div>';
//            $("#filesPanel").prepend(imageView);
//        }
        $(document).find('#filesPanel').removeClass('hidden');

    }

}
$(document).on("keyup", "#department_unit", function () {
    removeAutoFillParams();
});
$(document).on("keyup", "#endorse_lastname", function () {
    removeAutoFillParams();
});

function removeAutoFillParams() {
    $("#endorsementDepartmentUnitId").val('');
    $('.js_endorsee').remove();
}