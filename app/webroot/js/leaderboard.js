/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var seconddata = "";
$(document).ready(function () {
    var firstdata = $(".leaderboardendorsedtable").html();
    var activeTab = null;
    $('a[data-toggle="tab"]').on('click', function (e) {
        $("#datepicker_startdate").val("");
        $("#datepicker_enddate").val("");
        activeTab = $(e.target).attr("id");
        $("#setleaderboardendorseddata").attr("data-type", activeTab);
        if (activeTab == "endorser") {
            if (seconddata.length > 0) {
                $(".leaderboardendorsedtable").html("");
                $(".leaderboardendorsedtable").html(seconddata);
            } else {
                var type = activeTab;
                $.ajax({
                    type: "POST",
                    url: siteurl + 'cajax/getdataleaderboard',
                    data: {type: type},
                    success: function (data, textStatus, xhr) {
                        seconddata = data;
                        console.log(data);
                        $(".leaderboardendorsedtable").html("");
                        if (data.length > 0) {
                            $(".leaderboardendorsedtable").html(data);
                        } else {
                            $(".leaderboardendorsedtable").html('<tbody><tr class="table-head"><td class="nodataavailable">No Data Available</td></tr></tbody>');
                        }
                    },
                });
            }

        } else if (activeTab == "endorsed") {
            $(".leaderboardendorsedtable").html("");
            $(".leaderboardendorsedtable").html(firstdata);
        }
    })


    //====initial data set to variable

    //=clearing values for dates initially
    $("#datepicker_startdate").val("");
    $("#datepicker_enddate").val("");

    //=======setting leaderboard data
    $("#setleaderboardendorseddata").click(function () {
        //
            var start_date = $("#datepicker_startdate").val();
    var end_date = $("#datepicker_enddate").val();
    var type = $(this).attr("data-type");
    if(start_date==""){
         alertbootbox("Select a start date");
         return;
    }else if(start_date!=""){
        var dateobj = start_date.split("-");
        var starty = dateobj[2];
        var startm = dateobj[0];
        var startd = dateobj[1];
      //  alert(start_date);
        var d = new Date(starty,startm,startd);
        starttime = d.getTime();
        startdateendorse = start_date;
        if(end_date!=""){
          var dateobj = end_date.split("-");
          var endy = dateobj[2];
          var endm = dateobj[0];
          var endd = dateobj[1];
          var d = new Date(endy,endm,endd);
          endtime = d.getTime();
          if(starttime>endtime)
          {
            $("#datepicker_start").val("")
            startdateendorse ="";
            alertbootbox("End Date must be greater then the Strat Date");
            return;
          }
          
        }
        
    }
    if(end_date==""){
         var formdata={type: type, startdate: start_date,enddate:""};
    }else{
      var formdata={type: type, startdate: start_date,enddate:end_date};  
    }
   
     $.ajax({
                type: "POST",
                url: siteurl + 'cajax/getdataleaderboard',
                data: formdata,
                success: function (data, textStatus, xhr) {
                    $(".leaderboardendorsedtable").html("");
                    if (data.length > 0) {
                        $(".leaderboardendorsedtable").html(data);
                    } else {
                        $(".leaderboardendorsedtable").html('<tbody><tr class="table-head"><td class="nodataavailable">No Data Available</td></tr></tbody>');
                    }
                },
            });
        //
        //var startdate = $("#datepicker_startdate").val();
        //var enddate = $("#datepicker_enddate").val();
        //var type = $(this).attr("data-type");
        //if (enddate.length == 0 || startdate.length == 0) {
        //    alertbootbox("Both dates need to be filled.");
        //} else {
        //    $.ajax({
        //        type: "POST",
        //        url: siteurl + 'cajax/getdataleaderboard',
        //        data: {type: type, startdate: startdate, enddate: enddate},
        //        success: function (data, textStatus, xhr) {
        //            $(".leaderboardendorsedtable").html("");
        //            if (data.length > 0) {
        //                $(".leaderboardendorsedtable").html(data);
        //            } else {
        //                $(".leaderboardendorsedtable").html('<tbody><tr class="table-head"><td class="nodataavailable">No Data Available</td></tr></tbody>');
        //            }
        //        },
        //    });
        //}

    })
    //=======end setting leaderboard data

    $("#resetdates").click(function () {
        var getdatatype = $("#setleaderboardendorseddata").attr("data-type");
        $("#datepicker_startdate").val("");
        $("#datepicker_enddate").val("");
        $(".leaderboardendorsedtable").html("");
        if (getdatatype == "endorsed") {
            $(".leaderboardendorsedtable").html(firstdata);
        } else if (getdatatype == "endorser") {
            $(".leaderboardendorsedtable").html(seconddata);
        }

    });
});

