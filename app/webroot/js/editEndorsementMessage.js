$(document).ready(function () {
//code starts for edit ndorsement message
$(document).on("click", ".live-feeds-edit-ndorse-message", function (event) {

    console.log($(this).attr("endorse_id"));
    var clnew = $(event.target).attr('class');

    if (clnew == undefined) {
        window.location.href = siteurl + "endorse/details/" + $(this).attr("endorse_id") + "?mode=edit";
    } else if (clnew.search("like-img-endorse") < 0 && clnew.search("endorse-user") < 0) {
        window.location.href = siteurl + "endorse/details/" + $(this).attr("endorse_id") + "?mode=edit";
    }
    
});
//ends here

/** Added by Saurabh on @19112021 
* Edit post message from feeds
*/

    $(document).on("click", ".js-edit-endorse-message", function (e) {
        var endorseid = $(this).attr("data-endorse-id");
        var endorsemsg = $.trim($(".js-get-msg").val());
        var addMsg = $.trim($("textarea.add-msg-val").val());

        var addMsgOptional = $.trim($("textarea.add-msg-val").attr('data-optional'));
        //alert(addMsgOptional); 
        var userMsgMinChar = $(document).find("#user_msg_val").attr('data-min');
        
        // if(userMsgMinChar == 0){
        //     userMsgMinChar = 50;
        // }

        // if(addMsgOptional == 1){
        //     addMsgOptional = 0;
        // }
        //alert(userMsgMinChar);
        error = false;
        if (addMsgOptional == 0) {
            if (addMsg.length < 1) {
                $("#endMessageError").html("Message cannot be empty.");
                error = true;
            } else if (addMsg.length < userMsgMinChar) {
                $("#endMessageError").html('Please enter minimum ' + userMsgMinChar + ' characters in your comment.');
                error = true;
            } else {
                $("#endMessageError").html("");
            }
        } else {
            $("#endMessageError").html("");
        }
        if(!error){
            $.ajax({
                type: "POST",
                url: siteurl + 'cajax/editndorsementmsg',
                data: {endorseid: endorseid, endorsemsg: endorsemsg},
                success: function (data, textStatus, xhr) {
                    var jsonparser = $.parseJSON(data);
                    var status = jsonparser["result"]["status"];
                    if (jsonparser["result"]["status"]) {
                        alertbootboxcb(jsonparser["result"]["msg"], function () {
                            window.location.href = siteurl + "endorse";
                        });
                    } else {
                        alertbootbox(jsonparser["result"]["msg"]);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }
    });


$(document).on('click', '.js-cancel-editing', function () {
    var endorseid = $(this).attr("data-endorse-id");
    window.location.href = siteurl + "endorse/details/" + endorseid+"?mode=edit";
});

$(document).on('click', '.js-cancel-editing-redirect', function () {
    window.location.href = siteurl + "endorse";
});
    
$("#user_msg_val").focus();

});   