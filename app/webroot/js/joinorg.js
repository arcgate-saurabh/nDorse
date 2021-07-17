/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var joinorg_type = "public";
var joinorg_sel = ".join-org-grp"; var sel_offset = " ";
//joinorg_sel = "", sel_offset = "";
function searchallorganization() {
    $.ajax({
        url: siteurl + 'cajax/showallorg',
        type: "POST",
        data: {type: joinorg_type},
        success: function (data, textStatus, xhr) {
            $(joinorg_sel+"#orglisting").html("");
            $(joinorg_sel+"#orglisting").append(data);
        }
    });
}


$(document).on("click", "#joinorganization", function () {
    var secretcode = $("#OrganizationSecretcode").val();
    if (secretcode == "") {
        alertbootbox("Enter a uniqe code to join an Organization.");
    } else {
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/joinanorganization',
            data: {secretcode: secretcode},
            success: function (data, textStatus, xhr) {
                var jsonparser = $.parseJSON(data);
                var msg = jsonparser["result"]["msg"];
                alertbootboxcb(msg, function () {
                    if (jsonparser["result"]['isDefault']) {
                        window.location.href = siteurl + "endorse";
                    } else {
                        window.location.reload();
                    }

                });
//                if (jsonparser["result"]["status"] == true) {
//                    $("#flashmessage").addClass("alert-success");
//                } else {
//                    $("#flashmessage").addClass("alert-danger");
//                }
//                
                //$("#flashmessage").html(msg + '<span class="closeflashmsg pull-right">X</span>');
            },
        });
    }
});
//=================flash msg close as per it comes 
$(document).on("click", ".closeflashmsg", function () {
    var presentclass = $(this).parent().attr("class").split(" ")[1];
    $("#flashmessage").html("");
    $("#flashmessage").removeClass(presentclass);
})

//===========to join the request for any org
$(document).on("click", joinorg_sel+sel_offset+"#joinrequestorg", function () {
    var elem = $(this);
    var orgid = elem.attr("data-id");
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/joinrequestorg',
        data: {orgid: orgid},
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            if (jsonparser["result"]["status"] == true) {
                alertbootbox("Request Sent Successfully");
                elem.attr("disabled", "disabled");
                elem.text("REQUEST SENT");
            }
        },
    });
})

//=====search organization
$(document).on("keyup", joinorg_sel+sel_offset+"#searchorganization", function () {
    var searchvalue = $(this).val();
    var lengthinputvalue = searchvalue.length;
    $("#livesearch").html("");
    if (lengthinputvalue >= 2) {
        $("#clearsearcheddata").removeAttr("disabled");
        delay(function () {
            $.ajax({
                url: siteurl + 'cajax/searchorg',
                type: "POST",
                data: {searchvalue: searchvalue},
                success: function (data, textStatus, xhr) {
                    var jsonparser = $.parseJSON(data);
                    if (jsonparser["result"]["status"] == true) {
                        var resultdata = jsonparser["result"]["data"];
                        var orgdetail = resultdata["organization"];
                        console.log(orgdetail)
                        if (typeof (orgdetail[0]) != "undefined") {
                            for (tmp in orgdetail) {
                                $("#livesearch").append('<div class="livesearchdata" data-orgid="' + orgdetail[tmp]["id"] + '">' + orgdetail[tmp]["name"] + '</div>');
                            }
                        } else {
                            $("#livesearch").append('<div class="nodata">No Result Found</div>');
                        }

                    }
                }
            });
        }, 1000)

    } else if (lengthinputvalue == 0) {
        clearTimeout(timer);
        $("#clearsearcheddata").attr("disabled", "disabled");
        delay(function () {
            searchallorganization();
        }, 1000)
    } else {
        clearTimeout(timer);
    }
})

//===============clicking live searcheddata an find org
$(document).on("click", ".livesearchdata", function () {
    var orgid = $(this).attr("data-orgid");
    $("#searchorganization").val($(this).text());
    $.ajax({
        url: siteurl + 'cajax/livesearcheddata',
        type: "POST",
        data: {orgid: orgid, type: joinorg_type},
        success: function (data, textStatus, xhr) {
            $("#livesearch").html("");
            $(joinorg_sel+"#orglisting").html("");
            $(joinorg_sel+"#orglisting").append(data);
        }
    });
})

//=clear data when clicked
$(document).on("click", joinorg_sel+sel_offset+"#clearsearcheddata", function () {
    $(joinorg_sel+sel_offset+"#clearsearcheddata").attr("disabled", "disabled");
    $("#livesearch").html("");
    $(joinorg_sel+sel_offset+"#searchorganization").val("");
    searchallorganization();
});

