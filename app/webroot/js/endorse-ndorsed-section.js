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
    $("#datepicker_start_1").datepicker(dateparameters);
    $("#datepicker_end_1").datepicker(dateparameters);
});

$(document).on("click", "#showdatawithoutdate1", function () {
    $("#searchendorsements").attr("type", "text");
    $("#searchendorsements").val("");
    $("#livesearch").html("");
    $("#selectedValues").html("");
    $(".selected-values ").addClass('hidden');
    $("#datepicker_start_1").val("");
    $("#datepicker_end_1").val("");
    $("#searchendorsements").val("");
    startdateendorse = enddateendorse = "";
    curl = siteurl + 'cajax/getendorsedatesearch';
    var formData = {page: 1, type: endorsetype1};
    endorser_type = "";
    endorser_id = "";

    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorsementlist1").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div >No Data available</div>").appendTo("#endorsementlist1");
            } else {

                $(data_Arr[0]).appendTo("#endorsementlist1");
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

$(document).on("click", "#endorsesearchsection1", function () {

    var start_date = $("#datepicker_start_1").val();
    var end_date = $("#datepicker_end_1").val();
    if (start_date == "") {
        alertbootbox("Select a Start date");
        return;
    } else if (end_date == "") {
        alertbootbox("Select an End Date");
        return;
    } else if (start_date != "") {
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
                $("#datepicker_start_1").val("");
                startdateendorse = "";
                alertbootbox("End Date must be greater than the Start Date.");
                return;
            }
            enddateendorse = end_date;
        }

    }
    // endorse date search
    curl = siteurl + 'cajax/getendorsedatesearch';
    var formData = {page: 1, type: endorsetype1, start_date: start_date, end_date: end_date};
    if (endorser_type != "" && endorser_id != "")
    {
        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, start_date: start_date, end_date: end_date};
    }
//    console.log(formData);
    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorsementlist1").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div class='no-data-nDorse' >No Data available</div>").appendTo("#endorsementlist1");
            } else {

                $(data_Arr[0]).appendTo("#endorsementlist1");
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
    var postid = $(this).attr("post");
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
                console.log("Remove popup");
                $("#likes_endorse_" + postid).attr("like", 1);
                $("#likes_" + postid).attr("like", 1);
                $("#likes_endorse_" + postid).attr("src", siteurl + "img/liked.png");
                $(document).find(".show-me-popup-new_" + postid).click();
            } else {
                console.log("Add popup");
                $("#likes_endorse_" + postid).attr("like", 0);
                $("#likes_" + postid).attr("like", 0);
                $("#likes_endorse_" + postid).attr("src", siteurl + "img/like.png");
            }

            var likeCaption = " Like";
            if (like_count > 1)
                likeCaption = " Likes";

            $("#likes_" + postid).html(like_count + likeCaption);
            if (jsonparser["result"]["result"] == true) {
                $("#flashmessage").addClass("alert-success");
            } else {
                $("#flashmessage").addClass("alert-danger");
            }

            $("#flashmessage").html(msg + '<span class="closeflashmsg pull-right">X</span>');
        },
    });

});

$(document).on("click", ".endorselikeslist", function () {
    console.log("likeslist");
    var endorseid = $(this).attr("endorse");
    edit_rec(endorseid, 'endorse', 1, 'endorse/likeslist');
});

$(document).on("click", ".postlikeslist", function () {
    console.log("likeslist");
    var endorseid = $(this).attr("post");
    edit_rec(endorseid, 'post', 1, 'endorse/likeslist');
});

function edit_rec(id, type, pg, url) {
    //console.log(siteurl);
    var ac = new ajaxCall(siteurl + url, 'setNShowStatus');
    var param = new Object();
    param.id = id;
    param.pg = pg;
    param.type = type;
    ac.params = param;
    ac.call();
}

function ajaxCall(url, callback) {
    this.url = url;
    this.params = new Object();
    this.callback = callback;
}

