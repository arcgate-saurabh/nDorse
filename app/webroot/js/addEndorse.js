//document ready start
$(document).ready(function () {
    $("#endorsementAddForm").ajaxForm({
        dataType: 'json',
        beforeSubmit: function () {
            var endorsee = $("input.js_endorsee");
            var addMsg = $.trim($("textarea.add-msg").val());
            var addMsgOptional = $("textarea.add-msg").attr('data-optional');
            var error = false;
            var added = false;

            var userMsgMinChar = $(document).find("#user_msg").attr('data-min');
            $(".err").hide();


            $("#validImageError").html("");
            if (endorsee.length == 0) {
                //                selectedValues.each(function(){
                //                   var endorsementfor = $(this).attr("data-endorsementfor");
                //                    var endorsedId = $(this).attr("data-endorsedid");
                //                    $( "#endorsementAddForm" ).append('<input type="hidden" name="endorsee['+endorsementfor+'][]" value="'+endorsedId+'">');
                //console.log('added');
                //added = true;
                //                });
                //            return false;
                //            } else {
                $("#searchError").html("Please add user to nDorse.");
                error = true;
            } else {
                $("#searchError").html("");
            }
            if (addMsgOptional == 0) {
                if (addMsg.length < 1) {
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
            var checked = $(".js_coreValue:checked").length > 0;
            if (!checked) {
                $("#coreValueError").html("Please select Core Value(s) to nDorse.");
                error = true;
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
                uploadEndorseAttachments(response.endorsementIds, 0, response.msg);
            } else {
                alertbootbox(response.msg);
            }

        }
    });


    $('.js_emojis').on('hide.bs.modal', function () {
        $(this).find(".switchbutton").remove();
    });
});//document ready end


var endorseSelected = {
    "user": [],
    "department": [],
    'entity': []
};
var hashtags = [];
var selectedSyubCenterID = '';
$(document).on("keyup", "#endorsementSearchKey", function () {
    //    console.log(endorseSelected);



    if ($(this).val().length >= 2) {
        var keyword = $(this).val();
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/searchInOrg',
            data: {
                keyword: keyword,
                endorseSelected: endorseSelected,
                subcenter_id: selectedSyubCenterID,
                limit: endorsementLimit
            },
            success: function (response) {
                $("#nDorse-search").html(response);
                $("#nDorse-search").removeClass('hidden');
            }
        });
    } else {
        $("#nDorse-search").addClass('hidden');
    }
});


$(document).on("click", ".js_searched", function () {
    var endorsementfor = $(this).attr("data-endorsementfor");
    var endorsedId = $(this).attr("data-endorsedid");
    var subcenterId = $(this).attr("data-subcenterid");
    if(subcenterId == 'undefined'){
        subcenterId = "";
    }
    var name = $(this).find(".js_searchedName").html();
    var subCenterName = $(this).find(".subcentername").html();

    var endorseType = $("#endorseType").val();
    if (endorseType == "private" && (endorsementfor == 'department' || endorsementfor == "entity")) {
        alertbootbox("A Private nDorsement can only be sent to another User.");
    } else {
        endorseSelected[endorsementfor].push(endorsedId);

        var addHtml = '<span class="js_selectedValue" data-subcenterid="' + subcenterId + '"  data-endorsedid="' + endorsedId + '" data-endorsementfor="' + endorsementfor + '">' + name + ' <a href="javascript:void(0);" class="js_removeSelected" rel="' + endorsementfor + "_" + endorsedId + '">X</a></span>';

        $("#selectedValues").append(addHtml);
        $("#endorsementAddForm").append('<input type="hidden" class="js_endorsee" name="endorsee[' + endorsementfor + '][]" value="' + endorsedId + '" id="' + endorsementfor + "_" + endorsedId + '">');
        $("#endorsementAddForm").append('<input type="hidden" class="" name="subcenter_for[' + endorsedId + ']" value="' + subcenterId + '" id="subcenterId' + "_" + endorsedId + '">');
        $(".selected-values ").removeClass('hidden');
    }
    $("#nDorse-search").addClass('hidden');
    if (selectedSyubCenterID == '') {
        if (subCenterName != '') {
            //$(".selectedSubcenter").html(" You have selected the user from Sub center- <b>" + subCenterName + " </b>. Now you can search within this Sub center only.").css('display', 'block');
//            renderSubcenterCorevalue(subcenterId);
        }
    }
//    selectedSyubCenterID = subcenterId;
    $("#endorsementSearchKey").val("");
});




