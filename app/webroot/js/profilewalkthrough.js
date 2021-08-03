/* 
 * First time login walkthrough
 */

$(document).ready(function() {
    $("#finish").addClass("hide");
     var images = [
       siteurl + "img/profile-screen-1.jpg",
       siteurl + "img/profile-screen-2.jpg",
       siteurl + "img/profile-screen-3.jpg"
       
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
            $("#profile_login_walkthrough").addClass("hide");
        }
    });

    $("#image").attr(images[0]);

});

/** Added by Saurabh on @27072021 
*update login walkthrough flag
*/
$(document).on("click", ".js_profile_login_walkthrough", function (e) {
    var userid = $(this).attr("data-user-id");
    
    if(userid)
    {
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: siteurl + 'cajax/profileLoginWalkthrough',
            success: function (response) {
                if (response.success) {
                    //window.location.href = redirectUrl;
                    window.location.href = siteurl + "client/profile";
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