ajaxCall.prototype.call = function () {
    $("#overlay").show();
    var that = this;
    $.ajax({
        type: "POST",
        url: this.url,
        data: this.params,
        dataType: "html",
        success: function (data, textStatus, jqXHR) {
            $("#overlay").hide();
            that.data = data;
            var method = that.callback;
            eval(method + '(data)');
        },
        complete: function () {

        },
        error: function (e) {
            console.log('error');
            console.log(e);
        }
    });
}
//show fields value in the popup form
function setNShowStatus(data) {
    $('.likesmodel').html(data);
    $('.likesmodel').modal('show')
}
$(document).on("click", '.closeModal', function () {
    $(".likesmodel").modal('hide');
    $(".likesmodel").modal('hide');
});
$(document).on("click", ".like-img-endorse", function () {
    var endorseid = $(this).attr("endorse");
    var like = $(this).attr("like");

    if (like == 0) {
        like = 1;
    } else {
        like = 0;
    }

    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/likeendorse',
        data: {endorseid: endorseid, like: like},
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            var msg = jsonparser["result"]["msg"];
            var like_count = jsonparser["result"]["data"]["like_count"];

            if (like == 1) {
                $("#likes_endorse_" + endorseid).attr("like", 1);
                $("#likes_" + endorseid).attr("like", 1);
                $("#likes_endorse_" + endorseid).attr("src", siteurl + "img/liked.png");
            } else {
                $("#likes_endorse_" + endorseid).attr("like", 0);
                $("#likes_" + endorseid).attr("like", 0);
                $("#likes_endorse_" + endorseid).attr("src", siteurl + "img/like.png");
            }

            $("#likes_" + endorseid).html(like_count + " Like");
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
        console.log("POST");
        console.log("bottom!");
        console.log(endorsepage + " " + totalendorsepage);
        if (endorsepage <= totalendorsepage) {
            curl = siteurl + 'cajax/getendorsedatesearch';
            var formData = {page: endorsepage, type: endorsetype1};
            if (endorsetype1 != "public") {

                if (startdateendorse != "" && enddateendorse != "") {
                    formData = {page: endorsepage, type: endorsetype1, start_date: startdateendorse, end_date: enddateendorse};
                } else if (startdateendorse != "") {
                    formData = {page: endorsepage, type: endorsetype1, start_date: startdateendorse};
                }


            }

            if (endorser_type != "" && endorser_id != "")
            {

                formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1};
                if (endorsetype1 != "public") {

                    if (startdateendorse != "" && enddateendorse != "") {
                        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, start_date: startdateendorse, end_date: enddateendorse};
                    } else if (startdateendorse != "") {
                        formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, start_date: startdateendorse};
                    }


                }
            }
            if (jscall == false) {
                jscall = true;
                $.ajax({
                    url: curl,
                    type: "POST",
                    data: formData,
                    beforeSend: function () {
                        $(".hiddenloader").removeClass("hidden");
                    },
                    success: function (data, textStatus, jqXHR)
                    {
                        $(".hiddenloader").addClass("hidden");
                        var data_Arr = String(data).split('=====');
                        $(data_Arr[0]).appendTo("#endorsementlist1");

                        var widthvertical = $(".feed-vertical").parent(".col-md-8").css("width");
                        $(".feed-vertical").css("width", widthvertical);
                        endorsepage = endorsepage + 1;
                        console.log("NEW PAGE NUMBER : " + endorsepage);
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
    loadscript();
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
    var feedType = $("input[name=feedtype]:checked").val();
    //var formdata = {keyword: keyword, feed_type: feedType}; 
    if ($(this).val().length >= 3) {
        $("#livesearch").html("");
        var keyword = $(this).val();
        var formdata = {keyword: keyword, search_self: true, feed_type: feedType};
        if (page_ndorse_url.search("endorse/ndorse") > 0) {
            formdata = {keyword: keyword};
        }
        console.log(formdata);
        console.log("siteurl : " + siteurl);

        delay(function () {
            $.ajax({
                type: "POST",
                async: true,
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
                    var postTitleObj = data.post_title;
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
                    if (postTitleObj) {
                        for (tmpu in postTitleObj) {
                            //alert(userobj[tmpu].name);
                            $("#livesearch").append("<div class='livesearchdata' data-endorsementid='" + postTitleObj[tmpu].id + "' data-endorsementfor='post_title'>" + postTitleObj[tmpu].title + "</div>");
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
            var formData = {page: 1, type: endorsetype1, feed_type: feedType};
            if (endorsetype1 != "public") {

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


$(document).on("change", "#subcenter", function () {
    var page_ndorse_url = window.location.href;
    var subcenterID = $(this).val();
    var feedType = $("input[name=feedtype]:checked").val();
    console.log(subcenterID);
    var searchKeyword = $("#searchendorsements").val();

    var subcenter_id = "";
    if (subcenterID != 0) {
        subcenter_id = subcenterID;
    }

    var endorser_type = $('#searchendorsements').attr("data-endorsementfor");
    var endorser_id = $('#searchendorsements').attr("data-endorsementid");

    if (endorser_id != '') {

        var formData = {page: 1, type: endorsetype1, feed_type: feedType, subcenter_id: subcenter_id, endorser_type: endorser_type, endorser_id: endorser_id};
    } else {
        var formData = {page: 1, type: endorsetype1, feed_type: feedType, subcenter_id: subcenter_id};
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


    $("#livesearch").html("");



});
//
$(document).on("click", ".endorse-user", function (event) {
    if ($(this).attr("endorse_type") == "user") {
        window.location.href = siteurl + "client/profile/" + $(this).attr("user_id");
    }


});
//
$(document).on("click", ".live-feeds-ndorse", function (event) {

    console.log($(this).attr("endorse_id"));
    var clnew = $(event.target).attr('class');

    if (clnew == undefined) {
        window.location.href = siteurl + "endorse/details/" + $(this).attr("endorse_id");
    } else if (clnew.search("like-img-endorse") < 0 && clnew.search("endorse-user") < 0) {
        window.location.href = siteurl + "endorse/details/" + $(this).attr("endorse_id");
    }
    //like-img-endorse
});

$(document).on("click", ".live-feeds-post", function (event) {
    console.log($(this).attr("post_id"));
    var clnew = $(event.target).attr('class');
    if (clnew == undefined) {
        window.location.href = siteurl + "post/details/" + $(this).attr("post_id");
    } else if (clnew.search("like-img-post") < 0 && clnew.search("endorse-user") < 0) {
        window.location.href = siteurl + "post/details/" + $(this).attr("post_id");
    }
    //like-img-endorse
});

$(document).on("click", ".post-attachment-pin", function (event) {
    var postId = $(this).attr("post_id");
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/increasePostAttachmentPinClickCount',
        data: {post_id: postId},
        success: function (data, textStatus, xhr) {
            console.log(data);
            var jsonparser = $.parseJSON(data);
            var status = jsonparser["result"]["status"];
            if (status) {
                window.location.href = siteurl + "post/details/" + postId;
            }
        },
    });
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
    $("#searchendorsements").attr("data-endorsementfor", '');
    $("#searchendorsements").attr("data-endorsementid", '');
    var subCenterId = $("#subcenter").val();
    var subcenter_id = "";
    if (subCenterId != 0) {
        subcenter_id = subCenterId;
    }


    var feedType = $("input[name=feedtype]:checked").val();
    if (endorser_type != "" && endorser_id != "")
    {
        endorser_type = "";
        endorser_id = "";
        var formData = {page: 1, type: endorsetype1, feed_type: feedType, subcenter_id: subcenter_id};
        if (endorsetype1 != "public") {

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

    var subcenterId = $("#subcenter").val();
    console.log("subcenterId  : " + subcenterId);
    var subcenter_id = 0;
    if (subcenterId != 0) {
        subcenter_id = subcenterId;
    }
    var feedType = $("input[name=feedtype]:checked").val();
    // div class="livesearchdata" data-endorsementfor="user" data-endorsementid="308
    endorser_type = $(this).attr("data-endorsementfor");
    endorser_id = $(this).attr("data-endorsementid");
    $("#searchendorsements").attr("data-endorsementfor", endorser_type);
    $("#searchendorsements").attr("data-endorsementid", endorser_id);

    endorsepage = 1;
    var formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, feed_type: feedType, subcenter_id: subcenter_id};
    if (endorsetype1 != "public") {

        if (startdateendorse != "" && enddateendorse != "") {
            formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, start_date: startdateendorse, end_date: enddateendorse};
        } else if (startdateendorse != "") {
            formData = {page: endorsepage, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, start_date: startdateendorse};
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

/** Added by Babulal Prasad @03022017 
 * Delete post from feeds
 */
$(document).on("click", ".delete-post-from-feed", function (e) {
    var postid = $(this).attr("data-post-id");
    var post_block_div_id = "feed_" + postid;
    console.log(postid);
    $.confirm({
        title: false,
        content: 'Deleted Post will no longer be visible on the Live Feed.',
        type: 'red',
        columnClass: 'medium',
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: 'Confirm',
                btnClass: 'btn-red',
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: siteurl + 'cajax/deletepost',
                        data: {postid: postid},
                        success: function (data, textStatus, xhr) {
                            var jsonparser = $.parseJSON(data);
                            var status = jsonparser["result"]["status"];
                            if (status) {
                                $("#" + post_block_div_id).fadeOut('slow');
                            }
                        },
                    });
                }
            },
            cancel: function () {
            }
        }
    });
});


/** Added by Babulal Prasad @01112017 
 * Delete post from feeds
 */
$(document).on("click", ".delete-endorse-from-feed", function (e) {
    var endorseid = $(this).attr("data-endorse-id");
    var post_block_div_id = "live_feed_" + endorseid;
    $.confirm({
        title: false,
        content: 'Deleted nDorsement will no longer be visible on the Live Feed.',
        type: 'red',
        columnClass: 'medium',
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: 'Confirm',
                btnClass: 'btn-red',
                action: function () {
                    $.ajax({
                        type: "POST",
                        url: siteurl + 'cajax/deletendorsement',
                        data: {endorseid: endorseid},
                        success: function (data, textStatus, xhr) {
                            var jsonparser = $.parseJSON(data);
                            var status = jsonparser["result"]["status"];
                            if (status) {
                                $("#" + post_block_div_id).remove();
                                //window.location.href = siteurl + "endorse";
                            }
                        },
                    });
                }
            },
            cancel: function () {
            }
        }
    });
});
/** Added by Babulal Prasad @03022017 
 * Enable do not remind for this user
 */
$(document).on("click", ".enable-do-not-remind-feed", function () {
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/setDoNotRemind',
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            var status = jsonparser["result"]["status"];
            if (status) {
                $(document).find('.show-popup-flag').attr('data-toggle', '').attr('data-target', '');
            }
        },
    });

});

$(document).on("mouseup", ".orgfilterradio", function () {
    delay(function () {
        filterorganizationlist()
    }, 200);
});

function filterorganizationlist() {
    var keyword = $('#searchendorsements').val();
    var feedType = $("input[name=feedtype]:checked").val();
    endorser_type = $("#searchendorsements").attr("data-endorsementfor");
    endorser_id = $("#searchendorsements").attr("data-endorsementid");
    $(".search-icn").addClass("search-loader").removeClass('search-icn');

    var subcenterID = $("#subcenter").val();
    var subcenter_id = "";
    if (subcenterID != 0) {
        subcenter_id = subcenterID;
    }

    var formdata = {keyword: keyword, feed_type: feedType, endorser_type: endorser_type, endorser_id: endorser_id, type: endorsetype1, subcenter_id: subcenter_id};
    
    delay(function () {
        $.ajax({
            url: siteurl + 'cajax/getendorsedatesearch',
            type: "POST",
            data: formdata,
            success: function (data, textStatus, jqXHR)
            {
                $(".search-loader").removeClass("search-loader").addClass('search-icn');
                var data_Arr = String(data).split('=====');
                $("#endorsementlist").html("");
                $(".hiddenloader").addClass("hidden");
                $("#endorsementlist").html("");
                if ($.trim(data_Arr[0]) == "") {
                    $(" <div class='no-data-nDorse'>No Data available</div>").appendTo("#endorsementlist");
                } else {
                    $(data_Arr[0]).appendTo("#endorsementlist");
                }
                endorsepage = endorsepage + 1;
                totalendorsepage = data_Arr[1];
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
    }, 1000);



}




