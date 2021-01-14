$(document).ready(function () {

    $(document).on('click', '.submit-comment', function () {
        var msgText = $.trim($(".add-msg").val());
        $(".empty-comment-err").hide();
        if(msgText.length > 0){
            $("#postDetailsForm").submit();
        }else{
            $(".empty-comment-err").show();
        }
        
        
    });
    
    $("#postDetailsForm").ajaxForm({
        dataType: 'html',
        url: siteurl + "post/addcomment",
        beforeSubmit: function () {
            var endorsee = $("input.js_endorsee");
            var error = false;
            var added = false;

            $("#validImageError").html("");
            if (error) {
                window.scrollTo(0, 0);
                return false;
            } else {
                $("#endorseSubmit").prop("disabled", true);
                $(".js_Loader").removeClass('hidden');
            }
        },
        success: function (response) {
            $(".js_Loader").addClass('hidden');
            $(".add-msg").val('');
            $(".comment_container").append(response);
        }
    });


    $('.js_emojis').on('hide.bs.modal', function () {
        $(this).find(".switchbutton").remove();
    });

    $(document).on('click', '.loadmorecomments', function () {
        $(".toploader").removeClass('hidden');
        var post_id = $(this).attr('data-post-id');
        var page_no = $(this).attr('data-page-no');
        $.ajax({
            url: root + "post/loadmorecomments",
            type: "post",
            dataType: 'html',
            data: {post_id: post_id, page_no: page_no},
            success: function (response) {
                $(".toploader").addClass('hidden');
                $(".comment_container").prepend(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }


        });
    });
});