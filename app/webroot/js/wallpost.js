/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var endorser_type = "";
var endorser_id = "";
var startdateendorse = "";
var enddateendorse = "";
var dateparameters = {
    showOn: "button",
    buttonImage: siteurl + "img/calendar.png",
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
    var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
    $(".feed-vertical").css("width", widthvertical);
    $("#datepicker_start").datepicker(dateparameters);
    $("#datepicker_end").datepicker(dateparameters);
});

$(document).on("click", "#showdatawithoutdate", function () {
    $("#searchendorsements").attr("type", "text");
    $("#searchendorsements").val("");
    $("#livesearch").html("");
    $("#selectedValues").html("");
    $(".selected-values ").addClass('hidden');
    $("#datepicker_start").val("");
    $("#datepicker_end").val("");
    $("#searchendorsements").val("");
    startdateendorse = enddateendorse = "";
    curl = siteurl + 'cajax/getendorsedatesearch';
    var formData = {page: 1, type: endorsetype};
    endorser_type = "";
    endorser_id = "";

    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorsementlist").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div >No Data available</div>").appendTo("#endorsementlist");
            } else {

                $(data_Arr[0]).appendTo("#endorsementlist");
            }
    var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
            endorsepage = 2;
            totalendorsepage = data_Arr[1];
            console.log(endorsepage);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {

        }
    });
});
$(document).on("click", "#endorsesearch", function () {

    var start_date = $("#datepicker_start").val();
    var end_date = $("#datepicker_end").val();
    if (start_date == "") {
        alertbootbox("Select a Start date");
        return;
    }else if (end_date == "") {
        alertbootbox("Select an End Date");
        return;
    }
    
    else if (start_date != "") {
        var dateobj = start_date.split("-");
        var starty = dateobj[2];
        var startm = dateobj[0];
        var startd = dateobj[1];
        //  alert(start_date);
        var d = new Date(starty, startm, startd);
        starttime = d.getTime();
        startdateendorse = start_date;
        if (end_date != "") {
            var dateobj = end_date.split("-");
            var endy = dateobj[2];
            var endm = dateobj[0];
            var endd = dateobj[1];
            var d = new Date(endy, endm, endd);
            endtime = d.getTime();
            if (starttime > endtime)
            {
                $("#datepicker_start").val("")
                startdateendorse = "";
                alertbootbox("End Date must be greater than the Start Date.");
                return;
            }
            enddateendorse = end_date;
        }

    }
    // endorse date search
    curl = siteurl + 'cajax/getendorsedatesearch';
    var formData = {page: 1, type: endorsetype, start_date: start_date, end_date: end_date};
    if (endorser_type != "" && endorser_id != "")
    {
        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype, start_date: start_date, end_date: end_date};
    }
    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorsementlist").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div class='no-data-nDorse' >No Data available</div>").appendTo("#endorsementlist");
            } else {

                $(data_Arr[0]).appendTo("#endorsementlist");
            }

    var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
            endorsepage = 2;
            totalendorsepage = data_Arr[1];
            console.log(endorsepage);

        },
        error: function (jqXHR, textStatus, errorThrown)
        {

        }
    });


});

$(document).on("click", ".like-img-post", function () {
    var postid= $(this).attr("post");
    var like = $(this).attr("like");

    if (like == 0) {
        like = 1;
    } else {
        like = 0;
    }

    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/likepost',
        data: {postid: postid, like: like},
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            var msg = jsonparser["result"]["msg"];
            var like_count = jsonparser["result"]["data"]["like_count"];

            if (like == 1) {
                $("#likes_endorse_" + postid).attr("like", 1);
                $("#likes_" + postid).attr("like", 1);
                $("#likes_endorse_" + postid).attr("src", siteurl + "img/liked.png");
            } else {
                $("#likes_endorse_" + postid).attr("like", 0);
                $("#likes_" + postid).attr("like", 0);
                $("#likes_endorse_" + postid).attr("src", siteurl + "img/like.png");
            }

            $("#likes_" + postid).html(like_count + " Like");
            if (jsonparser["result"]["result"] == true) {
                $("#flashmessage").addClass("alert-success");
            } else {
                $("#flashmessage").addClass("alert-danger");
            }

            $("#flashmessage").html(msg + '<span class="closeflashmsg pull-right">X</span>');
        },
    });

});

