/* 
 * First time login walkthrough
 */

$(document).ready(function() {
    $("#finish").addClass("hide");
     var images = [
       siteurl + "img/login_walkthrough_step_1.jpg",
       siteurl + "img/login_walkthrough_step_2.jpg",
       siteurl + "img/login_walkthrough_step_3.jpg",
       siteurl + "img/login_walkthrough_step_4.jpg",
       siteurl + "img/login_walkthrough_step_5.jpg",
       siteurl + "img/login_walkthrough_step_6.jpg"
       //"http://lorempixel.com/200/100/",
     ];

     var imageIndex = 0;
     var iLength = images.length - 1;

     // $("#previous").on("click", function() {
     //   imageIndex = (imageIndex - 1);
     //   $("#image").attr('src', images[imageIndex]);

     //   $("#next").prop("disabled", false);
     //   $("#finish").removeClass("show");
     // });

    
    $("#next").on("click", function() {
       imageIndex = (imageIndex + 1);
        $("#image").attr('src', images[imageIndex]);
        if (imageIndex == iLength) {
            $(this).prop("disabled", true);
            
            $("#finish").removeClass("hide");
            $("#finish").addClass("show");
            $("#next").addClass("hide");
            $("#skipped_login_walkthrough").addClass("hide");
        }
    });

    $("#image").attr(images[0]);

});

/** Added by Saurabh on @27072021 
*update login walkthrough flag
*/
$(document).on("click", ".js_skipped_login_walkthrough", function (e) {
    var userid = $(this).attr("data-user-id");
    
    if(userid)
    {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: siteurl + 'cajax/skippedLoginWalkthrough',
            success: function (response) {
                if (response.success) {
                    //window.location.href = redirectUrl;
                    window.location.href = siteurl + "endorse";
                } else {
                    alertbootbox(response.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
});