$(document).on("click", ".js_closeSearch", function () {
    $("#nDorse-search").html("");
    $("#endorsementSearchKey").val("");
    $("#nDorse-search").addClass('hidden');
});

$(document).on("click", ".js_removeSelected", function () {
    var id = $(this).attr('rel');
    var splitted = id.split("_");
    var endorsementFor = splitted[0];
    var endorsedId = splitted[1];
    var index = endorseSelected[endorsementFor].indexOf(endorsedId);
    endorseSelected[endorsementFor].splice(index, 1);
    $("#" + id).remove();
    $("#subcenterId_" + endorsedId).remove();
    $(this).closest(".js_selectedValue").remove();
    if ($("#selectedValues").find(".js_selectedValue").length == 0) {
        $('.selected-values').addClass('hidden');
        $('.selectedSubcenter').html('').hide();
        selectedSyubCenterID = "";
//        renderSubcenterCorevalue(selectedSyubCenterID);
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
    $('.selectedSubcenter').html('').hide();
    selectedSyubCenterID = "";

//    renderSubcenterCorevalue(selectedSyubCenterID);

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

function uploadEndorseAttachments(endorsementIds, index, msg) {
    //     var fileSelect = document.getElementById( "endorseImages" );
    //     fileList = fileSelect.files;
    // 
    //     console.log('here');
    //     console.log(fileList.length);
    //     for(i in fileList) {
    if (index < fileList.length) {
        if (typeof fileList[index] == 'undefined') {
            index++;
            uploadEndorseAttachments(endorsementIds, index, msg);
        } else {
            var fd = new FormData( );
            fd.append('file', fileList[index]);
            fd.append('endorsement_ids', endorsementIds);
            console.log(fd);
            $.ajax({
                'url': siteurl + 'endorse/sendAttachments',
                'type': 'POST',
                'data': fd,
                'contentType': false,
                'processData': false,
                //            'xhr': function() {  
                //               var xhr = $.ajaxSettings.xhr();
                //               if(xhr.upload){ 
                //                 xhr.upload.addEventListener('progress', progressbar, false);
                //               }
                //               return xhr;
                //             },
                'success': function () {
                    index++;
                    uploadEndorseAttachments(endorsementIds, index, msg);
                    //               count++;
                    //               $this.trigger('ajax');
                }
            });
        }
    } else {
        //redirect
        alertbootboxcb(msg, function () {
            var endorseType = $("#endorseType").val();
            endorseType = (endorseType == 'standard') ? "Public" : endorseType.charAt(0).toUpperCase() + endorseType.slice(1);
            var endorseeList = "";

            $("input.js_endorsee").each(function () {
                var endorseeId = $(this).attr('id');
                var res = endorseeId.split("_");
                var endorseeType = res[0];

                endorseeList += endorseeType.charAt(0).toUpperCase() + endorseeType.slice(1) + ",";
            });

            endorseeList = endorseeList.slice(0, -1);

            ga('send', 'event', 'nDorsement Done', endorseType, endorseeList);
            window.location.href = siteurl + "endorse";
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
        if (totalSelectedStickers > 1) {
            alert("Maximum of 2 stickers");
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
        $("#endorsementAddForm").append('<input type="hidden" class="js_emojiInput" name="emojis[]" value="' + i + '" >');
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


/* Render Core values according SUBCENTER */
function renderSubcenterCorevalue(subcenter_id) {
    $.ajax({
//        'url': siteurl + 'cajax/getSubcenterCoreValues',
//        'type': 'POST',
//        'data': {subcenter_id: subcenter_id},
//        success: function (response) {
//            $(".ndorse-cvalue").html(response);
//        }
    });

}


$(document).on("click", ".hashtag-btn", function () {
    var hashtagID = $(this).attr('data-id');
    if ($(this).hasClass('btn-default')) {//selected
        $(this).removeClass('btn-default').addClass('btn-success');
        hashtags.push(hashtagID);
    } else {//Removed
        $(this).addClass('btn-default').removeClass('btn-success');
        var index = hashtags.indexOf(hashtagID);
        hashtags.splice(index, 1);
    }
    $('#hashtags').val(hashtags);
});