$(document).on("click", ".delete-img-post", function () {
    var postid= $(this).attr("post");
    //console.log(postid); return false;
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/deletepost',
        data: {postid: postid},
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            var msg = jsonparser["result"]["msg"];
            if (jsonparser["result"]["result"] == true) {
                $("#flashmessage").addClass("alert-success");
            } else {
                $("#flashmessage").addClass("alert-danger");
            }
            $("#flashmessage").html(msg + '<span class="closeflashmsg pull-right">X</span>');
        },
    });

});

var jscall = false;
$(window).scroll(function () {

    //  if($(window).scrollTop() + $(window).height() == $(document).height()) {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        console.log("bottom!");
        console.log(endorsepage + " " + totalendorsepage);
        if (endorsepage <= totalendorsepage) {
            curl = siteurl + 'cajax/getendorsedatesearch';
            var formData = {page: endorsepage, type: endorsetype};
            if (endorsetype != "public") {

                if (startdateendorse != "" && enddateendorse != "") {
                    formData = {page: endorsepage, type: endorsetype, start_date: startdateendorse, end_date: enddateendorse};
                } else if (startdateendorse != "") {
                    formData = {page: endorsepage, type: endorsetype, start_date: startdateendorse};
                }


            }

            if (endorser_type != "" && endorser_id != "")
            {

                formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype};
                if (endorsetype != "public") {

                    if (startdateendorse != "" && enddateendorse != "") {
                        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype, start_date: startdateendorse, end_date: enddateendorse};
                    } else if (startdateendorse != "") {
                        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype, start_date: startdateendorse};
                    }


                }
            }
            if (jscall == false) {
                jscall = true;
                $.ajax({
                    url: curl,
                    type: "POST",
                    data: formData,
                    beforeSend: function(){
             $(".hiddenloader").removeClass("hidden");
        },
                    success: function (data, textStatus, jqXHR)
                    {
$(".hiddenloader").addClass("hidden");
                        var data_Arr = String(data).split('=====');
                        $(data_Arr[0]).appendTo("#endorsementlist");
                        
                        var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
                        endorsepage = endorsepage + 1;
                        console.log(endorsepage);
                        jscall = false;
                        parseData = "";
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {

                    }
                });
            }
        }
    }
});