//==========scrolling function and load new data
var checkresult = 1;
$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
        var pagename = $("#pagename").val();
        if (pagename == "joinorg" && checkresult == 1) {
            //=======find the available records
            var pageval = $(".container .rec-org .col-md-4").length / 15;
            if (pageval % 1 === 0) {
                $(".hiddenloader").removeClass("hidden");
                delay(function () {
                    $.ajax({
                        type: "POST",
                        url: siteurl + 'cajax/moreorganizationsJoinorg',
                        data: {pageval: pageval, type: joinorg_type},
                        success: function (data, textStatus, xhr) {
                            if (data.length == 0) {
                                checkresult = 0;
                            }
                            //orglisting
                            $(joinorg_sel+"#orglisting").append(data);
                        },
                    });
                }, 500)

            } else {
                checkresult = 0;
                $(".hiddenloader").addClass("hidden");
                console.log("No data available");
            }
        }
    }
});


$(document).on("click", "body", function (event) {
    //=to close search org when externally clicked
    clnew = $(event.target).attr("class");
    var searcheddata = $("#livesearch").html();
    if (searcheddata.length > 0 && clnew != "livesearchdata") {
        $("#livesearch").html("");
        $(joinorg_sel+sel_offset+"#searchorganization").val("");
        $(joinorg_sel+sel_offset+"#clearsearcheddata").attr("disabled", "disabled");

    }
})


/* 
 * Backup @19Sept2017 commented by Babulal Prasad 
 * 
 $(document).on("click", "#sendmultiplerequest", function () {
 var orgids = [];
 
 $("#orglisting .col-md-4 .rec-comp").each(function () {
 var elem = $(this);
 if (elem.children(".switchbutton").is(":visible") == true) {
 var elementbutton = elem.children("#joinrequestorg");
 var idtofetch = elementbutton.attr("data-id");
 //console.log(idtofetch);
 $.ajax({
 type: "POST",
 async: false,
 url: siteurl + 'cajax/joinrequestorg',
 data: {orgid: idtofetch},
 success: function (data, textStatus, xhr) {
 var jsonparser = $.parseJSON(data);
 if (jsonparser["result"]["status"] == true) {
 elementbutton.attr("disabled", "disabled");
 elementbutton.removeAttr("style");
 elementbutton.text("REQUEST SENT");
 elem.children(".switchbutton").remove();
 elem.parent(".col-md-4").css("cursor", "")
 $(".send-multi").addClass("hidden");
 }
 },
 });
 }
 })
 }) */

$(document).on("click", "#sendmultiplerequest", function () {
    var orgids = {};
    var Formdata = {};
    $("#orglisting .col-md-4 .rec-comp").each(function () {
        var elem = $(this);
        if (elem.children(".switchbutton").is(":visible") == true) {
            var elementbutton = elem.children("#joinrequestorg");
            var idtofetch = elementbutton.attr("data-id");
            console.log(idtofetch);
            if (idtofetch.length > 0) {
                Formdata = {orgid: idtofetch,contact: "1231231231", relation_to_org: "Employee", why_want_to_join: "Member", relation_to_org_other: "Other reason"};
                orgids[idtofetch] = Formdata;
            }
        }
    })
    var orgData = JSON.stringify(orgids);
    $.ajax({
        type: "POST",
        url: siteurl + 'cajax/joinrequestorg',
        data: orgids,
        success: function (data, textStatus, xhr) {
            var jsonparser = $.parseJSON(data);
            if (jsonparser["result"]["status"] == true) {
                $("#orglisting .col-md-4 .rec-comp").each(function () {
                    var elem = $(this);
                    if (elem.children(".switchbutton").is(":visible") == true) {
                        var elementbutton = elem.children("#joinrequestorg");
                        elementbutton.attr("disabled", "disabled");
                        elementbutton.removeAttr("style");
                        elementbutton.text("REQUEST SENT");
                        elem.children(".switchbutton").remove();
                        elem.parent(".col-md-4").css("cursor", "")
                    }
                })
                $(".send-multi").addClass("hidden");
            }
        },
    });
})


$(document).on("click", joinorg_sel+".rec-comp", function () {
    if (!$(this).hasClass('no-hand')) {
        if ($(this).children(".switchbutton").is(":visible") == true) {
            $(this).children(".switchbutton").remove();
        } else if ($(this).children(".btn-orange").attr("disabled") != "disabled") {
            //$('<div class="switchbutton"><img alt="" class="defaultorg" src="' + siteurl + '/img/selected-org.png"></div>').appendTo($(this))
            $('<div class="switchbutton"></div>').appendTo($(this))
        }
        var lengthswitchbutton = $(".col-md-4  .switchbutton").length;
        if ($(".col-md-4  .switchbutton").length > 0) {
            //$(".send-multi").removeClass("hidden");
            $(".counterorg").text(lengthswitchbutton);
            //$('[data-toggle="tooltip"]').tooltip('show');
        } else {
            $(".send-multi").addClass("hidden");
        }
    }
})

$(document).ready(function () {
    $("#OrganizationSecretcode").val("");
    //=clearing the val of search value whn refreshed
    $(joinorg_sel+"#searchorganization").val("");

})
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    } else {
        return true;
    }
}