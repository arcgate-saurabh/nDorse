/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var myorg_sel =".myorg";
var btnObj = {
    'confirm': {
        label: 'Yes',
        className: 'btn-danger'
    },
    'cancel': {
        label: 'No',
        className: 'btn-default pull-right'
    },
};
var type = "endorser";
var checkresult = 1;
$(window).scroll(function () {
    var pagename = $("#pagename").val();
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
        if (pagename == "myorg" && checkresult == 1) {
            //=======find the available records
            var pageval = $(myorg_sel+"#orglisting .rec-org .col-md-4").length / 15;
            if (pageval % 1 === 0) {
                $(".hiddenloader").removeClass("hidden");
                delay(function () {
                    $.ajax({
                        type: "POST",
                        url: siteurl + 'cajax/moreorganizationsJoinorg',
                        data: {pageval: pageval, type: type},
                        success: function (data, textStatus, xhr) {
                            if (data.length == 0) {
                                checkresult = 0;
                            }
                            //orglisting
                            $(myorg_sel+"#orglisting").append(data);
                        },
                    });
                }, 500)

            } else {
                $(".hiddenloader").addClass("hidden");
                checkresult = 0;
                console.log("No data available");
            }
        }
    }
});

function removedefaulticon(cb, pid) {
    $(".switchbutton").find(".defaultorg").replaceWith('<button type="button" class="swtichorg btn btn-orange" data-orgid="' + pid + '">Switch Org</button>');
    if ($(".switchbutton").find(".defaultorg").length == 0) {
        clearTimeout(timer);
        cb();
    } else if ($(".switchbutton").find(".defaultorg").length > 0) {
        delay(function () {
            removedefaulticon(cb, pid);
        }, 20)
    }
}

$(document).on("click", ".swtichorg", function () {
    var $this = $(this);
    var orgid = $this.attr("data-orgid");
    var smsg = "Are you sure you want to switch to this organization.";
    bootbox.confirm({
        title: smsg,
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'cajax/switchorg',
                    data: {orgid: orgid},
                    success: function (data, textStatus, xhr) {
                        var currentdefaultorg = $(".switchbutton").find(".defaultorg").attr("rel");
                        var jsonparser = $.parseJSON(data);
                                //return false;
                        if (jsonparser["result"]["status"] == true) {
                            window.location.href=siteurl+"endorse";
                            removedefaulticon(function () {
                                $this.replaceWith('<img class="defaultorg" alt="" rel=' + orgid + ' src="' + siteurl + 'img/selected-org.png" />');
                            }, currentdefaultorg);
                        } else {
                            alertbootbox(jsonparser["result"]["msg"]);
                        }
                    },
                });
            }
        }
    });


})


/***/
$(document).on("click", ".leaveorg", function () {
    var $this = $(this);
    var orgid = $this.attr("data-orgid");
    var smsg = "Are you sure you want to leave this organization.";
    bootbox.confirm({
        title: smsg,
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'cajax/leaveorg',
                    data: {orgid: orgid},
                    success: function (data, textStatus, xhr) {
//                        var currentdefaultorg = $(".switchbutton").find(".defaultorg").attr("rel");
//                        var jsonparser = $.parseJSON(data);
//                                //return false;
//                        if (jsonparser["result"]["status"] == true) {
//                            window.location.href=siteurl+"endorse";
//                            removedefaulticon(function () {
//                                $this.replaceWith('<img class="defaultorg" alt="" rel=' + orgid + ' src="' + siteurl + 'img/selected-org.png" />');
//                            }, currentdefaultorg);
//                        } else {
//                            alertbootbox(jsonparser["result"]["msg"]);
//                        }
                    },
                });
            }
        }
    });


});
/**/

//==================orginfo js

//==============function to set roles in orginfo page
$(document).on("click", "#saveroleinorg", function () {
    if($("#department_id").val() == 0 && $("#job_title_id").val()==0 && $("#entity_id").val() == 0){
        alertbootbox("Select one or more options to update your role.");
        return false;
    }
    var orgid = $(this).attr("data-orgid");
    var rolevalues = {};
    rolevalues["department_id"] = $("#department_id").val();
    rolevalues["job_title_id"] = $("#job_title_id").val();
    rolevalues["entity_id"] = $("#entity_id").val();
    var jsonencodedroles = JSON.stringify(rolevalues);
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/changerolesfororg',
        data: {orgid: orgid, jsonencodedroles: jsonencodedroles},
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            $("#myModalroleinorg").modal("hide");
            alertbootboxcb(jsonparser["result"]["msg"], function () {
                window.location.reload();
            });
        },
    });
});

$(document).on("click", "#resetdates", function () {
    if ($("#datepicker_startdate").val() != "" && $("#datepicker_enddate").val() != "") {
        $("#datepicker_startdate").val("");
        $("#datepicker_enddate").val("");
        $("#daterange").trigger("click");
    } else {
        $("#datepicker_startdate").val("");
        $("#datepicker_enddate").val("");
    }

})


//==================end orginfo js
$(document).ready(function () {
    //=======it stops the description of company after 100 characters
    //$(".comp-discrptn").each(function () {
    //  var alltext = $(this).text()
    //if (alltext.length > 100) {
    //  var newtext = alltext.substring(0, 100);
    //newtext += '...';
    //$(this).text(newtext)
    //}
    //})
})