$(document).on("click", ".closeflashmsg", function () {
    var presentclass = $(this).parent().attr("class").split(" ")[1];
    $("#flashmessage").html("");
    $("#flashmessage").removeClass(presentclass);
})
$(document).on("keyup", "#searchendorsements", function () {
      var page_ndorse_url = window.location.href;
      console.log(page_ndorse_url);
       //search_self =true;
   
    if ($(this).val().length >= 3) {
        $("#livesearch").html("");
        var keyword = $(this).val();
           var formdata={keyword: keyword,search_self:true};
                         if (page_ndorse_url.search("endorse/ndorse") > 0 ) {
                            formdata={keyword: keyword};
                         }
        delay(function () {
        $.ajax({
            type: "POST",
            async:true,
            url: siteurl + 'cajax/endorsesearch',
            data: formdata,
            success: function (data, textStatus, xhr) {
                var jsonparser = $.parseJSON(data);
                var msg = jsonparser["result"]["msg"];
                var data = jsonparser["result"]["data"];
                var allobjects = {};
                allobjects["entity"] = {};
                allobjects["department"] = {};
                allobjects["user"] = {};
                var userobj = data.users;
                var departobj = data.departments;
                var entityobj = data.entities;
                var resultcounter = 0;
                if (departobj) {
                    for (tmpd in departobj) {
                        $("#livesearch").append("<div class='livesearchdata' data-endorsementid='" + departobj[tmpd].id + "' data-endorsementfor='department'>" + departobj[tmpd].name + "</div>");
                        resultcounter++;
                    }
                }
                if (entityobj) {
                    for (tmpe in entityobj) {
                        $("#livesearch").append("<div class='livesearchdata' data-endorsementid='" + entityobj[tmpe].id + "' data-endorsementfor='department'>" + entityobj[tmpe].name + "</div>");

                    }
                }
                if (userobj) {
                    for (tmpu in userobj) {
                        //alert(userobj[tmpu].name);
                        $("#livesearch").append("<div class='livesearchdata' data-endorsementid='" + userobj[tmpu].id + "' data-endorsementfor='user'>" + userobj[tmpu].name + "</div>");
                        resultcounter++;

                    }
                }

                if (resultcounter == 0) {

                }
            },
        });
        }, 1000);
    } else if ($(this).val().length == 0) {
        $("#livesearch").html("");
        if (endorser_type != "" && endorser_id != "")
        {
            endorser_type = "";
            endorser_id = "";
            var formData = {page: 1, type: endorsetype};
            if (endorsetype != "public") {

                if (startdateendorse != "" && enddateendorse != "") {
                    formData = {page: 1, endorser_type: endorser_type, start_date: startdateendorse, end_date: enddateendorse};
                } else if (startdateendorse != "") {
                    formData = {page: 1, endorser_type: endorser_type, start_date: startdateendorse};
                }


            }
            $.ajax({
                url: siteurl + 'cajax/getendorsedatesearch',
                type: "POST",
                data: formData,
                success: function (data, textStatus, jqXHR)
                {
                    var data_Arr = String(data).split('=====');
                    $("#endorsementlist").html("");
                    $(data_Arr[0]).appendTo("#endorsementlist");
                        var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);

                    endorsepage = 2;
                    totalendorsepage = data_Arr[1];
                    console.log(totalendorsepage);
                    console.log(endorsepage);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {

                }
            });
        }

    }
});
//
$(document).on("click", ".endorse-user", function (event) {
    if ($(this).attr("endorse_type") == "user") {
        window.location.href = siteurl + "client/profile/" + $(this).attr("user_id");
    }


});
//
$(document).on("click", ".live-feeds-ndorse", function (event) {

    console.log($(this).attr("post_id"));
    var clnew = $(event.target).attr('class');

    if (clnew == undefined) {
        window.location.href = siteurl + "post/details/" + $(this).attr("post_id");
    } else if (clnew.search("like-img-endorse") < 0 && clnew.search("endorse-user") < 0) {
        window.location.href = siteurl + "post/details/" + $(this).attr("post_id");
    }
    //like-img-endorse
});
//$("body").click(function (event) {
//        var clnew = $(event.target).attr('id');
//        //=======to get a click outside this modal
//        if (clnew == "myModalbulkusersimports") {
//            window.location.reload();
//        }
//    });
$(document).on("click", ".js_clearAll_endorse", function () {
    $("#searchendorsements").attr("type", "text");
    $("#searchendorsements").val("");
    $("#livesearch").html("");
    $("#selectedValues").html("");
    $(".selected-values ").addClass('hidden');
    if (endorser_type != "" && endorser_id != "")
    {
        endorser_type = "";
        endorser_id = "";
        var formData = {page: 1, type: endorsetype};
        if (endorsetype != "public") {

            if (startdateendorse != "" && enddateendorse != "") {
                formData = {page: 1, endorser_type: endorser_type, start_date: startdateendorse, end_date: enddateendorse};
            } else if (startdateendorse != "") {
                formData = {page: 1, endorser_type: endorser_type, start_date: startdateendorse};
            }


        }
        $.ajax({
            url: siteurl + 'cajax/getendorsedatesearch',
            type: "POST",
            data: formData,
            success: function (data, textStatus, jqXHR)
            {
                var data_Arr = String(data).split('=====');
                 $("#endorsementlist").html("");
                $(data_Arr[0]).appendTo("#endorsementlist");

                endorsepage = 2;
                totalendorsepage = data_Arr[1];
                  var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
                console.log(totalendorsepage);
                console.log(endorsepage);

            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }
});

$(document).on("click", ".livesearchdata", function () {
    $("#searchendorsements").val($(this).text());
    $("#searchendorsements").attr("type", "hidden");
    $("#selectedValues").html($(this).text());
    $(".selected-values ").removeClass('hidden');
    $("#livesearch").html("");
    // div class="livesearchdata" data-endorsementfor="user" data-endorsementid="308
    endorser_type = $(this).attr("data-endorsementfor");
    endorser_id = $(this).attr("data-endorsementid");
    endorsepage = 1;
    var formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype};
    if (endorsetype != "public") {

        if (startdateendorse != "" && enddateendorse != "") {
            formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype, start_date: startdateendorse, end_date: enddateendorse};
        } else if (startdateendorse != "") {
            formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype, start_date: startdateendorse};
        }


    }
    if (jscall == false) {
        jscall = true;
        
        $.ajax({
            url: siteurl + 'cajax/getendorsedatesearch',
            type: "POST",
            data: formData,
            success: function (data, textStatus, jqXHR)
            {

                var data_Arr = String(data).split('=====');

                $("#endorsementlist").html("");
                $(".hiddenloader").removeClass("hidden");
                $(data_Arr[0]).appendTo("#endorsementlist");

                endorsepage = endorsepage + 1;
                totalendorsepage = data_Arr[1];
                console.log(endorsepage);
                $(".hiddenloader").addClass("hidden");
                  var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
                jscall = false;
                parseData = "";
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }

});




