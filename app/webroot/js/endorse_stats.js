/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var startdatestats = "";
var enddateestats = "";
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
    $("#datepicker_start").datepicker(dateparameters);
    $("#datepicker_end").datepicker(dateparameters);
    $('.knob').focus(function () {
        this.blur();
    });
    $(".knob").knob({
        readOnly: true, //if true This will Set the Knob readonly cannot click
        font : 'Avenir-Light',
        cursor: false,
        draw: function () {
            $(this.i).val(this.cv + '%') //Puts a percent after values
        },
    });
     $(".knob").css("font-size","24px");
});

$(document).on("click", "#showchartendorsment", function () {
    window.location.href = siteurl + 'endorse/charts';
});
$(document).on("click", "#showdatawithoutdatestas", function () {
    $("#datepicker_start").val("");
    $("#datepicker_end").val("");

    startdateendorse = enddateendorse = "";
    curl = siteurl + 'cajax/getstatesearch';
    var formData = {};

    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorse-stats").html("");
            $("#endorse-badges").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div >No Data available</div>").appendTo("#endorse-stats");
            } else {

                $(data_Arr[0]).appendTo("#endorse-stats");
            }

            if ($.trim(data_Arr[1]) != "") {
                //$(" <div >No Data available</div>").appendTo("#endorse-stats");
                $(data_Arr[1]).appendTo("#endorse-badges");
            }

            if ($.trim(data_Arr[2]) != "") {

                //$(" <div >No Data available</div>").appendTo("#endorse-stats");

                $(".knob").val(data_Arr[2]);
                $('.knob').knob();
            }



        },
        error: function (jqXHR, textStatus, errorThrown)
        {

        }
    });
});
$(document).on("click", "#statesearch", function () {

    var start_date = $("#datepicker_start").val();
    var end_date = $("#datepicker_end").val();
    if (start_date == "") {
        alertbootbox("Enter start date");
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
                $("#datepicker_start").val("")
                startdateendorse = "";
                alertbootbox("end date greater than start date");
                return;
            }
            enddateendorse = end_date;
        }

    }
    // endorse date search
    curl = siteurl + 'cajax/getstatesearch';
    var formData = {start_date: start_date, end_date: end_date};

    $.ajax({
        url: curl,
        type: "POST",
        data: formData,
        success: function (data, textStatus, jqXHR)
        {
            var data_Arr = String(data).split('=====');

            $("#endorse-stats").html("");
            $("#endorse-badges").html("");
            if ($.trim(data_Arr[0]) == "") {
                $(" <div >No Data available</div>").appendTo("#endorse-stats");
            } else {

                $(data_Arr[0]).appendTo("#endorse-stats");
            }

            if ($.trim(data_Arr[1]) != "") {
                //$(" <div >No Data available</div>").appendTo("#endorse-stats");
                $(data_Arr[1]).appendTo("#endorse-badges");
            }

            if ($.trim(data_Arr[2]) != "") {

                //$(" <div >No Data available</div>").appendTo("#endorse-stats");

                $(".knob").val(data_Arr[2]);
                $('.knob').knob();
            }

        },
        error: function (jqXHR, textStatus, errorThrown)
        {

        }
    });


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
            } else {
                $("#likes_endorse_" + endorseid).attr("like", 0);
            }

            $("#likes_" + endorseid).html(like_count + " like");
            if (jsonparser["result"]["result"] == true) {
                $("#flashmessage").addClass("alert-success");
            } else {
                $("#flashmessage").addClass("alert-danger");
            }

            $("#flashmessage").html(msg + '<span class="closeflashmsg pull-right">X</span>');
        },
    });

});





