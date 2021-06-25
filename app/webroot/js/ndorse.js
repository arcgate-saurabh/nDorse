function hideAlert() {
    jQuery(".alert").delay(7000).fadeOut(600);
}

function scrollToTop() {
    $("html, body").animate({
        scrollTop: 0
    }, "slow");
}

function isInteger(str) {
    status = false;
    if (Math.floor(str) == str) {
        status = true;
    } else {
// value is not an integer, show some validation error
    }

    return status;
}

function setNoToAllOptions() {
    $(".rbtn").each(function () {
        if ($(this).attr('value') == 'no') {
            $(this).prop("checked", true);
        }
    });
}

function createAlert(targetDivCriteria, alertType, message) {
    alertclass = 'alert-success';
    if (!alertType)
        alertclass = 'alert-danger';
    str = '<div class=" alert ' + alertclass + '" id="flashMessage" style="">' + message + '</div>';
    $(targetDivCriteria).html(str);
    hideAlert();
}
r = [];
// upload user function
function uploadajaxcsv(index, arraylength, uploadarray, orgid, orgname, orgcode) {

    var val = uploadarray[index];
    var targetData = val.data;
    if ($.trim(targetData[8]) == "") {
        targetData[8] = 0;
    }

    console.log(targetData);
    next = index + 1;
    $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        url: siteurl + 'ajax/uploadbulkuserscsv',
        data: {targetdata: targetData, orgId: orgid, orgName: orgname, orgcode: orgcode},
        success: function (data, textStatus, xhr) {

            //success++;
            var result = data.result;
            if (result == "Updated" || result == "Inserted") {
                result = "Successful";
                imageicon = "<div class='successfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            } else if (result == "User updated but Email not updated") {
                var comment = result;
                result = "Partial";
                imageicon = "<div class='unsuccessfulupload'></div>";
            } else {
                var comment = result;
                result = "Unsuccessful";
                imageicon = "<div class='unsuccessfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            }
            console.log(data);
            var userstatus = data.status;
            //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("");
//            console.log(val.data[6]);
            if (userstatus === 0) {
                console.log("inactive");
                if (result == "Unsuccessful" || result == "Partial") {
                    comment = comment.replace(/\n/g, "<br />");
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(comment);
                } else {
                    var displayMsg = "";
                    console.log(val);
                    var statusUser = val.data[8];
                    if (statusUser == 1 || statusUser == 2) {
                        displayMsg = "User is set to inactive since subscription limit is over";
                    } else {
                        displayMsg = "User is set to inactive";
                    }
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(displayMsg);
                }

                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            } else {
                console.log("active");
                if (result == "Unsuccessful" || result == "Partial") {
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
                }
                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            }

            //console.log(finalresult[val.email1]);
            //if (success >= mylength)
            //{
            //    //window.location.reload();
            //}
            if (arraylength > next) {
                uploadajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }

        },
        error: function (xhr) {
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            // success++;
            if (arraylength > next) {
                uploadajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }
            //if (success >= mylength)
            //{
            //    window.location.reload();
            //}
        }
    });
}

// upload user function
function uploadadfsajaxcsv(index, arraylength, uploadarray, orgid, orgname, orgcode) {

    var val = uploadarray[index];
//    console.log(val); return false;
    var targetData = val.data;
    if ($.trim(targetData[8]) == "") {
        targetData[8] = 0;
    }

//    console.log(targetData); return false;

    next = index + 1;
    $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        url: siteurl + 'ajax/uploadbulkusersadfscsv',
        data: {targetdata: targetData, orgId: orgid, orgName: orgname, orgcode: orgcode},
        success: function (data, textStatus, xhr) {

            //success++;
            var result = data.result;
            if (result == "Updated" || result == "Inserted") {
                var comment = result;
                result = "Successful";
                imageicon = "<div class='successfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            } else {
                var comment = result;
                result = "Unsuccessful";
                imageicon = "<div class='unsuccessfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            }

            console.log(data);
            var userstatus = data.status;
            //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("");
//            console.log(val.data[6]);

            if (userstatus === 0) {
                console.log("inactive");
                if (result == "Unsuccessful") {
                    comment = comment.replace(/\n/g, "<br />");
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(comment);
                } else {
                    var displayMsg = "";
                    console.log(val);
                    var statusUser = val.data[8];
                    if (statusUser == 1 || statusUser == 2) {
                        displayMsg = "User is set to inactive since subscription limit is over";
                    } else {
                        displayMsg = "User is set to inactive";
                    }
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(displayMsg);
                }

                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            } else {
                console.log("active");
//                if (result == "Unsuccessful") {
                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
//                }
                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            }

            //console.log(finalresult[val.email1]);
            //if (success >= mylength)
            //{
            //    //window.location.reload();
            //}
            if (arraylength > next) {
                uploadadfsajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }

        },
        error: function (data, xhr) {
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            // success++;
            if (arraylength > next) {
                uploadadfsajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }
            //if (success >= mylength)
            //{
            //    window.location.reload();
            //}
        }
    });
}
// end
// 
// upload user function
function updateadfsempidajaxcsv(index, arraylength, uploadarray, orgid) {

    var val = uploadarray[index];
//    console.log(val); return false;
    var targetData = val.data;
    if ($.trim(targetData[8]) == "") {
        targetData[8] = 0;
    }

//    console.log(targetData); return false;

    next = index + 1;
    $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        url: siteurl + 'ajax/updatebulkusersempidcsv',
        data: {targetdata: targetData, orgId: orgid},
        success: function (data, textStatus, xhr) {

            //success++;
            var result = data.result;
            console.log(result);
            if (result == "Updated" || result == "Inserted") {
                var comment = result;
                result = "Successful";
                imageicon = "<div class='successfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            } else {
                var comment = result;
                result = "Unsuccessful";
                imageicon = "<div class='unsuccessfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            }

            console.log(data);
            var userstatus = data.status;
            //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            $("#bulkuserstable tr").find("td[emailresult='" + val.newEmpID + "']").html(result + imageicon);
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.newEmpID + "']").html("");
//            console.log(val.data[6]);

            if (userstatus === 0) {
                console.log("inactive");
                if (result == "Unsuccessful") {
                    comment = comment.replace(/\n/g, "<br />");
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.newEmpID + "']").html(comment);
                } else {
                    var displayMsg = "";
                    console.log(val);
                    var statusUser = val.data[8];
                    if (statusUser == 1 || statusUser == 2) {
                        displayMsg = "User is set to inactive since subscription limit is over";
                    } else {
                        displayMsg = "User is set to inactive";
                    }
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.newEmpID + "']").html(displayMsg);
                }

                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            } else {
                console.log("active");
//                if (result == "Unsuccessful") {
                $("#bulkuserstable tr").find("td[emailcomment = '" + val.newEmpID + "']").text(comment);
//                }
                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            }

            //console.log(finalresult[val.email1]);
            //if (success >= mylength)
            //{
            //    //window.location.reload();
            //}
            if (arraylength > next) {
                updateadfsempidajaxcsv(next, arraylength, uploadarray, orgid)
            }

        },
        error: function (data, xhr) {
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.newEmpID + "']").html("Failed <div class='unsuccessfulupload'></div>");
            $("#bulkuserstable tr").find("td[emailresult='" + val.newEmpID + "']").html();
            //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            // success++;
            if (arraylength > next) {
                updateadfsempidajaxcsv(next, arraylength, uploadarray, orgid)
            }
            //if (success >= mylength)
            //{
            //    window.location.reload();
            //}
        }
    });
}
// end


// Migration new LCMC ORG users function
function uploadLCMCajaxcsv(index, arraylength, uploadarray, orgid, orgname, orgcode) {

    var val = uploadarray[index];
//    console.log(val); return false;
    var targetData = val.data;
    if ($.trim(targetData[8]) == "") {
        targetData[8] = 0;
    }

    next = index + 1;
    $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        url: siteurl + 'ajax/uploadbulkusersLCMCcsv',
        data: {targetdata: targetData, orgId: orgid, orgName: orgname, orgcode: orgcode},
        success: function (data, textStatus, xhr) {

            //success++;
            var result = data.result;
            console.log(result);
            if (result == "Updated" || result == "Inserted") {
                var comment = result;
                result = "Successful";
                imageicon = "<div class='successfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            } else {
                var comment = result;
                result = "Unsuccessful";
                imageicon = "<div class='unsuccessfulupload'></div>";
                //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            }

            console.log(data);
            var userstatus = data.status;
            //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("");
//            console.log(val.data[6]);

            if (userstatus === 0) {
                console.log("inactive");
                if (result == "Unsuccessful") {
                    comment = comment.replace(/\n/g, "<br />");
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(comment);
                } else {
                    var displayMsg = "";
                    console.log(val);
                    var statusUser = val.data[8];
                    if (statusUser == 1 || statusUser == 2) {
                        displayMsg = "User is set to inactive since subscription limit is over";
                    } else {
                        displayMsg = "User is set to inactive";
                    }
                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html(displayMsg);
                }

                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            } else {
                console.log("active");
//                if (result == "Unsuccessful") {
                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
//                }
                //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
                //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            }

            //console.log(finalresult[val.email1]);
            //if (success >= mylength)
            //{
            //    //window.location.reload();
            //}
            if (arraylength > next) {
                uploadLCMCajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }

        },
        error: function (data, xhr) {
            $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            // success++;
            if (arraylength > next) {
                uploadLCMCajaxcsv(next, arraylength, uploadarray, orgid, orgname, orgcode)
            }
            //if (success >= mylength)
            //{
            //    window.location.reload();
            //}
        }
    });
}
// end

//====================change status of organization inactive and active status
function changestatus(id, url, status, file) {
//    alert("I am here");
//    alert(id + " " + url + " " + status + " " + file);
//    return false;
    if (file == "Organization" && status == 1) {
        var stype = $("#orgstatus_" + id).attr("type");
        var smsg = "Are you sure you want to inactivate this organization?";
        if (stype == "ndorse") {
            smsg = "A Subscription is purchased for this organization. Inactivating organization will terminate the subscription automatically and all paid users will be inactivated. Are you sure you want to inactivate this organization?";
        } else if (stype == "web") {
            smsg = "A Subscription is purchased for this organization. Inactivating organization will terminate the subscription automatically and all paid users will be inactivated. This will be effective after the current billing cycle. Are you sure you want to inactivate this organization?";
        }
        bootbox.confirm({
            title: smsg,
            message: ' ',
            buttons: btnObj,
            closeButton: false,
            callback: function (result) {
                if (result == true) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {targetid: id, status: status, file: file},
                        success: function (data, textStatus, xhr) {
                            var newrowclass = "";
                            var jsonparser = $.parseJSON(data);
                            var newstatus = jsonparser.status;
                            var userrole = jsonparser.role;
                            var encodeid = jsonparser.encodeid;
                            var is_purchase = jsonparser.is_purchase;
                            var newchanges = "Activate";
                            $('#orgstatus_' + id + ' h3').text('Organization Status: Inactive');
                            // alert(userrole);
                            if (userrole == 1) {
                                var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + id + ')">Sell Subscription</button>';
                            } else {
                                if (is_purchase == 0)
                                {
                                    var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '" ><a class="btn btn-info" href="' + siteurl + 'subscription/btpurchase/' + encodeid + '">Purchase Subscription</a></div>';
                                } else {
                                    var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '"><div class="msg">Subscription canceled by admin </div></div>';
                                }

                            }
                            $("#orgstatus_" + id).attr("type", "normal");
                            $("#purchase_" + id).html(uphtml);
                            $("#purchase_" + id).hide();
                            $("#row_" + id).addClass("inactive");
                            $("#statuschanges_" + id).children("a").remove();
                            url = "'" + url + "'";
                            file = "'" + file + "'";
                            $('<a href="#" data-toggle="modal" onclick="changestatus( ' + id + ', ' + url + ', ' + newstatus + ', ' + file + ')">' + newchanges + '</a>').appendTo('#statuschanges_' + id);
                            $(".arrow_box").hide();
                        },
                    });
                }
            }
        });
//        bootbox.confirm(smsg, function (result) {
//
//            if (result == true) {
//                $.ajax({
//                    type: 'POST',
//                    url: url,
//                    data: {targetid: id, status: status, file: file},
//                    success: function (data, textStatus, xhr) {
//                        var newrowclass = "";
//                        var jsonparser = $.parseJSON(data);
//                        var newstatus = jsonparser.status;
//                        var userrole = jsonparser.role;
//                        var encodeid = jsonparser.encodeid;
//                        var is_purchase = jsonparser.is_purchase;
//
//                        var newchanges = "Activate";
//                        $('#orgstatus_' + id + ' h3').text('Organization Status: Inactive');
//                        // alert(userrole);
//                        if (userrole == 1) {
//                            var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + id + ')">Sell Subscription</button>';
//                        } else {
//                            if (is_purchase == 0)
//                            {
//                                var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '" ><a class="btn btn-info" href="' + siteurl + 'subscription/btpurchase/' + encodeid + '">Purchase Subscription</a></div>';
//                            } else {
//                                var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '"><div class="msg">Subscription canceled by admin </div></div>';
//                            }
//
//                        }
//                        $("#orgstatus_" + id).attr("type", "normal");
//                        $("#purchase_" + id).html(uphtml);
//                        $("#purchase_" + id).hide();
//
//                        $("#row_" + id).addClass("inactive");
//
//
//                        $("#statuschanges_" + id).children("a").remove();
//                        url = "'" + url + "'";
//                        file = "'" + file + "'";
//                        $('<a href="#" data-toggle="modal" onclick="changestatus( ' + id + ', ' + url + ', ' + newstatus + ', ' + file + ')">' + newchanges + '</a>').appendTo('#statuschanges_' + id);
//                        $(".arrow_box").hide();
//                    },
//                });
//            }
//        });
//        $(".bootbox-confirm .modal-body button").removeClass("bootbox-close-button");
//        $(".bootbox-confirm .modal-body button").addClass("hidden");
        // saurabh
    } else {
        $.ajax({
            type: 'POST',
            url: url,
            data: {targetid: id, status: status, file: file},
            success: function (data, textStatus, xhr) {
                var newrowclass = "";
                var jsonparser = $.parseJSON(data);
                var newstatus = jsonparser.status;
                if (newstatus == 1) {
                    var newchanges = "Inactivate";
                    if (file == "Organization") {
                        $('#orgstatus_' + id + ' h3').text('Organization Status: Active');
                        $("#purchase_" + id).show();
                    } else {
                        $('#statusactivity_' + id).text('Active');
                    }
                    $("#row_" + id).removeClass("inactive");
                } else {
                    var newchanges = "Activate";
                    if (file == "Organization") {
                        $('#orgstatus_' + id + ' h3').text('Organization Status: Inactive');
                        $("#purchase_" + id).hide();
                    } else {
                        $('#statusactivity_' + id).text('Inactive');
                    }
                    $("#row_" + id).addClass("inactive");
                }
                $("#statuschanges_" + id).children("a").remove();
                url = "'" + url + "'";
                file = "'" + file + "'";
                $('<a href="#" data-toggle="modal" onclick="changestatus( ' + id + ', ' + url + ', ' + newstatus + ', ' + file + ')">' + newchanges + '</a>').appendTo('#statuschanges_' + id);
                $(".arrow_box").hide();
            },
        });
    }

}

//====================change status of guest nDorsement approve reject and draft
function changeGuestNdorseStatus(id, status) {
//    alert(id + " " + status);
    
    if (status == 1) {
        var smsg = "Are you sure you want to approve this nominations?";
    } else if (status == 2) {
        var smsg = "Are you sure you want to reject this nominations?";
    } else if (status == 3) {
        var smsg = "Are you sure you want to send to draft this nominations?";
    } else if (status == 4) {
        var smsg = "Are you sure you want to delete this nominations?";
    } else if (status == 0) {
        var smsg = "Are you sure you want to send to pending this nominations?";
    }

    url = siteurl + 'ajax/changeguestndorsementstatus';
    bootbox.confirm({
        title: smsg,
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {endorsement_id: id, status: status},
                    success: function (data, textStatus, xhr) {
//                        console.log(data);
//                        return false;
                        var newrowclass = "";
                        var jsonparser = $.parseJSON(data);
                        var newstatus = jsonparser.status;
                        var userrole = jsonparser.role;
                        var encodeid = jsonparser.encodeid;
                        var is_purchase = jsonparser.is_purchase;
                        var newchanges = "Activate";
                        $('#orgstatus_' + id + ' h3').text('Organization Status: Inactive');
                        // alert(userrole);
                        if (userrole == 1) {
                            var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + id + ')">Sell Subscription</button>';
                        } else {
                            if (is_purchase == 0)
                            {
                                var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '" ><a class="btn btn-info" href="' + siteurl + 'subscription/btpurchase/' + encodeid + '">Purchase Subscription</a></div>';
                            } else {
                                var uphtml = '<div id="js_orgAction_' + id + '" og="' + encodeid + '"><div class="msg">Subscription canceled by admin </div></div>';
                            }

                        }
                        $("#orgstatus_" + id).attr("type", "normal");
                        $("#purchase_" + id).html(uphtml);
                        $("#purchase_" + id).hide();
                        $("#row_" + id).addClass("inactive");
                        $("#statuschanges_" + id).children("a").remove();
                        $("#feedback_section_" + id).fadeOut();
                        
                        var nomination_type = "Guest";
                        if (window.location.href.indexOf("daisy") > -1) {
                            nomination_type = "Daisy";
                        } 

                        if (status == 3) {
                            alert(nomination_type +' nomination has been drafted successfully.');
                            //alert('Guest nomination has been drafted successfully.');
                        } else if (status == 2) {
                            alert(nomination_type +' nomination has been rejected successfully.');
                        } else if (status == 4) {
                            alert(nomination_type +' nomination has been deleted successfully.');
                        } else if (status == 0) {
                            alert(nomination_type +' nomination has been marked to pending successfully.');
                        } else {
                            alert(nomination_type +' nomination has been approved successfully.');
                        }

//                        url = "'" + url + "'";
//                        file = "'" + file + "'";
//                        $('<a href="#" data-toggle="modal" onclick="changestatus( ' + id + ', ' + url + ', ' + newstatus + ', ' + file + ')">' + newchanges + '</a>').appendTo('#statuschanges_' + id);
                        $(".arrow_box").hide();
                    },
                });
            }
        }
    });
    return false;
//    if (status == 1) {
//        var stype = $("#orgstatus_" + id).attr("type");
//        var smsg = "Are you sure you want to inactivate this organization?";
//        if (stype == "ndorse") {
//            smsg = "A Subscription is purchased for this organization. Inactivating organization will terminate the subscription automatically and all paid users will be inactivated. Are you sure you want to inactivate this organization?";
//        } else if (stype == "web") {
//            smsg = "A Subscription is purchased for this organization. Inactivating organization will terminate the subscription automatically and all paid users will be inactivated. This will be effective after the current billing cycle. Are you sure you want to inactivate this organization?";
//        }
//
//    } else {
//        $.ajax({
//            type: 'POST',
//            url: url,
//            data: {targetid: id, status: status, file: file},
//            success: function (data, textStatus, xhr) {
//                var newrowclass = "";
//                var jsonparser = $.parseJSON(data);
//                var newstatus = jsonparser.status;
//                if (newstatus == 1) {
//                    var newchanges = "Inactivate";
//                    if (file == "Organization") {
//                        $('#orgstatus_' + id + ' h3').text('Organization Status: Active');
//
//                        $("#purchase_" + id).show();
//                    } else {
//                        $('#statusactivity_' + id).text('Active');
//                    }
//                    $("#row_" + id).removeClass("inactive");
//
//                } else {
//                    var newchanges = "Activate";
//                    if (file == "Organization") {
//                        $('#orgstatus_' + id + ' h3').text('Organization Status: Inactive');
//
//                        $("#purchase_" + id).hide();
//                    } else {
//                        $('#statusactivity_' + id).text('Inactive');
//                    }
//                    $("#row_" + id).addClass("inactive");
//
//                }
//                $("#statuschanges_" + id).children("a").remove();
//                url = "'" + url + "'";
//                file = "'" + file + "'";
//                $('<a href="#" data-toggle="modal" onclick="changestatus( ' + id + ', ' + url + ', ' + newstatus + ', ' + file + ')">' + newchanges + '</a>').appendTo('#statuschanges_' + id);
//                $(".arrow_box").hide();
//            },
//        });
//    }

}

//====================change status of VIDEO Active, In-active and delete
function changeVideoStatus(org_id, id, status) {
//    alert(id + " " + status);


    if (status == 2) {
        var smsg = "Are you sure you want to in-active this video?";
    } else if (status == 1) {
        var smsg = "Are you sure you want to re-publish this video?";
    } else if (status == 3) {
        var smsg = "Are you sure you want to in-active this video?";
    }


    if ((siteurl.indexOf('localhost') == -1) && (siteurl.indexOf('staging') == -1)) {
        siteurl = siteurl.replace("http", "https");
    } else {
        siteurl = siteurl.replace("https", "http");
    }
    url = siteurl + 'ajax/changevideostatus';
    bootbox.confirm({
        title: smsg,
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {video_id: id, status: status, org_id: org_id},
                    success: function (data, textStatus, xhr) {
                        console.log(data);
                        var jsonparser = $.parseJSON(data);
                        var apistatus = jsonparser.apistatus;
                        if (apistatus) {
                            if (status == 1) {
                                alert('A Copy of video has been posted successfully.');
//                                $("#video_section_" + id).fadeOut();
                            } else if (status == 2 || status == 3) {
                                $("#video_section_" + id).fadeOut();
                                alert('Video has been in-activated successfully.');
                            }
                        }
                        $(".arrow_box").hide();
                    },
                });
            }
        }
    });
    return false;
}


//===================delete users
function deleteUser(id) {
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/deleteuserstatus',
        data: {targetid: id},
        success: function (data, textStatus, xhr) {
            //totalUsers = $('#totalusers').text();
            //$('#totalusers').text(totalUsers-1);
            $('#row_' + id).remove();
            $('.close').trigger('click');
        },
    });
}

//===================delete endorser
function deleteEndorser(id) {
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/deleteendorserstatus',
        data: {targetid: id},
        success: function (data, textStatus, xhr) {
            totalUsers = $('#totalusers').text();
            $('#totalusers').text(totalUsers - 1);
            $('#row_' + id).remove();
            $('.close').trigger('click');
        },
    });
}


//===========================upload csv for bulk user
function uploadcsvbulkuser(orgid, orgname, orgcode) {
    if ($('#uploadedfile').is(':visible') == true) {
//$('#myModalbulkusersimports').modal('show');
//$("#bulkuserstable").html("");
        var filed = $(".hidefileupload").prop("files")[0];
        var reader = new FileReader();
        reader.readAsText(filed);
        var myVals = new Array();
        reader.onload = function (event) {
            var csvData = event.target.result;
            console.log(csvData);
            try {
                data = $.csv.toArrays(csvData);
                dataObj = $.csv.toObjects(csvData);
                console.log(data);
                console.log(dataObj);
            } catch (err) {
                alertbootbox("Invalid CSV");
                return false;
            }
            //if (data[0].length < 11 || data[0].length > 11) {
            //if (data[0].length < 12 || data[0].length > 12) { // +1 : Daisy enabled added by Babulal Prasad @20MAY2019
            if (data[0].length < 13 || data[0].length > 13) { // +1 : Sub-center-name added by Babulal Prasad @09-OCT-2019
                alertbootbox("Something wrong with the sheet 1");
                return false;
            }
            $('#myModalbulkusersimports').modal('show');
            $("#bulkuserstable").html("");
            var counter = 1;
            $('<table class="table table-striped table-hover"><tbody class="bulkuploadtable"><td>Emails</td><td>Status</td><td>Comments</td></tbody></table>').appendTo("#bulkuserstable");
            for (tmp in data) {
                if (tmp != 0 && data[tmp][6] != "") {
                    var email = data[tmp][6];
                    //$('<tr class="bulkusersrow"><td class="emailsusers col-md-3">' + data[tmp][0] + '</td></tr>').appendTo("#bulkuserstable");
                    myVals.push({email1: email, data: data[tmp]});
                    $('<tr><td>' + email + '</td><td emailresult=' + email + '>Processing.....</td><td emailcomment=' + email + '>Processing.....</td></tr>').appendTo(".bulkuploadtable");
                }
            }
            //$("#bulkuserstable").empty();
            var mylength = myVals.length;
            if (mylength > 0) {
                //alert("mylength : "+mylength);
                uploadajaxcsv(0, mylength, myVals, orgid, orgname, orgcode);
            }
            //var success = 0;
            //var finalresult = {};
            //var x = new Array();
            //delay(function () {
            //    $.each(myVals, function (index, val) {
            //        $.ajax({
            //            type: 'POST',
            //            async: false,
            //            dataType: 'json',
            //            url: siteurl + 'ajax/uploadbulkuserscsv',
            //            data: {targetdata: val.data, orgId: orgid, orgName: orgname, orgcode: orgcode},
            //            success: function (data, textStatus, xhr) {
            //
            //                success++;
            //                var result = data.result;
            //                console.log(result+"---test");
            //                if (result == "Updated" || result == "Inserted") {
            //                    result = "Successful";
            //                    imageicon = "<div class='successfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            //                } else {
            //                    var comment = result;
            //                    result = "Unsuccessful";
            //                    imageicon = "<div class='unsuccessfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            //                }
            //                var userstatus = data.status;
            //                //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                if (userstatus == 0) {
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    } else {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("User is set to inactive since subscription limit is over");
            //                    }
            //
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }else if(userstatus == 3) {
            //                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                }
            //                else{
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    }
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }
            //
            //                //console.log(finalresult[val.email1]);
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            },
            //            error: function (xhr) {
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //                //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                success++;
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            }
            //        });
            //
            //
            //    });
            //
            //}, 1000);

//            for (tmp in myVals) {
//                var values = myVals[tmp];
//                var email = values.email1;
//                console.log(email);
//                console.log(finalresult.email);
//                $(finalresult[email]).appendTo("#bulkuserstable");
//            }
            $('#uploadedfile').remove();
            $('#bulkuserbutton').val("");
        };
    } else {
        alert("Choose File First");
        return false;
    }
}



//===========================upload csv for bulk ADFS user
function uploadcsvbulkADFSuser(orgid, orgname, orgcode) {
    if ($('#uploadedfile').is(':visible') == true) {
//$('#myModalbulkusersimports').modal('show');
//$("#bulkuserstable").html("");
        var filed = $(".hidefileupload").prop("files")[0];
        var reader = new FileReader();
        reader.readAsText(filed);
        var myVals = new Array();
        reader.onload = function (event) {
            var csvData = event.target.result;
            console.log(csvData); //exit;
            try {
                data = $.csv.toArrays(csvData);
                dataObj = $.csv.toObjects(csvData);
                console.log(data);
                console.log(dataObj);
            } catch (err) {
                console.log(err);
                alertbootbox("Invalid CSV");
                return false;
            }
            //if (data[0].length < 11 || data[0].length > 11) {
            if (data[0].length < 7 || data[0].length > 7) { // +1 : Daisy enabled added by Babulal Prasad @20MAY2019
                alertbootbox("Something wrong with the sheet 1");
                return false;
            }
            $('#myModalbulkusersimports').modal('show');
            $("#bulkuserstable").html("");
            var counter = 1;
            $('<table class="table table-striped table-hover"><tbody class="bulkuploadtable"><td>Emails</td><td>Status</td><td>Comments</td></tbody></table>').appendTo("#bulkuserstable");
            for (tmp in data) {
                if (tmp != 0 && data[tmp][5] != "") {
                    var email = data[tmp][5];
                    //$('<tr class="bulkusersrow"><td class="emailsusers col-md-3">' + data[tmp][0] + '</td></tr>').appendTo("#bulkuserstable");
                    myVals.push({email1: email, data: data[tmp]});
                    $('<tr><td>' + email + '</td><td emailresult=' + email + '>Processing.....</td><td emailcomment=' + email + '>Processing.....</td></tr>').appendTo(".bulkuploadtable");
                }
            }
            //$("#bulkuserstable").empty();
            var mylength = myVals.length;
            if (mylength > 0) {
                //alert("mylength : "+mylength);
                uploadadfsajaxcsv(0, mylength, myVals, orgid, orgname, orgcode);
            }
            //var success = 0;
            //var finalresult = {};
            //var x = new Array();
            //delay(function () {
            //    $.each(myVals, function (index, val) {
            //        $.ajax({
            //            type: 'POST',
            //            async: false,
            //            dataType: 'json',
            //            url: siteurl + 'ajax/uploadbulkuserscsv',
            //            data: {targetdata: val.data, orgId: orgid, orgName: orgname, orgcode: orgcode},
            //            success: function (data, textStatus, xhr) {
            //
            //                success++;
            //                var result = data.result;
            //                console.log(result+"---test");
            //                if (result == "Updated" || result == "Inserted") {
            //                    result = "Successful";
            //                    imageicon = "<div class='successfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            //                } else {
            //                    var comment = result;
            //                    result = "Unsuccessful";
            //                    imageicon = "<div class='unsuccessfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            //                }
            //                var userstatus = data.status;
            //                //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                if (userstatus == 0) {
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    } else {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("User is set to inactive since subscription limit is over");
            //                    }
            //
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }else if(userstatus == 3) {
            //                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                }
            //                else{
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    }
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }
            //
            //                //console.log(finalresult[val.email1]);
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            },
            //            error: function (xhr) {
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //                //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                success++;
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            }
            //        });
            //
            //
            //    });
            //
            //}, 1000);

//            for (tmp in myVals) {
//                var values = myVals[tmp];
//                var email = values.email1;
//                console.log(email);
//                console.log(finalresult.email);
//                $(finalresult[email]).appendTo("#bulkuserstable");
//            }
            $('#uploadedfile').remove();
            $('#bulkuserbutton').val("");
        };
    } else {
        alert("Choose File First");
        return false;
    }
}

//===========================Bulk update employeeID ===//
function uploadcsvbulkEmpIdUpdate(orgid) {
    if ($('#uploadedfile').is(':visible') == true) {
//$('#myModalbulkusersimports').modal('show');
//$("#bulkuserstable").html("");
        var filed = $(".hidefileupload").prop("files")[0];
        var reader = new FileReader();
        reader.readAsText(filed);
        var myVals = new Array();
        reader.onload = function (event) {
            var csvData = event.target.result;
            console.log(csvData); //exit;
            try {
                data = $.csv.toArrays(csvData);
                dataObj = $.csv.toObjects(csvData);
                console.log(data);
                console.log(dataObj);
            } catch (err) {
                console.log(err);
                alertbootbox("Invalid CSV");
                return false;
            }
//            console.log(data); return false;
            if (data[0].length < 3 || data[0].length > 3) { 
                alertbootbox("Something wrong with the sheet 1");
                return false;
            }
            $('#myModalbulkusersimports').modal('show');
            $("#bulkuserstable").html("");
            var counter = 1;
            $('<table class="table table-striped table-hover"><tbody class="bulkuploadtable"><td>Old Emp Id</td><td>New Emp Id</td><td>Email</td><td>Status</td><td>Comments</td></tbody></table>').appendTo("#bulkuserstable");
            for (tmp in data) {
//                pr(tmp); exit;
                if (tmp != 0 && data[tmp][1] != "") {
                    var email = data[tmp][2];
                    var oldEmpID = data[tmp][0];
                    var newEmpID = data[tmp][1];
                    //$('<tr class="bulkusersrow"><td class="emailsusers col-md-3">' + data[tmp][0] + '</td></tr>').appendTo("#bulkuserstable");
                    myVals.push({email1: email, newEmpID:newEmpID, data: data[tmp]});
                    $('<tr><td>' + oldEmpID + '</td><td>' + newEmpID + '</td><td>' + email + '</td><td emailresult=' + newEmpID + '>Processing.....</td><td emailcomment=' + newEmpID + '>Processing.....</td></tr>').appendTo(".bulkuploadtable");
                }
            }
            //$("#bulkuserstable").empty();
            var mylength = myVals.length;
            if (mylength > 0) {
                //alert("mylength : "+mylength);
                updateadfsempidajaxcsv(0, mylength, myVals, orgid);
            }
            //var success = 0;
            //var finalresult = {};
            //var x = new Array();
            //delay(function () {
            //    $.each(myVals, function (index, val) {
            //        $.ajax({
            //            type: 'POST',
            //            async: false,
            //            dataType: 'json',
            //            url: siteurl + 'ajax/uploadbulkuserscsv',
            //            data: {targetdata: val.data, orgId: orgid, orgName: orgname, orgcode: orgcode},
            //            success: function (data, textStatus, xhr) {
            //
            //                success++;
            //                var result = data.result;
            //                console.log(result+"---test");
            //                if (result == "Updated" || result == "Inserted") {
            //                    result = "Successful";
            //                    imageicon = "<div class='successfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            //                } else {
            //                    var comment = result;
            //                    result = "Unsuccessful";
            //                    imageicon = "<div class='unsuccessfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            //                }
            //                var userstatus = data.status;
            //                //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                if (userstatus == 0) {
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    } else {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("User is set to inactive since subscription limit is over");
            //                    }
            //
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }else if(userstatus == 3) {
            //                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                }
            //                else{
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    }
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }
            //
            //                //console.log(finalresult[val.email1]);
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            },
            //            error: function (xhr) {
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //                //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                success++;
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            }
            //        });
            //
            //
            //    });
            //
            //}, 1000);

//            for (tmp in myVals) {
//                var values = myVals[tmp];
//                var email = values.email1;
//                console.log(email);
//                console.log(finalresult.email);
//                $(finalresult[email]).appendTo("#bulkuserstable");
//            }
            $('#uploadedfile').remove();
            $('#bulkuserbutton').val("");
        };
    } else {
        alert("Choose File First");
        return false;
    }
}

//===========================upload csv for bulk NEW LCMC user user
function uploadcsvbulkNEWLCMCuser(orgid, orgname, orgcode) {
    if ($('#uploadedfile').is(':visible') == true) {
//$('#myModalbulkusersimports').modal('show');
//$("#bulkuserstable").html("");
        var filed = $(".hidefileupload").prop("files")[0];
        var reader = new FileReader();
        reader.readAsText(filed);
        var myVals = new Array();
        reader.onload = function (event) {
            var csvData = event.target.result;
            try {
                data = $.csv.toArrays(csvData);
                dataObj = $.csv.toObjects(csvData);
                console.log(data);
                console.log(dataObj);
            } catch (err) {
                console.log(err);
                alertbootbox("Invalid CSV");
                return false;
            }

            //if (data[0].length < 11 || data[0].length > 11) {
            if (data[0].length < 13 || data[0].length > 13) { // +1 : Daisy enabled added by Babulal Prasad @20MAY2019
                alertbootbox("Something wrong with the sheet 1");
                return false;
            }
            $('#myModalbulkusersimports').modal('show');
            $("#bulkuserstable").html("");
            var counter = 1;
            $('<table class="table table-striped table-hover"><tbody class="bulkuploadtable"><td>Emails</td><td>Status</td><td>Comments</td></tbody></table>').appendTo("#bulkuserstable");
            for (tmp in data) {
                if (tmp != 0 && data[tmp][5] != "") {
                    var email = data[tmp][0];
                    //$('<tr class="bulkusersrow"><td class="emailsusers col-md-3">' + data[tmp][0] + '</td></tr>').appendTo("#bulkuserstable");
                    myVals.push({email1: email, data: data[tmp]});
                    $('<tr><td>' + email + '</td><td emailresult=' + email + '>Processing.....</td><td emailcomment=' + email + '>Processing.....</td></tr>').appendTo(".bulkuploadtable");
                }
            }
            //$("#bulkuserstable").empty();
            var mylength = myVals.length;
            if (mylength > 0) {
                //alert("mylength : "+mylength);
                uploadLCMCajaxcsv(0, mylength, myVals, orgid, orgname, orgcode);
            }
            //var success = 0;
            //var finalresult = {};
            //var x = new Array();
            //delay(function () {
            //    $.each(myVals, function (index, val) {
            //        $.ajax({
            //            type: 'POST',
            //            async: false,
            //            dataType: 'json',
            //            url: siteurl + 'ajax/uploadbulkuserscsv',
            //            data: {targetdata: val.data, orgId: orgid, orgName: orgname, orgcode: orgcode},
            //            success: function (data, textStatus, xhr) {
            //
            //                success++;
            //                var result = data.result;
            //                console.log(result+"---test");
            //                if (result == "Updated" || result == "Inserted") {
            //                    result = "Successful";
            //                    imageicon = "<div class='successfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-pass-icon.png";
            //                } else {
            //                    var comment = result;
            //                    result = "Unsuccessful";
            //                    imageicon = "<div class='unsuccessfulupload'></div>";
            //                    //imageicon = siteurl+"/app/webroot/img/test-fail-icon.png";
            //                }
            //                var userstatus = data.status;
            //                //$("#bulkuserstable tr").find("td[emailresult='"+val.email1+"']").html(result+' <img src="'+imageicon+'"/>');
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html(result + imageicon);
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                if (userstatus == 0) {
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    } else {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("User is set to inactive since subscription limit is over");
            //                    }
            //
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + ' (User is set to inactive since quota is over)</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }else if(userstatus == 3) {
            //                    $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text("");
            //                }
            //                else{
            //                    if (result == "Unsuccessful") {
            //                        $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").text(comment);
            //                    }
            //                    //var resultstring = '<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>';
            //                    //$('<div class="row bulkusersrow" ><div class="emailsusers col-md-9">' + val.email1 + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-pass-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                }
            //
            //                //console.log(finalresult[val.email1]);
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            },
            //            error: function (xhr) {
            //                $("#bulkuserstable tr").find("td[emailcomment = '" + val.email1 + "']").html("Failed <div class='unsuccessfulupload'></div>");
            //                $("#bulkuserstable tr").find("td[emailresult='" + val.email1 + "']").html();
            //                //$('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-9">' + email + '</div><div class="responseusers col-md-3" >' + result + ' <img src="' + siteurl + '/app/webroot/img/test-fail-icon.png"/></div></div>').appendTo("#bulkuserstable");
            //                success++;
            //                if (success >= mylength)
            //                {
            //                    //window.location.reload();
            //                }
            //            }
            //        });
            //
            //
            //    });
            //
            //}, 1000);

//            for (tmp in myVals) {
//                var values = myVals[tmp];
//                var email = values.email1;
//                console.log(email);
//                console.log(finalresult.email);
//                $(finalresult[email]).appendTo("#bulkuserstable");
//            }
            $('#uploadedfile').remove();
            $('#bulkuserbutton').val("");
        };
    } else {
        alert("Choose File First");
        return false;
    }
}


function uploadbulkimages(orgid) {
    if ($('#uploadedimagesfile').is(':visible') == true) {
        $("#bulkuserstable").empty();
        $('#myModalbulkusersimports').modal('show');
        //===============changing header information of modal
        $('#myModalbulkusersimports .modal-title').text('Bulk Import of Photos');
        //==========it provides the deatail of file and records
        var filed = $(".hidefileuploadimages").prop("files")[0];
        var reader = new FileReader();
        reader.readAsText(filed);
        reader.onload = function (event) {
            var csvData = event.target.result;
            data = $.csv.toArrays(csvData);
            if (data[0][0] != "Email" && data[0][1] != "Links") {
                $('#myModalbulkusersimports .modal-title').text('Wrong File Format');
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    async: false,
                    dataType: 'json',
                    url: siteurl + 'ajax/uploadbulkimagescsv',
                    data: {targetdata: data, orgid: orgid},
                    success: function (data, textStatus, xhr) {
                        for (tmp in data) {
                            $('<div class="row bulkusersrow" style=""><div class="emailsusers col-md-6">' + tmp + '</div><div class="responseusers col-md-6" >' + data[tmp] + '</div>').appendTo("#bulkuserstable");
                            $("#bulkuserstable").animate({"height": "+=20"}, "slow");
                        }
                    },
                    error: function (xhr) {

                    }
                });
            }
        }

    } else {
        alert("Choose File First");
        return false;
    }
}


//===========================upload csv for bulk links
function uploadcsvbulklinks() {
    if ($('#uploadedlinksfile').is(':visible')) {
        $("#bulklinksimportInfoForm").submit();
        return true;
    } else {
        alert("Choose File First");
        return false;
    }
}

//==clearing data on live endorsement page
$(document).on("click", "body", function (event) {
//=to close search org when externally clicked
    var clnew = $(event.target).attr("class");
    var searcheddata = $("#livesearch").html();
    if ($("#pagename").val() == "liveendorsements") {
        if (searcheddata.length > 0 && clnew != "livesearchdata") {
            $("#livesearch").html("");
            $("#searchliveendorsements").val("");
            $("#clearsearcheddata").attr("disabled", "disabled");
        }
    }

})

//==============================change endorser role
function changeendorserrole(userid, orgid, to) {
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/endorserrolechange',
        data: {userid: userid, orgid: orgid, changeTo: to},
        success: function (data, textStatus, xhr) {
            $(".arrow_box").hide();
            $('#mytable').trigger('update');
            if (to == 2) {
                //$("#roleendorser_" + userid).text("Designated Admin");
                $("#roleendorser_" + userid).text("Admin");
                $("#funcchangerole_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrole(' + userid + ', ' + orgid + ', 3)">' + Revoke_Admin_Control + '</a>').appendTo($("#funcchangerole_" + userid));
                $("#funcchangerolenew_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrolenew(' + userid + ', ' + orgid + ', 6)">' + Give_Elite_Control + '</a>').appendTo($("#funcchangerolenew_" + userid));
            } else {
                $("#roleendorser_" + userid).text(endorser);
                $("#funcchangerole_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrole(' + userid + ', ' + orgid + ', 2)">' + Give_Admin_Control + '</a>').appendTo($("#funcchangerole_" + userid));
                $("#funcchangerolenew_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrolenew(' + userid + ', ' + orgid + ', 6)">' + Give_Elite_Control + '</a>').appendTo($("#funcchangerolenew_" + userid));
            }
        }
    });
}

function changeendorserrolenew(userid, orgid, to) {
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/endorserrolechange',
        data: {userid: userid, orgid: orgid, changeTo: to},
        success: function (data, textStatus, xhr) {
            $(".arrow_box").hide();
            $('#mytable').trigger('update');
            if (to == 6) {
                //$("#roleendorser_" + userid).text("Designated Admin");
                $("#roleendorser_" + userid).text("nDorse Elite");
                $("#funcchangerolenew_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrolenew(' + userid + ', ' + orgid + ', 3)">' + Revoke_Elite_Control + '</a>').appendTo($("#funcchangerolenew_" + userid));
                $("#funcchangerole_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrole(' + userid + ', ' + orgid + ', 2)">' + Give_Admin_Control + '</a>').appendTo($("#funcchangerole_" + userid));
            } else {
                $("#roleendorser_" + userid).text(endorser);
                $("#funcchangerolenew_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrolenew(' + userid + ', ' + orgid + ', 6)">' + Give_Elite_Control + '</a>').appendTo($("#funcchangerolenew_" + userid));
                $("#funcchangerole_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="changeendorserrole(' + userid + ', ' + orgid + ', 2)">' + Give_Admin_Control + '</a>').appendTo($("#funcchangerole_" + userid));
            }
        }
    });
}

function setdaisyusers(userid, status) {
    console.log("userid : " + userid + " - status : " + status);
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/setdaisyuserstatus',
        data: {userid: userid, daisy_status: status},
        success: function (data, textStatus, xhr) {
            $(".arrow_box").hide();
            $('#mytable').trigger('update');
            if (status == 0) {
                $("#changedaisystatus_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="setdaisyusers(' + userid + ', 1)">' + Give_DAISY_Control + '</a>').appendTo($("#changedaisystatus_" + userid));
                $("#daisy_label_" + userid).removeClass("show").addClass("hide");
            } else {
                $("#changedaisystatus_" + userid).text("");
                $('<a href="javascript:void(0)" onclick="setdaisyusers(' + userid + ', 0)">' + Revoke_DAISY_Control + '</a>').appendTo($("#changedaisystatus_" + userid));
                $("#daisy_label_" + userid).removeClass("hide").addClass("show");
            }
        }
    });
}

function changestatususers(id, ps) {
    if (ps == "active") {
        var valCheckedRadio = 1;
    } else if (ps == "partially active") {
        var valCheckedRadio = 3;
    } else if (ps == "inactivert") {
        var valCheckedRadio = $('input[name=status_usersrt]:checked').val();
    }
//    else {
//        var valCheckedRadio = $('input[name=status_users_' + id + ']:checked').val();
//    }
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/changeuserstatus',
        data: {targetid: id, checkedvalue: valCheckedRadio, },
        success: function (data, textStatus, xhr) {
            var parseData = JSON.parse(data);
            if (parseData.status) {
                $('#mytable').trigger('update');
                var active = "'active'";
                var changestatusbox = '<a onclick="changestatususers(' + id + ', ' + active + ')" href="javascript:void(0)">Remove Evaluation</a>';
                if (valCheckedRadio == 0) {
                    var newValue = "Complete Inactivate";
                    $("#row_" + id).addClass("inactive");
                } else if (valCheckedRadio == 3) {
                    var newValue = "Evaluation Mode";
                    $("#row_" + id).addClass("inactive");
                } else {
                    var newValue = "Active";
                    active = "'partially active'";
                    //var changestatusbox = '<a href="javascript:void(0)" onclick="showinactivepopup(' + id + ')">Inactive</a>';
                    var changestatusbox = '<a onclick="changestatususers(' + id + ', ' + active + ')" href="javascript:void(0)">Evaluation Mode</a>';
                    $("#row_" + id).removeClass("inactive");
                }
                $("#row_" + id).find(".text-active").text(newValue);
                $("#changestatus_" + id).html(changestatusbox);
                $('.close').trigger('click');
                $("." + parseData.user_id + "_one").hide();
            } else {
                $('.close').trigger('click');
                $("." + parseData.user_id + "_one").hide();
                alertbootbox("pool not available.please update/purchase your subscription.");
            }
        }
    });
}

function changestatususersnew(id, oid, ps) {

    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/userOrgActionFromAdmin',
        data: {user_id: id, oid: oid, status: ps},
        success: function (data, textStatus, xhr) {
            var parseData = JSON.parse(data);
            $(".arrow_box").hide();
//            console.log(parseData);
//             return false;
            if (parseData.status) {
                $('#mytable').trigger('update');
            } else {
                $('.close').trigger('click');
            }
        }
    });
}

function pendingrequest(rule, orgid, orgname) {
    var emailschecked = {};
    $('#myModal_pendingrequest .css-checkbox').each(function () {
        if ($(this).is(":checked") == true && $(this).val() != "") {
            emailschecked[$(this).attr("rel")] = $(this).val();
        }
    })
    var counter = Object.keys(emailschecked).length;
    if (counter == 0) {
        alertbootbox("Please select atleast one email address");
        return false;
    }
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/pendingemails',
        data: {emailschecked: emailschecked, rule: rule, orgid: orgid, orgname: orgname},
        success: function (data, textStatus, xhr) {
            if (rule == "accept") {
                var jsonresult_src = $.parseJSON(data);
                var uidarray = jsonresult_src["userorgid"];
                var jsonresult = jsonresult_src["fresult"];
                var updateh = "";
                for (tmp in jsonresult) {
                    var user = jsonresult[tmp]["User"];
                    var uid = uidarray[tmp];
                    var fname = user["fname"];
                    var lname = user["lname"];
                    var email = user["email"];
                    var created = user["created"];
                    var updated = user["updated"];
                    var fullname = fname + " " + lname;
                    var userstatus = user["status"];
                    var statusdisplay = "Active";
                    var activemsg = " activated";
                    if (userstatus == 0) {
                        var statusdisplay = "Inactive";
                        activemsg = " Inactivated since limit is over";
                    }
                    if (updateh != "")
                    {
                        updateh += "<br />" + fname + " " + lname + " (" + email + ") is " + activemsg;
                    } else {
                        updateh += "<br />" + fname + " " + lname + " (" + email + ") is " + activemsg;
                    }
                    $('<tr id="row_' + uid + '"></tr>').insertBefore('#mytable > tbody > tr:first');
                    $('<td><img alt="" class="img-circle" src="' + siteurl + 'img/user.png"></td>').appendTo('#row_' + uid);
                    $('<td><h6 style="color:#ffffff; font-size:18px;">' + fullname + '</h6><p style="color:#c2c1c1; font-size:14px;">' + email + '<br>Last updated on: ' + updated + '<br>Created on: ' + updated + '</p></td>').appendTo('#row_' + uid);
                    $('<td class="text-active">' + statusdisplay + '</td><td id="roleendorser_' + uid + '">Endorser</td>').appendTo('#row_' + uid);
                    if (userstatus == 1) {
                        var user_active = "'partially active'";
                        $('<td><div class="ThreeDotsImg pull-right"><a class="dots" rel="' + uid + '_one" href="javascript:void(0);"><img alt="" class="img-responsive" src="' + siteurl + 'img/3dots.png"></a><div class="arrow_box rel= ' + uid + '_one" style="display: none;"><div style=" margin-top:-25px;" class="pull-right popupArrow"><img alt="" class="img-responsive" src="' + siteurl + 'img/popupArrow.png"></div><ul><li id="changestatus_' + uid + '"><a onclick="changestatususers(' + uid + ',' + user_active + ')" href="javascript:void(0)">Evaluation Mode</a></li><li id="funcchangerole_' + uid + '"><a href="javascript:void(0)" onclick="changeendorserrole(' + uid + ', ' + orgid + ', 2)">Give Admin Control</a></li><li><a href="javascript:void(0)" onclick ="showdeletepopup(' + uid + ')">Delete</a></li></ul></div></div></td>').appendTo('#row_' + uid);
                    } else {
                        $('<td><div class="ThreeDotsImg pull-right"><a class="dots" rel="' + uid + '_one" href="javascript:void(0);"><img alt="" class="img-responsive" src="' + siteurl + 'img/3dots.png"></a><div class="arrow_box rel= ' + uid + '_one" style="display: none;"><div style=" margin-top:-25px;" class="pull-right popupArrow"><img alt="" class="img-responsive" src="' + siteurl + 'img/popupArrow.png"></div><ul><li><a href="javascript:void(0)" onclick ="showdeletepopup(' + uid + ')">Delete</a></li></ul></div></div></td>').appendTo('#row_' + uid);
                    }
                }
                var counter = Object.keys(emailschecked).length;
                var counterprid = $("#counterpr").text();
                $("#counterpr").text(counterprid - counter);
                //$("#myModal2_deletepopup").modal("show");
                //$(".modal-title").text("");
                //$(".modal-title").text("Request Accepted");
                //alert(updateh);
                //alertbootbox(updateh);
                if (updateh != "") {
                    alertbootboxcb(updateh, function () {
                        window.location.reload();
                    });
                } else {
                    alertbootboxcb("Request Accepted", function () {
                        window.location.reload();
                    });
                }
                if ($("#counterpr").text() == 0) {
                    $("#innerhtmlpr a").attr("onclick", "");
                }
            } else {
                alertbootbox("Request Deleted");
            }
            $('#myModal_pendingrequest .css-checkbox').prop("checked", false)
            $('#myModal_pendingrequest').modal('hide')
        },
    });
}

function pendingbutton(orgid) {
    $("#myModal_pendingrequest #pendingdata").empty();
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/createemails',
        data: {orgid: orgid},
        success: function (data, textStatus, xhr) {
            var jsonresult = $.parseJSON(data);
            for (tmp in jsonresult) {
                var key = tmp;
                var fname = jsonresult[tmp]["firstname"];
                var lname = jsonresult[tmp]["lastname"];
                var email = jsonresult[tmp]["email"];
                var mobile_number = jsonresult[tmp]["mobile_number"];
                var relationship_to_org = jsonresult[tmp]["relationship_to_org"];
                var relationship_to_org_desc = jsonresult[tmp]["relationship_to_org_desc"];
                var why_want_to_join = jsonresult[tmp]["why_want_to_join"];
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + '</label><label class="css-label label-two" for="checkpr' + key + '">' + lname + '</label><label class="css-label label-three" for="checkpr' + key + '">' + email + '</label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + ' ' + lname + '<span class="css-label label-email" for="checkpr' + key + '"> (' + email + ')</span></label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + ' ' + lname + '<span class="css-label label-email" for="checkpr' + key + '"> (' + email + ')</span><span class="css-label label-email" for="checkpr' + key + '"> ' + mobile_number + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org_desc + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + why_want_to_join + '</span></label></div>').appendTo("#myModal_pendingrequest #pendingdata");
//                $('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + ' ' + lname + '<span class="css-label label-email" for="checkpr' + key + '"> (' + email + ')</span><span class="css-label label-email" for="checkpr' + key + '"> ' + mobile_number + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org_desc + '</span><span class="css-label label-email" for="checkpr' + key + '"> ' + why_want_to_join + '</span></label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                $('<tr><td ><div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label" for="checkpr' + key + '">&nbsp;</label></div></td><td ><span class="css-label label-one" for="checkpr' + key + '">' + fname + ' ' + lname + '</span></td><td ><span class="css-label label-email" for="checkpr' + key + '"> (' + email + ')</span></td><td ><span class="css-label label-email" for="checkpr' + key + '"> ' + mobile_number + '</span></td><td ><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org + '</span></td><td ><span class="css-label label-email" for="checkpr' + key + '"> ' + relationship_to_org_desc + '</span></td><td ><span class="css-label label-email" for="checkpr' + key + '"> ' + why_want_to_join + '</span></td></tr>').appendTo("#myModal_pendingrequest #pendingdata");
            }
            $('#myModal_pendingrequest').modal('show')
        },
    });
}

//====================show popup active inactive on runtime
function showinactivepopup(uid) {
    $("#myModal3_activestatus").modal("show");
    $("#myModal3_activestatus #confirmbuttoninactiveusers").attr("onclick", "");
    $("#myModal3_activestatus #confirmbuttoninactiveusers").attr("onclick", "changestatususers(" + uid + ", 'inactivert')");
    return false;
}

//====================show delete popup on runtime
function showdeletepopup(uid) {
    $("#myModal2_delete").modal("show");
    $("#myModal2_delete #confirmbuttondelete").attr("onclick", "");
    $("#myModal2_delete #confirmbuttondelete").attr("onclick", "deleteEndorser(" + uid + ")");
    return false;
}

//============function to reinvite from web
function reinviteweb(userid, useremail, fname, orgid, orgname, orgsecretcode) {
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/reinviteemail',
        data: {userid: userid, orgname: orgname, orgsecretcode: orgsecretcode, orgid: orgid, useremail: useremail, fname: fname},
        success: function (data, textStatus, xhr) {
            //=============close side popup
            $(".arrow_box").hide();
            //$("#myModal2_deletepopup").modal("show");
            $(".modal-title").text("");
            alertbootbox("Reinvited");
            //$(".modal-title").text("Reinvited");
        },
    });
}


//========reset password for users
/*$("#UserCreateclientForm").validate({
 rules: {
 'data[User][email]': {
 required: true,
 email: true,
 },
 'data[User][fname]': {
 required: true,
 },
 'data[User][lname]': {
 required: true,
 },
 'data[User][role]': {
 required: true,
 },
 //            'data[User][password]': {
 //                required: true,
 //                minlength: 8,
 //            },
 //            'data[User][confirm_password]': {
 //                required: true,
 //                equalTo: '#UserPassword'
 //            },
 },
 messages: {
 'data[User][firstname]': {
 required: "Email is required",
 email: "Invalid email"
 },
 'data[User][fname]': {
 required: "First Name is required",
 },
 'data[User][lname]': {
 required: "Last Name is required",
 },
 'data[User][role]': {
 required: "Please select role",
 },
 //            'data[User][password]': {
 //                required: "Password is required",
 //            },
 //            'data[User][confirm_password]': {
 //                required: "Confirm Password is required",
 //                equalTo: "Confirm Password do not match",
 //            },
 }
 
 });
 
 */

function resetpassworduser(uid, uname, uemail) {
    $("#username").text(uname);
    $("#uid").text(uid);
    $("#uemail").text(uemail);
    $("#resetpassworduser").modal("show");
}


//function searchendorsement(searchvalue) {
//    if (searchvalue.length >= 3) {
//        var orgid = $("#endorsementorgid").val();
//        $(".search-icn").addClass("search-loader").removeClass('search-icn');
//        delay(function () {
//            $.ajax({
//                type: "POST",
//                url: siteurl + 'ajax/searchendorsement',
//                data: {searchvalue: searchvalue, orgid: orgid},
//                success: function (data, xhr) {
//                    $("#searchendorsement").html('');
//                    $("#searchendorsement").html(data);
//                    $(".search-loader").addClass("search-icn").removeClass('search-loader');
//
//                },
//                error: function (jqXHR, textStatus, errorThrown) {
//
//                }
//            });
//        }, 2000);
//    }
//}

var endorsementalldata = "";
$(document).on("keyup", "#searchliveendorsements", function () {
    if ($(this).val().length >= 2) {
        $(".search-icn").addClass("search-loader").removeClass('search-icn');
        var orignalvalue = $(this).val();
        var searchvalue = trimAndLowerCaseString($(this).val());
        var orgid = $("#endorsementorgid").val();
        $("#livesearch").html("");
        delay(function () {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/SearchLiveEndorsementsHints',
                data: {searchvalue: searchvalue, orgid: orgid},
                success: function (data, xhr) {
                    $(".search-loader").addClass("search-icn").removeClass('search-loader');
                    var jsonparser = $.parseJSON(data);
                    if (jsonparser.msg == "success") {
                        var allobjects = {};
                        allobjects["entity"] = {};
                        allobjects["department"] = {};
                        allobjects["user"] = {};
                        var departments = jsonparser.departmentsresults;
                        var entities = jsonparser.entityresults;
                        var users = jsonparser.usersresults;
                        if (departments) {
                            for (tmpd in departments) {
                                allobjects["department"][tmpd] = departments[tmpd];
                            }
                        }
                        if (entities) {
                            for (tmpe in entities) {
                                allobjects["entity"][tmpe] = entities[tmpe];
                            }
                        }
                        if (users) {
                            for (tmpu in users) {
                                allobjects["user"][tmpu] = users[tmpu];
                            }
                        }
                    }

                    var resultcounter = 0;
                    for (tmpdata in allobjects) {
                        if ($.isEmptyObject(allobjects[tmpdata]) == false) {
                            for (finaltmp in allobjects[tmpdata]) {
                                $("#livesearch").append("<div class='livesearchdata' data-endorsementid='" + finaltmp + "' data-endorsementfor='" + tmpdata + "'>" + allobjects[tmpdata][finaltmp] + "</div>");
                                resultcounter++;
                            }

                        } else {
                            console.log("no result for " + tmpdata);
                        }
                    }
                    if (resultcounter == 0) {
                        $("#searchendorsement").html("");
                        $("#searchendorsement").append("<div class='nodataavailable'>No Data Available</div>");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            })
        }, 2000);
    } else if ($(this).val().length == 0) {
        $("#searchendorsement").html(allendorsements);
        clearTimeout(timer);
        $(".search-loader").addClass("search-icn").removeClass('search-loader');
    }
});
var livesearchdataobject = {}
//==========searching data after clicking
$(document).on("click", ".livesearchdata", function () {
    $("#searchliveendorsements").val($(this).text());
    $("#livesearch").html("");
    var orgid = $("#endorsementorgid").val();
    var endorsementfor = $(this).attr("data-endorsementfor");
    var endorsementid = $(this).attr("data-endorsementid");
    livesearchdataobject["endorsementfor"] = endorsementfor;
    livesearchdataobject["endorsementid"] = endorsementid;
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchendorsementfiltered',
        data: {orgid: orgid, endorsementfor: endorsementfor, endorsementid: endorsementid},
        success: function (data, xhr) {
            $("#searchendorsement").html('');
            if (data.length == 0) {
                $("#searchendorsement").html("<div class='nodataavailable'>No Data Available</div>");
            } else {
                $("#searchendorsement").html(data);
            }


            //$(".search-loader").addClass("search-icn").removeClass('search-loader');
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
});
$("#submitfilterendorsement").click(function () {
    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var entities = $("#entityfilter").val();
    var startdate = $("#startdaterandc").val();
    var enddate = $("#enddaterandc").val();
    var orgid = $("#endorsementorgid").val();
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/filterallendorsement',
        data: {jobtitles: jobtitles, departments: departments, entities: entities, orgid: orgid, startdate: startdate, enddate: enddate},
        beforeSend: function () {
            $(".search-icn").addClass("search-loader").removeClass('search-icn');
        },
        success: function (data, xhr) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
            $("#allendorsementsearching").html(data);
            totalendorsements();
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        }
    });
})

$(document).on("click", "#submitfilterusers", function () {
    var searchkeyword = $("#searchkeyword").val();
    searchusers(searchkeyword);
});
function searchusers(searchvalue) {

    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var status = $("#statusFilter").val();
    var usertype = $("#usertypeFilter").val();
    var orgid = $("#orgid").val();
    $(".search-icn").addClass("search-loader").removeClass('search-icn');
    delay(function () {
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/searchusers',
            data: {searchvalue: searchvalue, orgid: orgid, departments: departments, jobtitles: jobtitles, status: status, usertype: usertype, },
            success: function (data, xhr) {

//                console.log(data); return false;

                $(".search-loader").addClass("search-icn").removeClass('search-loader');
                //======to apply sorting even after load and search users
                $('#mytable').trigger('update');
                $("#userslisting").html('');
                if (data.length == 0) {
                    $("#userslisting").append("<tr><td colspan=5>No Data Available</td></tr>");
                } else {
                    $("#userslisting").html(data);
                }

                //$("#searchendorsement").html(data);
                //$(data).appendTo(".table-condensed tbody");
                //$(".hiddenloader").addClass("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    }, 1000);
}

$(document).on("keyup", "#searchorganization", function () {
    filterorganizationlist();
});
$(document).on("mouseup", ".orgfilterradio", function () {
    delay(function () {
        filterorganizationlist()
    }, 200);
});
function filterorganizationlist() {
    var searchvalue = $('#searchorganization').val();
    var orgType = $("input[name=orgtype]:checked").val();
    if ((searchvalue.length >= "0")) {
        $(".search-icn").addClass("search-loader").removeClass('search-icn');
        delay(function () {

            console.log("before 2 : " + siteurl);
            console.log(siteurl.indexOf('localhost') > 0);
            console.log(siteurl.indexOf('staging') > 0);
            if ((siteurl.indexOf('localhost') > 0) || (siteurl.indexOf('staging') > 0)) {

                console.log((siteurl.indexOf('https')));
//                if (siteurl.indexOf('https') > 0) {
                siteurl = siteurl.replace("https", "http");
//                }

            }
            console.log("after : " + siteurl);
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/searchorganization',
                data: {searchvalue: searchvalue, orgType: orgType},
                success: function (data, xhr) {
                    console.log(data.length);
                    $(".containerorg").html('');
                    $(".search-loader").addClass("search-icn").removeClass('search-loader');
                    if (data.length == 0) {
                        $(".containerorg").html("<div class='nodataavailable'>No Data Available</div>");
                    } else {
                        $(".containerorg").html(data);
                    }
                    //$("#searchendorsement").html(data);
                    //$(data).appendTo(".table-condensed tbody");
                    $(".hiddenloader").addClass("hidden");
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }, 1000);
    } else {
        $(".containerorg").html("");
        $(".containerorg").append(allorglisting);
        $(".search-loader").addClass("search-icn").removeClass('search-loader');
        clearTimeout(timer);
    }

}
$(document).on("keyup", "#searchallusers", function () {
    var searchvalue = $('#searchallusers').val();
//    console.log(searchvalue);
//    return false;
    console.log(searchvalue.length);
    if ((searchvalue.length > 0)) {

        $(".search-icn").addClass("search-loader").removeClass('search-icn');
        delay(function () {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/searchallusers',
                data: {searchvalue: searchvalue},
                success: function (data, xhr) {
//                    console.log(data);
//                    return false;
                    $(".containerorg").html('');
                    $(".search-loader").addClass("search-icn").removeClass('search-loader');
                    if (data.length == 0) {
                        $(".containerorg").html("<div class='nodataavailable'>No Data Available</div>");
                    } else {
                        $(".containerorg").html(data);
                    }
                    //$("#searchendorsement").html(data);
                    //$(data).appendTo(".table-condensed tbody");
                    //$(".hiddenloader").addClass("hidden");
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }, 1000);
    } else {
        $(".containerorg").html("");
        $(".containerorg").append("<div class='nodataavailable'>No Data Available</div>");
        $(".search-loader").addClass("search-icn").removeClass('search-loader');
        clearTimeout(timer);
    }

});
function deleteuser(uid) {
    $("#myModa2_deleteusers").modal("show");
    $("#myModa2_deleteusers #deleteclick").attr("onclick", "");
    $("#myModa2_deleteusers #deleteclick").attr("onclick", "deleteUser(" + uid + ")");
}

function deleteorganizations(orgid) {
    $("#myModa2_delete").modal("show");
    $("#myModa2_delete #deleteclick").attr("onclick", "");
    $("#myModa2_delete #deleteclick").attr("onclick", "deleteitem(" + orgid + ")");
}

//====================deleting the organization with ajax and making its status 2
function deleteitem(id) {
    $.ajax({
        type: 'POST',
        url: siteurl + 'ajax/deleteorgstatus',
        data: {targetid: id},
        success: function (data, textStatus, xhr) {
            var noOfOrgUsers = $("#o_totalusers_" + id).text();
            var TotalUsers = $("#u_totalusers").text();
            var NewTotalUsers = TotalUsers - noOfOrgUsers;
            $("#u_totalusers").text(NewTotalUsers);
            $("#u_totalorgs").text($("#u_totalorgs").text() - 1);
            $('#row_' + id).hide();
            $('.rowend_' + id).remove();
            $('.section_' + id).remove();
            $('.close').trigger('click');
        },
    });
}

var req_sent = false;
//var dateparameters = {
//    showOn: "button",
//    buttonImage: siteurl + "img/calendar.gif",
//    buttonImageOnly: true,
//    changeMonth: true,
//    changeYear: true,
//    required: true,
//    dpDate: true,
//    maxDate: '0d',
//    showAnim: "fadeIn",
//    yearRange: "-100:+0",
//    //dateFormat: 'yy-mm-dd'
//    dateFormat: 'mm-dd-yy'
//};

//=============to search orgowners from the index page
function searchorgowners(searchvalue) {
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchorgowners',
        data: {searchvalue: searchvalue},
        success: function (data, xhr) {
            $(".tableusersindex").find("tr:gt(0)").remove();
            $(data).appendTo(".tableusersindex");
            //$("#searchendorsement").html(data);
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

//=======================zoom functionality for leaderboard
function reportsandchartszoom(orgid, graphname) {
    var title = "";
    $(".modal-dialog").css("width", "1200px");
    if (graphname == "leader_board") {
//      title = "Leader Board";
        $(".modal-dialog").css("");
        $(".modal-dialog").css("width", "800px");
    }

    var startdate = $("#startdaterandc").val();
    var enddate = $("#enddaterandc").val();
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/leaderboardzoomin',
        data: {orgid: orgid, startdaterandc: startdate, enddaterandc: enddate, graphname: graphname},
        success: function (data, xhr) {
            $("#myModal2_commonrandc").modal("show");
            $(".modal-body .modal-title").text(title)
            $(".modal-body #bodytext").html("");
            $(".modal-body #bodytext").html(data);
            $("#leaderboardtablezoom").tablesorter();
            bindButtonClick();
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}


//============searchleaderboarddata

function searchleaderboard(searchvalue, orgid) {
    var searchstartdate = $("#startdaterandc").val();
    var searchenddate = $("#enddaterandc").val();
    var searchvalue = trimAndLowerCaseString(searchvalue);
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchleaderboard',
        data: {searchvalue: searchvalue, orgid: orgid, searchstartdate: searchstartdate, searchenddate: searchenddate},
        success: function (data, xhr) {
            if ($('#myModal2_commonrandc').is(':visible') == true) {
                var tableid = "tableleaderboardzoom";
                $('#leaderboardtablezoom').trigger('update');
            } else {
                var tableid = "tableleaderboard";
                $('#leaderboardtable').trigger('update');
            }
            $("#" + tableid + " #leaderboardsquare").html("");
            if (data.length == 0) {
                $("#" + tableid + " #leaderboardsquare").html("<tr><td colspan='5'>No Data Available</td></tr>");
            } else {
                $("#" + tableid + " #leaderboardsquare").html(data);
            }
//            if (data.length == 0) {
//                $("#tableleaderboard #leaderboardsquare").html("");
//                $("#tableleaderboard #leaderboardsquare").html("<tr><td colspan='5'>No Data Available</td></tr>");
//            } else {
//                if ($('#myModal2_commonrandc').is(':visible') == true) {
//                    $("#tableleaderboardzoom #leaderboardsquare").html("");
//                    $("#tableleaderboardzoom #leaderboardsquare").html(data);
//                } else {
//                    $("#tableleaderboard #leaderboardsquare").html("");
//                    $("#tableleaderboard #leaderboardsquare").html(data);
//                }
//            }

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

//=================renew subscription popup
function renewsubscription() {
    $("#myModal2_commonpopupmessage").modal("show")
    $("#myModal2_commonpopupmessage .modal-title").text("Renew Subscription")
}

function purchasesubscription(org_id) {
//    console.log(org_id);
    $("#adminsubscriptionsubmit").prop("disabled", false);
    $("#adminsubscriptionIndexForm").validate().resetForm();
    $("#sale_org_id").val(org_id);
    $("#suadmin_subscription_trial").hide();
    //$("#suadmin_subscription_paid").hide();
    $("#suadmin_subscription_amt").hide();
    $("#amt").val("");
    $("#users").val("");
    $("#adminsubscriptionTrialDuration").val("");
    $("#adminsubscriptionMode").val("");
    $("#myModal2_purchasesubscription").modal("show")
    $("#myModal2_purchasesubscription .modal-title").text("Sell Subscription");
}
function overwritesubscription(org_id) {
    console.log(org_id);
    //upgradeadminsubscriptionInfoForm
    $("#overwriteadminsubscriptionIndexForm").validate().resetForm();
    $("#overwrite_org_id").val(org_id);
    $("#overwrite_org_id_amt").val("");
    $("#overwrite_org_id_users").val("");
    $("#myModal2_overwritesubscription").modal("show")
    $("#myModal2_overwritesubscription .modal-title").text("Override Subscription");
}
function upgradesubscription(org_id) {
    console.log(org_id);
    //upgradeadminsubscriptionInfoForm
    $("#updateadminsubscriptionsubmit").prop("disabled", false);
    $("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#up_org_id").val(org_id);
    $("#upgrade_amt").val("");
    $("#upgrade_users").val("");
    $("#myModal2_upgradesubscription").modal("show")
    $("#myModal2_upgradesubscription .modal-title").text("Upgrade Subscription");
}
function downgradesubscription(org_id, pool_qty) {
    console.log(org_id);
    $("#downgradeadminsubscriptionsubmit").prop("disabled", false);
    $("#downgradeadminsubscriptionIndexForm").validate().resetForm();
    //$("#downgrade_active_users1").hide();
    // $("#downgrade_active_users").html("");
    $("#suadmin_downgrade_amt").show();
    $("#disp_user_option").hide();
    var userquota = 0;
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/getinactiveusers',
        data: {org_id: org_id},
        success: function (data) {
            console.log(data);
            var parseData = JSON.parse(data);
            var userdata = parseData.fresult;
            var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + up_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + up_org_id + ',' + parseData.qty + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + up_org_id + ')" >Terminate Subscription</button>';
            //  downgrade_active_users

            for (tmp in userdata) {
                userquota++;
                //var key = tmp;
                //var fname = userdata[tmp]["fname"];
                //var lname = userdata[tmp]["lname"];
                //var email = userdata[tmp]["email"];
                //var username = fname + ' ' + lname;
                ////$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + '</label><label class="css-label label-two" for="checkpr' + key + '">' + lname + '</label><label class="css-label label-three" for="checkpr' + key + '">' + email + '</label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                //$('<div class="checkboxsearch" username="' + username.toLowerCase() + '" id="' + key + '"><input type="checkbox" class="checkselect" name="inactiverequests[]" id="checkpr' + key + '"   value="' + key + '" rel="' + key + '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + fname + ' ' + lname + '</div>').appendTo("#myModal2_downgradesubscription #downgrade_active_users");
            }
            // alert(userquota);
            $("#user_quota").val(userquota);
            //  alert(pool_qty+ " "+userquota);
            allowed_users = 0;
            totalqty = parseInt(pool_qty);
            if (userquota > 10 && totalqty > 0)
            {
                totalqty = ((parseInt(pool_qty)) + 10);
                allowed_users = totalqty - userquota;
            } else if (totalqty > 0) {
                allowed_users = totalqty;
            }

            $("#myModal2_downgradesubscription .modal-title").text("Downgrade Subscription (Max users allowed: " + allowed_users + ")");
        }
    });
    $("#down_org_id").val(org_id);
    $("#pool_qty").val(pool_qty);
    $("#downgrade_amt").val("");
    $("#downgrade_users").val("");
    $("#myModal2_downgradesubscription").modal("show")

}
function terminatesubscription(org_id) {
    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#adminterminatesubscriptionsubmit").prop("disabled", false);
    $("#terminate_org_id").val(org_id);
    $("#adminterminatesubscriptionsubmit").attr("type", "mannual");
    $("#myModal2_terminatesubscription").modal("show")
    $("#myModal2_terminatesubscription .modal-title").text("Terminate Subscription");
}
function terminatesubscriptiontrial(org_id) {
    console.log(org_id);
    bootbox.confirm({
        title: 'Are you sure you want to terminate the trial subscription?',
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/terminate',
                    data: {terminate_org_id: org_id, adminterminatesubscription: {"option": 1}},
                    dataType: 'json',
                    success: function (response) {
                        if (response.status) {
                            var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + org_id + ')">Sell Subscription</button>';
                            var page_ndorse_url = window.location.href;
                            if (page_ndorse_url.search("info") > 0) {
                                window.location.reload();
                            } else {
                                $("#available_quota_" + org_id).html(10);
                                $("#purchase_" + org_id).html(uphtml);
                                $("#myModal2_terminatesubscription").modal("hide")
                            }
                        }

                        // bootbox.alert(response.msg);

                    },
                    error: function (response) {

                    }
                });
            }
        }
    });
//    bootbox.confirm("Are you sure you want to terminate the trial subscription?", function (result) {
//
//        if (result == true) {
//            $.ajax({
//                type: "POST",
//                url: siteurl + 'ajax/terminate',
//                data: {terminate_org_id: org_id, adminterminatesubscription: 1},
//                dataType: 'json',
//                success: function (response) {
//
//                    if (response.status) {
//
//
//                        var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + org_id + ')">Sell Subscription</button>';
//
//
//                        var page_ndorse_url = window.location.href;
//                        if (page_ndorse_url.search("info") > 0) {
//                            window.location.reload();
//                        } else {
//                            $("#available_quota_" + org_id).html(10);
//                            $("#purchase_" + org_id).html(uphtml);
//                            $("#myModal2_terminatesubscription").modal("hide")
//                        }
//                    }
//
//                    // bootbox.alert(response.msg);
//
//                },
//                error: function (response) {
//
//                }
//            });
//        }
//    });
//    $(".bootbox-confirm .modal-body button").removeClass("bootbox-close-button");
//    $(".bootbox-confirm .modal-body button").addClass("hidden");
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    //$("#adminterminatesubscriptionsubmit").prop("disabled", false);
    //$("#terminate_org_id").val(org_id);
    //
    //$("#adminterminatesubscriptionsubmit").attr("type", "trial");
    //$("#myModal2_terminatesubscription").modal("show")
    //$("#myModal2_terminatesubscription .modal-title").text("Terminate Subscription");
}
function terminatesubscriptionbybraintree(org_id) {

    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#terminate_org_id").val(org_id);
    $("#adminterminatesubscriptionsubmit").prop("disabled", false);
    $("#adminterminatesubscriptionsubmit").attr("type", "braintree");
    $("#myModal2_terminatesubscription").modal("show")
    $("#myModal2_terminatesubscription .modal-title").text("Terminate Subscription");
}
// overwrite scription

function bulkactive(org_id) {
    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#bulk_org_id").val(org_id);
    $("#bulk_active_users").html("");
    $("#active_users_no").val("");
    $("#bulkactiveuserOldestActive").val("yes");
    var userquota = 0;
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/getactiveusers',
        data: {org_id: org_id},
        success: function (data) {
            console.log(data);
            var parseData = JSON.parse(data);
            var userdata = parseData.fresult;
            //  downgrade_active_users

            for (tmp in userdata) {
                userquota++;
                var key = tmp;
                var fname = userdata[tmp]["fname"];
                var lname = userdata[tmp]["lname"];
                var email = userdata[tmp]["email"];
                var username = fname + ' ' + lname;
                var userid = userdata[tmp]["id"];
                var admin = "";
                if (userdata[tmp]["user_role"] == 2)
                {
                    admin = " (Admin)";
                }
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + '</label><label class="css-label label-two" for="checkpr' + key + '">' + lname + '</label><label class="css-label label-three" for="checkpr' + key + '">' + email + '</label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                $('<div class="checkboxsearchuseractive checkbox" username="' + username.toLowerCase() + '" id="' + userid + '"><input type="checkbox" class="checkactiveselectuser css-checkbox" name="activeuser[]" id="checkpr' + userid + '"   value="' + userid + '" rel="' + userid + '"/>&nbsp; <label class="css-label" for="checkpr' + userid + '"> ' + fname + ' ' + lname + admin + '</label></div>').appendTo("#myModal2_bulkactiveuser #bulk_active_users");
            }
            // alert(userquota);
            if (userquota > 0) {
                $("#myModal2_bulkactiveuser").modal("show")
                $("#myModal2_bulkactiveuser .modal-title").text("Bulk Activate Users");
            } else {
                alertbootbox("There is no user to activate since all users are already active");
            }
        }
    });
}
// bulk inactive user functionality
function bulkinactive(org_id) {
    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#inbulk_org_id").val(org_id);
    $("#bulk_inactive_users").html("");
    $("#inactive_users_no").val("");
    var userquota = 0;
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/getinactiveusers',
        data: {org_id: org_id},
        success: function (data) {
            console.log(data);
            var parseData = JSON.parse(data);
            var userdata = parseData.fresult;
            //  downgrade_active_users

            for (tmp in userdata) {
                userquota++;
                var key = tmp;
                var fname = userdata[tmp]["fname"];
                var lname = userdata[tmp]["lname"];
                var email = userdata[tmp]["email"];
                var username = fname + ' ' + lname;
                var userid = userdata[tmp]["id"];
                var admin = "";
                if (userdata[tmp]["user_role"] == 2)
                {
                    admin = " (Admin)";
                }
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + '</label><label class="css-label label-two" for="checkpr' + key + '">' + lname + '</label><label class="css-label label-three" for="checkpr' + key + '">' + email + '</label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                $('<div class="checkboxsearchuserinactive checkbox" username="' + username.toLowerCase() + '" id="' + userid + '"><input type="checkbox" class="checkinactiveselectuser css-checkbox" name="inactiveuser[]" id="checkpr' + userid + '"   value="' + userid + '" rel="' + userid + '"/>&nbsp; <label class="css-label" for="checkpr' + userid + '">' + fname + ' ' + lname + admin + '</label></div>').appendTo("#myModal2_bulkinactiveuser #bulk_inactive_users");
            }
            // alert(userquota);
            if (userquota > 0) {
                $("#myModal2_bulkinactiveuser").modal("show")
                $("#myModal2_bulkinactiveuser .modal-title").text("Bulk Inactivate Users");
            } else {
                alertbootbox("There is no user to inactivate since all user are already inactive");
            }

        }
    });
}
function revertsubscription(org_id, qty) {
    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $.ajax({
        type: 'POST',
        url: siteurl + "ajax/revert",
        data: {targetid: org_id},
        success: function (data, textStatus, xhr) {
            var parseData = JSON.parse(data);
            var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + org_id + ',' + (qty) + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + org_id + ')" >Terminate Subscription</button>';
            if (parseData.type == "trial") {
                var uphtml = '<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + org_id + ')" >Terminate Subscription</button>';
            }


            var page_ndorse_url = window.location.href;
            if (page_ndorse_url.search("info") > 0) {

                alertbootboxcb("Subscription successfully reverted to active.", function () {
                    window.location.reload();
                });
            } else {
                alertbootbox("Subscription successfully reverted to active.");
                $("#purchase_" + org_id).html(uphtml);
            }
        },
    });
}
function AllitemDisplay()
{
    $(".checkboxsearch").show();
    $(".checkboxsearchuseractive").show();
    $(".checkboxsearchuserinactive").show();
    $(".checkboxsearchuserreinvite").show();
}
function itemSearch(searchtext)
{
    console.log(searchtext);
    if (searchtext != "") {
        // AllitemDisplay();
        jQuery("div.checkboxsearch").each(function () {
            var strsearch = jQuery(this).attr('username');
            if (strsearch.indexOf(searchtext.toLowerCase()) >= 0)
            {
                jQuery(this).show();
            } else
            {
                //checkpr
                $("#checkpr" + jQuery(this).attr("id")).removeAttr('checked');
                jQuery(this).hide();
            }
        });
        //jQuery('.breakfast_step .product_items').tinyscrollbar({ thumbSize: 68 });

    } else {
        AllitemDisplay();
    }


}
function itemSearchactiveuser(searchtext)
{

    console.log(searchtext);
    if (searchtext != "") {
        // AllitemDisplay();
        jQuery("div.checkboxsearchuseractive").each(function () {
            var strsearch = jQuery(this).attr('username');
            if (strsearch.indexOf(searchtext.toLowerCase()) >= 0)
            {
                jQuery(this).show();
            } else
            {
                //checkpr
                $("#checkpr" + jQuery(this).attr("id")).removeAttr('checked');
                jQuery(this).hide();
            }
        });
        //jQuery('.breakfast_step .product_items').tinyscrollbar({ thumbSize: 68 });

    } else {
        AllitemDisplay();
    }

}
function itemSearchinactiveuser(searchtext)
{

    console.log(searchtext);
    if (searchtext != "") {
        // AllitemDisplay();
        jQuery("div.checkboxsearchuserinactive").each(function () {
            var strsearch = jQuery(this).attr('username');
            if (strsearch.indexOf(searchtext.toLowerCase()) >= 0)
            {
                jQuery(this).show();
            } else
            {
                //checkpr
                // $("#checkpr"+jQuery(this).attr("id")).removeAttr('checked');
                jQuery(this).hide();
            }
        });
        //jQuery('.breakfast_step .product_items').tinyscrollbar({ thumbSize: 68 });

    } else {
        AllitemDisplay();
    }

}

function itemSearchReinviteUser(searchtext) {
    console.log(searchtext);
    if (searchtext != "") {
// AllitemDisplay();
        jQuery("div.checkboxsearchuserreinvite").each(function () {
            var strsearch = jQuery(this).attr('username');
            if (strsearch.indexOf(searchtext.toLowerCase()) >= 0)
            {
                jQuery(this).show();
            } else
            {
//checkpr
// $("#checkpr"+jQuery(this).attr("id")).removeAttr('checked');
                jQuery(this).hide();
            }
        });
        //jQuery('.breakfast_step .product_items').tinyscrollbar({ thumbSize: 68 });

    } else {
        AllitemDisplay();
    }
}

//=============search all endorsement data when searched
function searchallendorsement(searchvalue) {
    var orgid = $("#endorsementorgid").val();
    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var entities = $("#entityfilter").val();
    $("#allendorsementsearching").html("");
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchallendorsement',
        data: {searchvalue: searchvalue, orgid: orgid, jobtitles: jobtitles, departments: departments, entities: entities},
        success: function (data, xhr) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
            $("#allendorsementsearching").html(data);
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        }
    });
}

//=============search all endorsement data when searched
function searchallguestendorsement(searchvalue) {
    var orgid = $("#endorsementorgid").val();
    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var entities = $("#entityfilter").val();
    $("#allendorsementsearching").html("");
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchallguestendorsement',
        data: {searchvalue: searchvalue, orgid: orgid, jobtitles: jobtitles, departments: departments, entities: entities},
        success: function (data, xhr) {
            //console.log(data); return false;
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
            $("#allendorsementsearching").html(data);
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        }
    });
}

//=============search all endorsement data when searched
function searchalldaisyendorsement(searchvalue) {
    var orgid = $("#endorsementorgid").val();
    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var entities = $("#entityfilter").val();
    $("#allendorsementsearching").html("");
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchalldaisyendorsement',
        data: {searchvalue: searchvalue, orgid: orgid, jobtitles: jobtitles, departments: departments, entities: entities},
        success: function (data, xhr) {
//            console.log(data); return false;
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
            $("#allendorsementsearching").html(data);
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        }
    });
}

//=============search all endorsement data when searched
function searchallguestendorsement(searchvalue) {
    var orgid = $("#endorsementorgid").val();
    var jobtitles = $("#jobtitlefilter").val();
    var departments = $("#departmentfilter").val();
    var entities = $("#entityfilter").val();
    $("#allendorsementsearching").html("");
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/searchallguestendorsement2',
        data: {searchvalue: searchvalue, orgid: orgid, jobtitles: jobtitles, departments: departments, entities: entities},
        success: function (data, xhr) {
//            console.log(data); return false;
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
            $("#allendorsementsearching").html(data);
            //$(data).appendTo(".table-condensed tbody");
            //$(".hiddenloader").addClass("hidden");
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        }
    });
}

var timer = 0;
var delay = (function () {
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();
var element = $("#html-content-holder"); // global variable
var getCanvas; // global variable


function printable() {
    html2canvas([document.getElementById("previewImage")], {
        logging: true,
        onrendered: function (canvas) {
            //$('#img_text').val(canvas.toDataURL("image/png"));
            $("#img_val").attr('src', canvas.toDataURL("image/png"));
            delay(function () {
                $.print("#img_val");
            }, 2000);
            //document.getElementById("myForm").submit();
        }
    });
    return false;
}

function totalendorsements() {
    var totalendorsements = "";
    $("#nodata").remove();
    if ($("#allendorsementsearching tr:visible").size() <= 0) {
        $('<tr id="nodata"><td colspan="5">No Data Available</td></tr>').appendTo("tbody#allendorsementsearching");
        totalendorsements = 0;
    } else {
        totalendorsements = $("#allendorsementsearching tr:visible").size();
    }

//    if ($(".table-striped tr:visible").size() <= 1) {
//        $('<tr id="nodata"><td colspan="5">No Data Available</td></tr>').appendTo("tbody");
//        totalendorsements = 0;
//    } else {
//        $("#nodata").remove();
//        totalendorsements = $(".table-striped tr:visible").size() - 1;
//    }
    $("#totalendorsements").html(totalendorsements);
}


function bindButtonClick() {
    $(".btn-Preview-Image").on('click', function () {
        var idtoprint = $(this).attr("rel");
        var newwindow = window.open(siteurl + 'ajax/printing/' + idtoprint, '', 'width=1500, height=1500, scrollbars=yes')
    });
}
//===function to save all endorsements as spreadsheet
function saveallendorsement(divId, orgid, information, type) {
    var spreadsheetobject = {};
    var i = 0;
    var ifAttachment = 0;
    $("#" + divId + " tr").each(function () {
        if ($(this).is(":visible") == true) {
            var abc = new Array();
            var tdlength = 1;
            $(this).children("td").each(function () {
                if ($(this).children("img").length == 1) {
                    abc.push("yes");
                } else {
                    if (($(this).text()).trim() != "" || $(this).attr("class") == "comment" || $(this).attr("class") == "attachment" || $(this).attr("class") == "emojis") {
                        abc.push($.trim($(this).text()));
                    } else {
                        abc.push("no");
                    }

                    if ($(this).attr("class") == "attachment") {
                        ifAttachment = 1;
                    }
                }
            })
            spreadsheetobject[i] = abc;
            i++;
        }
    });
    var json = JSON.stringify(spreadsheetobject);
//    var totalendorsements = $("#totalendorsements").text();
    var totalendorsements = i;
//    $("#allendorsementsearching tr").each(function () {
//        if ($(this).is(":visible") == true) {
//            var abc = '';
//            var tdlength = 1;
//            $(this).children("td").each(function () {
//                if ($(this).children("img").length == 1) {
//                    abc += "y,";
//                } else {
//                    if (($(this).text()).trim() != "") {
//                        abc += $(this).text()+",";
//                    } else {
//                        abc += "n,";
//                    }
//                }
//            })
//            spreadsheetobject[i] = abc;
//            i++;
//        }
//    });
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/saveasspreadsheetallendorsements',
        data: {orgid: orgid, information: information, spreadsheetobject: json, totalendorsements: totalendorsements, type: type, ifAttachment: ifAttachment},
        success: function (data, xhr) {
            var jsonparse = $.parseJSON(data);
            var url = siteurl + 'xlsxfolder/' + jsonparse.filename;
            window.open(url, '_self');
//            $("#samplelink")
//                .attr({
//                'download': 'export.xlsx',
//                'href': data.file,
//                'target': '_blank'
//            });
//            setTimeout(function(){
//                document.getElementById("samplelink").click();
//            },2000)

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

//===function to save all endorsements as spreadsheet
function saveallposts(divId, orgid, information, type, userSelected) {
    var startdaterandc = $("#startdaterandc").val();
    var enddaterandc = $("#enddaterandc").val();
    var DateRange = '';
    if (startdaterandc.length > 0) {
        var DateRange = '\nDuration Reported: ' + startdaterandc + " to " + enddaterandc;
    }

    var spreadsheetobject = {};
    var i = 0;
    var ifAttachment = 0;
    $("#" + divId + " tr").each(function () {
        if ($(this).is(":visible") == true) {
            var abc = new Array();
            var tdlength = 1;
            $(this).children("td").each(function () {
                if ($(this).find("img").length == 1) {
                    abc.push("Yes");
                } else {
                    console.log($(this).attr("class"));
                    if ($(this).attr("class") != "marks" && (($(this).text()).trim() != "" || $(this).attr("class") == "comment" || $(this).attr("class") == "attachment" || $(this).attr("class") == "emojis")) {
                        abc.push($.trim($(this).text()));
                    } else {
                        abc.push("No");
                    }

                    if ($(this).attr("class") == "attachment") {
                        ifAttachment = 1;
                    }
                }
            })
            spreadsheetobject[i] = abc;
            i++;
        }
    });
    var json = JSON.stringify(spreadsheetobject);
//    console.log(json); return false;
//    var totalendorsements = $("#totalendorsements").text();
    var totalendorsements = i;
//    $("#allendorsementsearching tr").each(function () {
//        if ($(this).is(":visible") == true) {
//            var abc = '';
//            var tdlength = 1;
//            $(this).children("td").each(function () {
//                if ($(this).children("img").length == 1) {
//                    abc += "y,";
//                } else {
//                    if (($(this).text()).trim() != "") {
//                        abc += $(this).text()+",";
//                    } else {
//                        abc += "n,";
//                    }
//                }
//            })
//            spreadsheetobject[i] = abc;
//            i++;
//        }
//    });
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/saveasspreadsheetallposts',
        data: {orgid: orgid, information: information, spreadsheetobject: json, totalendorsements: totalendorsements, type: type, ifAttachment: ifAttachment, userSelected: userSelected, DateRange: DateRange},
        success: function (data, xhr) {
            var jsonparse = $.parseJSON(data);
            var url = siteurl + 'xlsxfolder/' + jsonparse.filename;
            window.open(url, '_self');
//            $("#samplelink")
//                .attr({
//                'download': 'export.xlsx',
//                'href': data.file,
//                'target': '_blank'
//            });
//            setTimeout(function(){
//                document.getElementById("samplelink").click();
//            },2000)

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

var htmldata = "";
//=function to delete faq
$(document).on("click", ".deletefaq", function () {
    var idtodelete = $(this).attr("data-idfaq");
    bootbox.confirm({
        title: 'Are you sure, you want to delete?',
        message: ' ',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/faqformdelete',
                    data: {idtodelete: idtodelete},
                    success: function (data) {
                        var jsonparser = $.parseJSON(data);
                        if (jsonparser.msg == "deleted") {
                            var dataedit = jsonparser.datatoedit;
                            $("#panelid" + idtodelete).remove();
                        }
                    }
                });
            }
        }
    });
})

//=function to edit faq
$(document).on("click", ".editfaq", function () {
    var idtoedit = $(this).attr("data-idfaq");
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/faqformedit',
        data: {idtoedit: idtoedit},
        success: function (data) {
            var jsonparser = $.parseJSON(data);
            if (jsonparser.msg == "success") {
                var dataedit = jsonparser.datatoedit;
                var answer = dataedit.globalsettingFaq.answer;
                var question = dataedit.globalsettingFaq.question;
                $(".editfaq").show();
                $("#panelid" + idtoedit).find(".editfaq").hide();
                $("#faqQuestion").val(question);
                $("#faqAnswer").val(answer);
                tinyMCE.get('faqAnswer').setContent(answer);
                $(".faqsubmit .btn").attr("data-formid", idtoedit);
                //$(".editfaq").hide();
            }
        }
    });
});
//============to collect emails and mail and change table
$(document).on("click", "#reinviteemails", function () {
    var emails = {};
    var emailsflow = {};
    //var orgid = $("#orgid").val(); 
    var totalchecked = 0;
    $('#myModal_invitations .css-checkbox').each(function () {
        if ($(this).is(":checked") == true && $(this).val() != "") {
            totalchecked++;
            emails[$(this).attr("rel")] = $(this).val();
            emailsflow[$(this).attr("rel")] = $(this).attr("data-value")
        }
    })
    if (totalchecked == 0) {
        alertbootbox("Please select atleast one email address");
        return false;
    }

    var emailsflow = JSON.stringify(emailsflow);
    var emails = JSON.stringify(emails);
    var orgdetails = $("#orgdetails").val();
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/invitationsemails',
        data: {orgdetails: orgdetails, emailsflow: emailsflow, emails: emails},
        success: function (data, textStatus, xhr) {
            console.log(data);
            //$("#myModal2_deletepopup").modal("show");
            $("#myModal_invitations").modal("hide");
            //$(".modal-title").text("");
            //$(".modal-title").text("Invitations Sent");
            alertbootbox("Invitations Sent");
            //===========unchecking the selected values
            $('#myModal_invitations .css-checkbox').prop("checked", false)
        },
    });
})

//==================button for bulk users upload
$(document).on("click", "#choosefilebulkusers", function () {
    $("#bulkuserbutton").trigger("click");
});
//===============file to select n click of bulk images upload
$(document).on("click", "#choosefileexistingusers", function () {
    $("#bulkimagesbutton").trigger("click");
});
//============popup to show users profile
$(document).on("click", ".usersprofile", function () {
    var userorgid = $(this).attr("data-userorgid");
    var orgid = $(this).attr("data-orgid");
    $("#myModalViewProfile").modal("show");
    $("#formusers")[0].reset();
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/viewprofile',
        data: {userorgid: userorgid},
        success: function (data) {
            var jsonparser = $.parseJSON(data);
            if (jsonparser.msg == "success") {
                var userdetail = jsonparser.data["User"];
                $("#myModalViewProfile .modal-body form div").each(function () {
                    var formid = $(this).children(".form-control").attr("id");
                    //$("#" + formid).val("");
                    if (formid == "mobile" && userdetail["mobile_visible"] == 0) {
                        $("#mobile").parent().remove();
                        return true;
                    }
                    $("#" + formid).val(userdetail[formid]);
                    if (formid == "dob") {
                        if (userdetail[formid] == "0000-00-00") {
                            $("#" + formid).val("");
                        }
                    }

                });
                var deparmtment = jsonparser.data["OrgDepartment"].name;
                //$("#department").val("");
                $("#department").val(deparmtment);
                var jobtitle = jsonparser.data["OrgJobTitle"].title;
                //$("#jobtitle").val("");
                $("#jobtitle").val(jobtitle);
                var subcenter_name = jsonparser.data["OrgSubcenter"].long_name;
                //$("#jobtitle").val("");
                $("#subcenter").val(subcenter_name);
            }

        }
    });
});
var allendorsements = "";
//=comments section of allendrosement
function clickcomment() {
    $(document).on("click", ".comment", function () {
        var ctext = $(this).text();
        if (ctext.length > 1) {
            alertbootbox(ctext);
        }
    })
}

function selectallfunctionality(selectallbutton, classtoselect) {
    $("#" + selectallbutton).click(function () {
        if ($("#" + selectallbutton).is(":checked")) {
            $("." + classtoselect).prop("checked", true);
        } else {
            $("." + classtoselect).prop("checked", false);
        }
    })
}

//==============select functionalities  on mails
//    $("#mailingselectall").click(function () {
//        if ($("#mailingselectall").is(":checked")) {
//            $(".mailingcbclass").prop("checked", true);
//        } else {
//            $(".mailingcbclass").prop("checked", false);
//        }
//    })
//==============================================end select functionalities  on mails
var allorglisting = "";
$(document).on("keyup", "#searchannouncementsorg", function () {
    var searchvalue = trimAndLowerCaseString($(this).val());
    if (searchvalue.length >= 2) {
//delay(function () {
        $(".announcementstatus-cb div.checkbox").each(function () {
            var orgname = trimAndLowerCaseString($(this).attr("searchorg"));
            if (orgname.indexOf(searchvalue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
        //}, 1000);
    } else if (searchvalue.length < 2) {
        $(".announcementstatus-cb div.checkbox").show();
    }
})

$(document).on("keyup", "#searchannouncements", function () {
    var searchvalue = trimAndLowerCaseString($(this).val());
    if (searchvalue.length >= 2) {
//delay(function () {
        $(".mail-to-org-checkbox div.checkbox").each(function () {
            var orgname = trimAndLowerCaseString($(this).attr("searchorgannouncement"));
            if (orgname.indexOf(searchvalue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
        //}, 1000);
    } else if (searchvalue.length < 2) {
        $(".mail-to-org-checkbox div.checkbox").show();
    }
});
//Search users for announcements
$(document).on("keyup", "#searchannouncementsusers", function () {
    var searchvalue = trimAndLowerCaseString($(this).val());
    if (searchvalue.length >= 2) {
//delay(function () {
        $(".mail-to-user-checkbox div.checkbox").each(function () {
            var orgname = trimAndLowerCaseString($(this).attr("searchuserannouncement"));
            if (orgname.indexOf(searchvalue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
        //}, 1000);
    } else if (searchvalue.length < 2) {
        $(".mail-to-user-checkbox div.checkbox").show();
    }
});
//Search departments for announcements
$(document).on("keyup", "#searchannouncementsdept", function () {
    var searchvalue = trimAndLowerCaseString($(this).val());
    if (searchvalue.length >= 2) {
//delay(function () {
        $(".mail-to-dept-checkbox div.checkbox").each(function () {
            var orgname = trimAndLowerCaseString($(this).attr("searchdeptannouncement"));
            if (orgname.indexOf(searchvalue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
        //}, 1000);
    } else if (searchvalue.length < 2) {
        $(".mail-to-dept-checkbox div.checkbox").show();
    }
});
//Search sub org for announcements
$(document).on("keyup", "#searchannouncementssuborg", function () {
    var searchvalue = trimAndLowerCaseString($(this).val());
    if (searchvalue.length >= 2) {
//delay(function () {
        $(".mail-to-suborg-checkbox div.checkbox").each(function () {
            var orgname = trimAndLowerCaseString($(this).attr("searchsuborgannouncement"));
            if (orgname.indexOf(searchvalue) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
        //}, 1000);
    } else if (searchvalue.length < 2) {
        $(".mail-to-suborg-checkbox div.checkbox").show();
    }
});
$(document).ready(function () {

//=======on cliking outside modal
    $("body").click(function (event) {
        var clnew = $(event.target).attr('id');
        //=======to get a click outside this modal
        if (clnew == "myModalbulkusersimports") {
            window.location.reload();
        }
    });
    $(".closebulkimport").on("click", function () {
        window.location.reload();
    })
    if ($("#pagename").val() == "indexorganizations") {
        allorglisting = $(".containerorg").html();
    }


//============common functionality for all select all buttons

    selectallfunctionality("mailingselectall", "mailingcbclass");
    selectallfunctionality("mailingselectalluser", "mailingcbclassuser");
    selectallfunctionality("mailingselectalldept", "mailingcbclassdept");
    selectallfunctionality("mailingselectallsuborg", "mailingcbclasssuborg");
    selectallfunctionality("announcementselectall", "announcementscbclass");
    //============function to clicking on comment
    clickcomment();
    $('#bt_updateSubscription').on('hidden.bs.modal', function (e) {
// do something...
        $("#updateadminsubscriptionIndexForm").validate().resetForm();
        if ($("#bt_updateSubscription #reload").val() == 'reload') {
            window.location.reload();
        }
    })

    if ($("#pagename").val() == "liveendorsements") {
        allendorsements = $("#searchendorsement").html();
    }

//=====showing tooltip on glyphicons
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'bottom'
    });
    //=======clearing the faq question answer if open
    $("#faqQuestion").val("");
    $("#faqAnswer").val("");
    //=deleting the faq item


    //=====faq question answer submit
    $(".faqsubmit").on("click", function () {


        tinyMCE.triggerSave();
        //var x = tinyMCE.activeEditor.getContent({format: 'text'})
        var x = tinyMCE.get('faqAnswer').getContent({format: 'text'});
        var abc = x.trim();
        if (abc == "") {
            $("#faqAnswer").val("");
        }
        $("#faqSettingForm").submit();
    });
    //=validating faq submit form
    $("#faqSettingForm").validate({
        ignore: [],
        rules: {
            'data[faq][Question]': {
                required: true,
            },
            'data[faq][Answer]': {
                required: true,
            }
        },
        messages: {
            'data[faq][Question]': {
                required: "Question cannot be empty",
            },
            'data[faq][Answer]': {
                required: "Answer cannot be empty",
            }
        },
        submitHandler: function (form) {
            var question = $("#faqQuestion").val();
            var answer = $("#faqAnswer").val();
            var formsubmitvalue = $(".faqsubmit .btn").attr("data-formid");
            $("#panelid" + formsubmitvalue).remove();
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/faqformsubmit',
                data: {question: question, answer: answer, formsubmitvalue: formsubmitvalue},
                success: function (data) {
                    var jsonparser = $.parseJSON(data);
                    if (jsonparser.msg == 'success') {
                        var question = jsonparser.recorddata.question;
                        var answer = jsonparser.recorddata.answer;
                        //======since need to replace new line with br
                        //answer = answer.replace(/\n/g, "<br />");
                        $('<div id="panelid' + jsonparser.lastid + '" class="panel panel-default"></div>').insertAfter(".faqHeader");
                        $('<div class="panel-heading"><div style="position:relative; right:-10px;"><span class="glyphicon glyphicon-pencil pull-right editfaq" data-idfaq ="' + jsonparser.lastid + '"></span><span class="deletefaq pull-right glyphicon glyphicon-trash" data-toggle="tooltip" data-original-title="Delete" data-idfaq ="' + jsonparser.lastid + '"></span></div><h4 class="panel-title"> <a href="#collapse' + jsonparser.lastid + '" data-parent="#accordion" data-toggle="collapse" class="question accordion-toggle collapsed"></a></h4></div>').appendTo("#panelid" + jsonparser.lastid);
                        $('<div class="panel-collapse collapse" id="collapse' + jsonparser.lastid + '"><div class="panel-body answer"></div></div>').appendTo("#panelid" + jsonparser.lastid);
                        $("#panelid" + jsonparser.lastid + " .question").text(question);
                        $("#panelid" + jsonparser.lastid + " .answer").html(answer);
                        //=clearing the value
                        $("#faqQuestion").val("");
                        $("#faqAnswer").val("");
                        tinyMCE.get('faqAnswer').setContent("");
                        $(".faqsubmit .btn").attr("data-formid", "0");
                    } else {
                        alert(jsonparser.msg);
                        return false;
                    }
                    $(".editfaq").show();
                }
            });
        }
    });
    $("#checkout").validate({
        rules: {
            'usercount': {
//                required: true,
                range: [1, $("#js_maxSubUsers").val()],
                number: true
            }
        },
        messages: {
            'usercount': {
//                required: "User count cannot be empty",
                range: "Please enter number of users between 1 and " + $("#js_maxSubUsers").val(),
                number: "Only numbers are allowed"
            }
        }

    });
    $("#updateadminsubscriptionIndexForm").validate({
        rules: {
            'data[updateadminsubscription][userCount]': {
                required: true,
                min: 1,
                number: true
            }
        },
        messages: {
            'data[updateadminsubscription][userCount]': {
                required: "User count cannot be empty",
                min: "Enter minimum 1",
                number: "Only numbers are allowed"
            }
        }
    });
    $("#updateadminsubscriptionIndexForm").ajaxForm({
        url: siteurl + "subscription/update",
        dataType: 'json',
        beforeSubmit: function () {
            $("#bt_updateSubscription button[type=submit], input[type=submit]").prop("disabled", true);
            return $("#updateadminsubscriptionIndexForm").valid(); // TRUE when form is valid, FALSE will cancel submit
        },
        success: function (response) {
//            $("#available_quota_" + response.og).html(response.available_quota);
//            if ($(".js_poolPurchased").length) {
//                $(".js_poolPurchased").html(response.pool_purchased);
//            }
//            $(".js_updateSubscription").attr('pp', response.pool_purchased);
//            $("#bt_updateSubscription button[type=submit], input[type=submit]").prop("disabled", false);
//            $("#updateadminsubscriptionIndexForm").resetForm();
//            $("#bt_updateSubscription").modal('hide');
//            alertbootbox(response.msg);
            $("#bt_updateSubscription").modal('hide');
            if (response.success == true) {
                alertbootboxcb(response.msg, function () {
                    window.location.reload();
                });
            } else {
                alertbootbox(response.msg);
            }

        },
        error: function (msg) {
            return false;
        }
    });
    //save as spreadsheet for users endorsements page listing reports
    $(".endorsementssas").on("click", function () {
        var userid = $(this).attr("data-userid");
        var information = $(this).attr("data-information");
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/saslistingreports',
            data: {userid: userid, information: information},
            success: function (data, xhr) {
                var jsonparser = $.parseJSON(data);
                if (jsonparser.msg == "success") {
                    var url = siteurl + 'xlsxfolder/' + jsonparser.filename;
                    window.open(url, '_self');
                } else {
                    alert("Something Went Wrong");
                }
//                var jsonparse = $.parseJSON(data);
//                var url = siteurl + 'xlsxfolder/' + jsonparse.filename;
//                window.open(url, '_blank');

            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    //====reset dates on click
    $("#resetdates").on("click", function () {
        $("#startdaterandc").val("");
        $("#enddaterandc").val("");
    });
    jQuery('#searchuser').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        AllitemDisplay();
        var searchtext1 = $('#searchuser').val();
        itemSearch(searchtext1);
        //}

    });
    jQuery('#searchactiveuser').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        AllitemDisplay();
        var searchtext1 = $('#searchactiveuser').val();
        itemSearchactiveuser(searchtext1);
        //}

    });
    jQuery('#searchinactiveuser').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        AllitemDisplay();
        var searchtext1 = $('#searchinactiveuser').val();
        itemSearchinactiveuser(searchtext1);
        //}

    });
    jQuery('#searchReinviteUser').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        AllitemDisplay();
        var searchtext1 = $('#searchReinviteUser').val();
        itemSearchReinviteUser(searchtext1);
        //}

    });
    jQuery('#users').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        //alert("test"+$('#users').val());
        var price = $('#users').val() * 10.80;
        $("#amt").val(price.toFixed(2));
        //}

    });
    jQuery('#upgrade_users').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        //alert("test"+$('#users').val());
        var price = $('#upgrade_users').val() * 10.80;
        $("#upgrade_amt").val(price.toFixed(2));
        //}

    });
    jQuery('#overwrite_users').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        //alert("test"+$('#users').val());
        var price = $('#overwrite_users').val() * 10.80;
        $("#overwrite_amt").val(price.toFixed(2));
        //}

    });
    jQuery('#convertUsers').keyup(function (event) {

        var keycode = (event.keyCode ? event.keyCode : event.which);
        //if(keycode == '13'){
        //alert("test"+$('#convertUsers').val());
        var price = $(this).val() * annual_price_per_user;
        $("#convertAmt").val(price.toFixed(2));
        //}

    });
    //jQuery('#downgrade_users').keyup(function (event) {
    //
    //
    //    var searchtext1 = $('#downgrade_users').val();
    //    if (searchtext1 != "")
    //    {
    //       var all_quota_user = parseInt($("#pool_qty").val())-parseInt(searchtext1);
    //      // alert(all_quota_user + " "+$("#user_quota").val());
    //       if(all_quota_user<1){
    //        
    //        $("#suadmin_downgrade_amt").hide();
    //         $("#disp_user_option").hide();
    //         $("#downgrade_active_users1").hide();
    //        $("#select_user").val("");
    //        alert("this downgra   de subscription treated as cancel subscription");
    //        return false;
    //       }
    //       $("#suadmin_downgrade_amt").show();
    //       if(all_quota_user >= parseInt($("#user_quota").val()))
    //       {
    //        $("#disp_user_option").hide();
    //        $("#downgrade_active_users1").hide();
    //        $("#select_user").val("");
    //       }else{
    //        
    //      
    //       }
    //    }else{
    //         $("#disp_user_option").hide();
    //    }
    //
    //
    //});
    //$.print("#printable");
    //========================binding the button click of printing options
    bindButtonClick();
    $(".resetendorsementsfilters").click(function () {
        $("#jobtitlefilter").val("");
        $("#departmentfilter").val("");
        $("#entityfilter").val("");
        $(".datesubmitter").trigger("click");
        if (!$("#filter-nDorsements").is(":hidden")) {
            $("#submitfilterendorsement").trigger("click");
            $("#filter-nDorsements").removeClass("in");
        }
    });
    $(".resetUserlistFilters").click(function () {

        $("#jobtitlefilter").val("");
        $("#departmentfilter").val("");
        $("#statusFilter").val("");
        $("#usertypeFilter").val("");
//        $(".datesubmitter").trigger("click");
        if (!$("#filter-nDorsements").is(":hidden")) {
            $("#submitfilterusers").trigger("click");
            $("#filter-nDorsements").removeClass("in");
        }

    });
    //======saveas spreadsheetleaderboard
    $("#saveasspreadsheetleaderboard").on("click", function () {
        var startdate = $("#startdaterandc").val();
        var enddate = $("#enddaterandc").val();
        var orgid = $("#randcorgid").val();
        var searchvalue = $("#searchleaderboard").val();
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/leaderboardsavespreadsheet',
            data: {startdate: startdate, enddate: enddate, orgid: orgid, searchvalue: searchvalue},
            success: function (data, xhr) {
//                console.log(data);
//                return false;
                var jsonparser = $.parseJSON(data);
                if (jsonparser.result == "success") {
                    var url = siteurl + 'xlsxfolder/' + jsonparser.filename;
                    window.open(url, "_self");
                } else {
                    alert("Something went wrong");
                }
//                var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(jsonparser.contentob);
//                $("#samplelink")
//                        .attr({
//                            'download': jsonparser.filename + '.csv',
//                            'href': csvData,
//                            //'target': '_blank'
//                        });
//                setTimeout(function () {
//                    document.getElementById("samplelink").click();
//                }, 2000)

            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    //======save as spreadsheetleaderboard NEW
    $("#saveasspreadsheetleaderboard-new").on("click", function () {

        $(document).find("#saveasspreadsheetleaderboard-new").prop("disabled", true);
        $(document).find("#export-loader-img").show();
        var startdate = $("#startdaterandc_1").val();
        var enddate = $("#enddaterandc_1").val();
        var orgid = $("#randcorgid").val();
        var searchvalue = $("#searchleaderboard").val();
        var selectedSubcenterId = $("#selectedSubcenterId").val();
        var selectedDepartmentId = $("#selectedDepartmentId").val();
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/leaderboardsavespreadsheetNew',
            data: {startdate: startdate, enddate: enddate, orgid: orgid, searchvalue: searchvalue, subcenterid: selectedSubcenterId, departmentid: selectedDepartmentId},
            success: function (data, xhr) {
//                console.log(data);
//                return false;
                var jsonparser = $.parseJSON(data);
                if (jsonparser.result == "success") {
                    var url = siteurl + 'xlsxfolder/' + jsonparser.filename;
                    window.open(url, "_self");
                    $(document).find("#export-loader-img").hide();
                    $(document).find("#saveasspreadsheetleaderboard-new").prop("disabled", false);
                } else {
                    alert("Something went wrong");
                    $(document).find("#export-loader-img").hide();
                    $(document).find("#saveasspreadsheetleaderboard-new").prop("disabled", false);
                }
//                var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(jsonparser.contentob);
//                $("#samplelink")
//                        .attr({
//                            'download': jsonparser.filename + '.csv',
//                            'href': csvData,
//                            //'target': '_blank'
//                        });
//                setTimeout(function () {
//                    document.getElementById("samplelink").click();
//                }, 2000)

            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    });
    $("#submitfilterendorsement").click(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        var startdate = $("#startdaterandc").val();
        var enddate = $("#enddaterandc").val();
        var orgid = $("#endorsementorgid").val();
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/filterallendorsement',
            data: {jobtitles: jobtitles, departments: departments, entities: entities, orgid: orgid, startdate: startdate, enddate: enddate},
            beforeSend: function () {
                $(".search-icn").addClass("search-loader").removeClass('search-icn');
            },
            success: function (data, xhr) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
                $("#allendorsementsearching").html(data);
                totalendorsements();
                //$(data).appendTo(".table-condensed tbody");
                //$(".hiddenloader").addClass("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
            }
        });
    });
    $("#submitdaisyfilterendorsement").click(function () {
//        alert("TEST");
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        var startdate = $("#startdaterandc").val();
        var enddate = $("#enddaterandc").val();
        var orgid = $("#endorsementorgid").val();
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/filteralldaisyendorsement',
            data: {jobtitles: jobtitles, departments: departments, entities: entities, orgid: orgid, startdate: startdate, enddate: enddate},
            beforeSend: function () {
                $(".search-icn").addClass("search-loader").removeClass('search-icn');
            },
            success: function (data, xhr) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
                $("#allendorsementsearching").html(data);
                totalendorsements();
                //$(data).appendTo(".table-condensed tbody");
                //$(".hiddenloader").addClass("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
            }
        });
    });
    $("#submitguestfilterendorsement").click(function () {
//        alert("TEST");
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        var startdate = $("#startdaterandc").val();
        var enddate = $("#enddaterandc").val();
        var orgid = $("#endorsementorgid").val();
        $.ajax({
            type: "POST",
            dataType: 'html',
            url: siteurl + 'ajax/filterallguestendorsement',
            data: {jobtitles: jobtitles, departments: departments, entities: entities, orgid: orgid, startdate: startdate, enddate: enddate},
            beforeSend: function () {
                $(".search-icn").addClass("search-loader").removeClass('search-icn');
            },
            success: function (data, xhr) {
//                console.log(data); return false;
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
                $("#allendorsementsearching").html(data);
                totalendorsements();
                //$(data).appendTo(".table-condensed tbody");
                //$(".hiddenloader").addClass("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
            }
        });
    })


    $("#submitfilterpost").click(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        var startdate = $("#startdaterandc").val();
        var enddate = $("#enddaterandc").val();
        var orgid = $("#endorsementorgid").val();
        var reportType = $("input[name='report_type']:checked").val();
        var UserId = $("#daterangerandcUserId").val();
        console.log("jobtitles : " + jobtitles + " departments : " + departments + " entities : " + entities + " startdate : " + startdate + " enddate : " + enddate + " orgid :" + orgid + " reportType :" + reportType + " UserId :" + UserId);
//        return false;

        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/filterallpost',
            data: {jobtitles: jobtitles, departments: departments, entities: entities, orgid: orgid, startdate: startdate, enddate: enddate, reporttype: reportType, Userid: UserId},
            beforeSend: function () {
                $(".search-icn").addClass("search-loader").removeClass('search-icn');
            },
            success: function (data, xhr) {
                //console.log(data);return false;
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
                $("#allendorsementsearching").html(data);
                totalendorsements();
                //$(data).appendTo(".table-condensed tbody");
                //$(".hiddenloader").addClass("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $(".search-loader").addClass("search-icn").removeClass('search-loader');
            }
        });
    })


    function searchendorsementruntime() {
        var searchvalue = $("#searchallendorsement").val();
        $(".table-striped tbody tr").each(function () {
            if (searchvalue == "") {
                $(this).show();
            } else {
                searchvalue = trimAndLowerCaseString(searchvalue);
                var username = trimAndLowerCaseString($(this).attr("username"));
                if (typeof (username) != "undefined") {
                    username = username.toLowerCase();
                    if (username.indexOf(searchvalue) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }

            }
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        })
        totalendorsements();
    }

//===fetch value after a delay to send request less than the desired requests
    $('#searchallendorsement').keyup(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        if (jobtitles == null && departments == null && entities == null) {
            $(".search-icn").addClass("search-loader").removeClass('search-icn');
            delay(function () {
                searchallendorsement($("#searchallendorsement").val());
            }, 1000);
        }
//        $(".search-icn").addClass("search-loader").removeClass('search-icn');
//        delay(function () {
//            searchpostruntime();
//        }, 1000);
        /*}*/
    });
    //===fetch value after a delay to send request less than the desired requests
    $('#searchallguestendorsement').keyup(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        if (jobtitles == null && departments == null && entities == null) {
            $(".search-icn").addClass("search-loader").removeClass('search-icn');
            delay(function () {
                searchallguestendorsement($("#searchallguestendorsement").val());
            }, 1000);
        }
//        $(".search-icn").addClass("search-loader").removeClass('search-icn');
//        delay(function () {
//            searchpostruntime();
//        }, 1000);
        /*}*/
    });
    //===fetch value after a delay to send request less than the desired requests
    $('#searchalldaisyendorsement').keyup(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        if (jobtitles == null && departments == null && entities == null) {
            $(".search-icn").addClass("search-loader").removeClass('search-icn');
            delay(function () {
                searchalldaisyendorsement($("#searchalldaisyendorsement").val());
            }, 1000);
        }
//        $(".search-icn").addClass("search-loader").removeClass('search-icn');
//        delay(function () {
//            searchpostruntime();
//        }, 1000);
        /*}*/
    });
    //===fetch value after a delay to send request less than the desired requests
    $('#searchallguestendorsement').keyup(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        if (jobtitles == null && departments == null && entities == null) {
            $(".search-icn").addClass("search-loader").removeClass('search-icn');
            delay(function () {
                searchallguestendorsement($("#searchallguestendorsement").val());
            }, 1000);
        }
//        $(".search-icn").addClass("search-loader").removeClass('search-icn');
//        delay(function () {
//            searchpostruntime();
//        }, 1000);
        /*}*/
    });
    /***** Code to search run time from all post added by babulal prasad @23-nov-2017 ***/
    function searchpostruntime() {
        var searchvalue = $("#searchallpost").val();
        $(".table-striped tbody tr").each(function () {
            if (searchvalue == "") {
                $(this).show();
            } else {
                searchvalue = trimAndLowerCaseString(searchvalue);
                var username = trimAndLowerCaseString($(this).attr("username"));
                if (typeof (username) != "undefined") {
                    username = username.toLowerCase();
                    if (username.indexOf(searchvalue) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }

            }
            $(".search-loader").addClass("search-icn").removeClass('search-loader');
        })
        totalendorsements();
    }

    //===fetch value after a delay to send request less than the desired requests
    $('#searchallpost').keyup(function () {
        var jobtitles = $("#jobtitlefilter").val();
        var departments = $("#departmentfilter").val();
        var entities = $("#entityfilter").val();
        /*if (jobtitles == null && departments == null && entities == null) {
         $(".search-icn").addClass("search-loader").removeClass('search-icn');
         delay(function () {
         searchallendorsement($("#searchallendorsement").val());
         }, 1000);
         }*/
        $(".search-icn").addClass("search-loader").removeClass('search-icn');
        delay(function () {
            searchpostruntime();
        }, 1000);
        /*}*/
    });
    /****** END Runtime post search code ***/


    $(window).scroll(function () {
        var scrollHeight = $(window).scrollTop() + $(window).height() + parseInt(300);
//        console.log("scrollHeight : " + scrollHeight);
//        console.log("height : " + $(document).height());
//        console.log("req_sent : " + req_sent);
        if (parseInt(scrollHeight) > parseInt($(document).height())) {
            if (req_sent == false) {
                req_sent = true;
                console.log($("#pagename").val());
                if ($("#pagename").val() == "indexusers") {
                    //===finding te total data and create offet accordingly
                    var searchvalue = $("#searchorgowners").val();
                    var totalrecords = $(".tableusersindex tr").length - 1;
                    if ($("#totaluserrecords").val() <= totalrecords) {
                        $(".hiddenloader").remove();
                        return false;
                    } else {
                        $(".hiddenloader").removeClass("hidden");
                    }
                    setTimeout(function () {
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'ajax/loadmoreajax',
                            data: {totalrecords: totalrecords, searchvalue: searchvalue},
                            success: function (data, xhr) {
                                req_sent = false;
                                if (data == "") {
                                    $(".hiddenloader").remove();
                                }
                                $("#divloader").hide();
                                $(data).appendTo(".table-condensed tbody");
                                $(".hiddenloader").addClass("hidden");
                            }
                        });
                    }, 1000)
                } else if ($("#pagename").val() == "userslisting") {
                    var searchkeyword = $("#searchkeyword").val();
                    var jobtitles = $("#jobtitlefilter").val();
                    var departments = $("#departmentfilter").val();
                    var status = $("#statusFilter").val();
                    var usertype = $("#usertypeFilter").val();
                    var totalrecords = $("#mytable tr").length - 1;
                    console.log(totalrecords);
                    var orgid = $("#orgid").val();
                    if ($("#totalrecords").val() <= totalrecords) {
                        $(".hiddenloader").remove();
                        return false;
                    } else {
                        $(".hiddenloader").removeClass("hidden");
                    }
                    $("#totalrecords").remove();
                    setTimeout(function () {
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'ajax/loadmoreusers',
                            data: {totalrecords: totalrecords, orgid: orgid, searchkeyword: searchkeyword, jobtitles: jobtitles, status: status, usertype: usertype},
                            success: function (data, xhr) {
                                req_sent = false;
                                $('#mytable').trigger('update');
                                if (data == "") {
                                    $(".hiddenloader").remove();
                                    return false;
                                }
                                $(data).appendTo("#mytable tbody");
                                $("#divloader").hide();
                                $(".hiddenloader").addClass("hidden");
                            }
                        });
                    }, 1000)
                } else if ($("#pagename").val() == "indexorganizations") {
                    var searchkeyword = $("#searchorganization").val();
                    var orgType = $("input[name=orgtype]:checked").val();
                    var totalrecords = $(".containerorg .row-padding").length;
                    console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
                    if ($("#totalrecords").val() <= totalrecords) {
                        $(".hiddenloader").remove();
                        return false;
                    } else {
                        $(".hiddenloader").removeClass("hidden");
                    }
                    setTimeout(function () {
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'ajax/loadmoreorganizations',
                            data: {totalrecords: totalrecords, searchkeyword: searchkeyword, orgType: orgType},
                            success: function (data, xhr) {
                                req_sent = false;
                                if (data == "") {
                                    $(".hiddenloader").remove();
                                    return false;
                                }
                                $(data).appendTo(".containerorg");
                                $(".hiddenloader").addClass("hidden");
                            }
                        });
                    }, 1000)
                } else if ($("#pagename").val() == "liveendorsements") {
                    var searchkeyword = $("#searchliveendorsements").val();
                    var totalrecords = $("#searchendorsement section").length;
                    var orgid = $("#endorsementorgid").val();
                    if (totalrecords > 0) {
                        $(".hiddenloader").removeClass("hidden");
                        if (searchkeyword != "") {
//                        setTimeout(function () {
//                            $.ajax({
//                                type: "POST",
//                                url: siteurl + 'ajax/searchendorsement',
//                                data: {searchvalue: searchkeyword, orgid: orgid, totalrecords: totalrecords},
//                                success: function (data, xhr) {
//                                    if (data == "") {
//                                        $(".hiddenloader").remove();
//                                        return false;
//                                    }
//                                    $(data).appendTo("#searchendorsement");
//                                    $(".hiddenloader").addClass("hidden");
//                                    req_sent = false;
//                                }
//                            });
//                        });
                            var endorsementfor = livesearchdataobject["endorsementfor"];
                            var endorsementid = livesearchdataobject["endorsementid"];
                            $.ajax({
                                type: "POST",
                                url: siteurl + 'ajax/searchendorsementfiltered',
                                data: {orgid: orgid, endorsementfor: endorsementfor, endorsementid: endorsementid, totalrecords: totalrecords},
                                success: function (data, xhr) {
                                    if (data == "") {
                                        req_sent = false;
                                        $(".hiddenloader").remove();
                                        return false;
                                    }
                                    $("#searchendorsement").append(data);
                                    //$(".search-loader").addClass("search-icn").removeClass('search-loader');
                                },
                                error: function (jqXHR, textStatus, errorThrown) {

                                }
                            });
                        } else {
                            if ($("#totalrecords").val() <= totalrecords) {
                                $(".hiddenloader").remove();
                                return false;
                            } else {
                                $(".hiddenloader").removeClass("hidden");
                            }
                            setTimeout(function () {
                                $.ajax({
                                    type: "POST",
                                    url: siteurl + 'ajax/loadmoreliveendorsements',
                                    data: {totalrecords: totalrecords, searchkeyword: searchkeyword, orgid: orgid},
                                    success: function (data, xhr) {
                                        req_sent = false;
                                        if (data == "") {
                                            $(".hiddenloader").remove();
                                            return false;
                                        }
                                        $(data).appendTo("#searchendorsement");
                                        $(".hiddenloader").addClass("hidden");
                                    }
                                });
                            });
                        }
                    }
                } else if ($("#pagename").val() == "guestendorsements") { //Added By Babulal Prasad @29-may-2018 to scroll down to loadmore guest nDorsements

                    var listStatus = $(".statusbttn.active").attr("data-value");
                    var totalrecords = $("#searchendorsement section").length;
                    var orgid = $("#org_id").val();
                    console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
                    console.log("orgid :" + orgid);
                    console.log("listStatus :" + listStatus);
                    return false;
                    if ($("#totalrecords").val() <= totalrecords) {
                        $(".hiddenloader").remove();
                        return false;
                    } else {
                        $(".hiddenloader").removeClass("hidden");
                    }
                    setTimeout(function () {
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'ajax/loadmoreguestndorsements',
                            data: {totalrecords: totalrecords, status: listStatus, orgId: orgid},
                            success: function (data, xhr) {
                                req_sent = false;
                                if (data == "") {
                                    $(".hiddenloader").remove();
                                    return false;
                                }
                                $(data).appendTo("#searchendorsement");
                                $(".hiddenloader").addClass("hidden");
                            }
                        });
                    }, 1000)
                } else if ($("#pagename").val() == "adsettings") { //Added By Babulal Prasad @29-aug-2019 to scroll down to loadmore Active directory users
                    console.log('WRITE CODE HERE FOR PAGINATION!');
//                    var listStatus = $(".statusbttn.active").attr("data-value");
//                    var totalrecords = $("#searchendorsement section").length;
//                    var orgid = $("#org_id").val();
//
//                    console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
//                    console.log("orgid :" + orgid);
//                    console.log("listStatus :" + listStatus);
//                    return false;
//
//
//                    if ($("#totalrecords").val() <= totalrecords) {
//                        $(".hiddenloader").remove();
//                        return false;
//                    } else {
//                        $(".hiddenloader").removeClass("hidden");
//                    }
//                    setTimeout(function () {
//                        $.ajax({
//                            type: "POST",
//                            url: siteurl + 'ajax/loadmoreguestndorsements',
//                            data: {totalrecords: totalrecords, status: listStatus, orgId: orgid},
//                            success: function (data, xhr) {
//                                req_sent = false;
//                                if (data == "") {
//                                    $(".hiddenloader").remove();
//                                    return false;
//                                }
//                                $(data).appendTo("#searchendorsement");
//                                $(".hiddenloader").addClass("hidden");
//                            }
//                        });
//                    }, 1000)
                }
            }
        }

    });
    //Added By Babulal Prasad @29-may-2018 //To filter guest nDorsement result
    $(".statusbttn").click(function () {
        if ($("#pagename").val() == "guestendorsements") {
            var listStatus = $(this).attr("data-value");
            var statusClass = $(this).attr("data-name");
            var totalrecords = $("#searchendorsement section").length;
            var orgid = $("#org_id").val();
            console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
            console.log("orgid :" + orgid);
            console.log("listStatus :" + listStatus);
//        return false;
            $(".statusbttn").attr("disabled", false);
            $("." + statusClass).attr("disabled", true);
            $(".heading_status_type").html(statusClass);
            $("#disclaimerText").html(statusClass);
            $(".hiddenloader").removeClass("hidden");
            if (siteurl.indexOf('staging') > -1) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("https", "http");
                }
            }

            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/loadmoreguestndorsements',
                    data: {status: listStatus, orgId: orgid},
                    success: function (data, xhr) {
                        req_sent = false;
                        if (data == "") {
                            $(".hiddenloader").remove();
                            return false;
                        }
                        $("#searchendorsement").html(data);
                        $(".hiddenloader").addClass("hidden");
                    },
                    fail: function (data, xhr) {
                        $("." + statusClass).attr("disabled", false);
                        $(".hiddenloader").addClass("hidden");
                    }
                });
            }, 1000)
        } else if ($("#pagename").val() == "daisyendorsements") {
            var listStatus = $(this).attr("data-value");
            var statusClass = $(this).attr("data-name");
            var totalrecords = $("#searchendorsement section").length;
            var orgid = $("#org_id").val();
            console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
            console.log("orgid :" + orgid);
            console.log("listStatus :" + listStatus);
//        return false;
            $(".statusbttn").attr("disabled", false);
            $("." + statusClass).attr("disabled", true);
            $(".heading_status_type").html(statusClass);
            $("#disclaimerText").html(statusClass);
            $(".hiddenloader").removeClass("hidden");
            if (siteurl.indexOf('staging') > -1) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("https", "http");
                }
            }

            setTimeout(function () {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/loadmoredaisyndorsements',
                    data: {status: listStatus, orgId: orgid},
                    success: function (data, xhr) {
                        req_sent = false;
                        if (data == "") {
                            $(".hiddenloader").remove();
                            return false;
                        }
                        $("#searchendorsement").html(data);
                        $(".hiddenloader").addClass("hidden");
                    },
                    fail: function (data, xhr) {
                        $("." + statusClass).attr("disabled", false);
                        $(".hiddenloader").addClass("hidden");
                    }
                });
            }, 1000)
        }
    });
    //===============validating new password form and force submit
    $("#passwordresetsubmit").click(function () {
        $("#userresetInfoForm").submit();
    })
    //===============validating new password form
    $("#userresetInfoForm").validate({
        rules: {
            'data[userreset][New Password]': {
                required: true,
                minlength: 8,
            },
            'data[userreset][Confirm Password]': {
                required: true,
                equalTo: '#Usernewpassword'
            }
        },
        messages: {
            'data[userreset][New Password]': {
                required: "Password cannot be empty",
                minlength: "Password Must be of atleast 8 characters    "
            },
            'data[userreset][Confirm Password]': {
                required: "Confirm Password cannot be empty",
                equalTo: "Password and confirm password do not match"
            }
        },
        submitHandler: function (form) {
            var fname = $("#username").text();
            var uid = $("#uid").text();
            var newpassword = $("#Usernewpassword").val();
            var orgname = $("#orgname").text();
            var uemail = $("#uemail").text();
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/resetuserpassword',
                data: {fname: fname, uid: uid, npassword: newpassword, orgname: orgname, uemail: uemail},
                success: function (data) {
                    $("#Usernewpassword").val("");
                    $("#Userconfirmpassword").val("");
                    $("#resetpassworduser").modal("hide");
                    alertbootbox("Password Reset Successfully");
                }
            });
        }

    });
    //===============faq form
    $("#faqbutton").click(function () {
        $("#faqform").validate({
            rules: {
                'data[Name]': {
                    required: true,
                },
                'data[Email]': {
                    required: true,
                    email: true,
                },
                'data[Subject]': {
                    required: true,
                },
                'data[Message]': {
                    required: true,
                },
            },
            messages: {
                'data[Name]': {
                    required: "Name Cannot Be Blank.",
                },
                'data[Email]': {
                    required: "Email Cannot Be Blank.",
                    email: "Email is Not Valid.",
                },
                'data[Subject]': {
                    required: "Subject Cannot Be Blank.",
                },
                'data[Message]': {
                    required: "Message Cannot Be Blank.",
                },
            },
            submitHandler: function (form) {
//                $(".main").addClass("disabletouches");
//                var name = $("#Name").val();
//                var email = $("#Email").val();
//                var subject = $("#Subject").val();
//                var msg = $("#Message").val();
//                $.ajax({
//                    type: "POST",
//                    url: siteurl + 'ajax/submitfaqform',
//                    data: {name: name, email: email, subject: subject, msg: msg},
//                    success: function (data) {
//                        $("#Name").val("");
//                        $("#Email").val("");
//                        $("#Subject").val("");
//                        msg = $("#Message").val("");
//                        //$("#myModal2_commonpopupmessage").modal("show");
//                        //$("#myModal2_commonpopupmessage .modal-title").text("Message Sent successfully, We will contact you soon");
//                        alertbootbox("Message Sent successfully, We will contact you soon");
//                        $(".main").removeClass("disabletouches");
//                    }
//                });
//                return false;

                var name = $.trim($("#Name").val());
                var email = $.trim($("#Email").val());
                var subject = $.trim($("#Subject").val());
                var message = $.trim($("#Message").val());
                $("#contactussubmit").attr("disabled", "disabled");
                $("#contactussubmit").html('Sending <i class="fa fa-refresh fa-spin"></i>')
                $.ajax({
                    type: "POST",
                    url: siteurl + 'cajax/StaticFormContactus',
                    data: {name: name, email: email, subject: subject, message: message},
                    success: function (data, xhr) {
                        $("#contactussubmit").prop("disabled", false);
                        $("#contactussubmit").html('Send');
                        if (data == "Success") {
                            $('#faqform')[0].reset();
                            msg = "Your request is successfully submitted. We will contact you soon.";
                        } else {
                            msg = "Something went wrong."
                        }
                        bootbox.alert({
                            closeButton: false,
                            "message": msg,
                            "className": "bootboxalertclass",
                            "callback": function () {
                                console.log("successfull");
                            }
                        });
//                var jsonparse = $.parseJSON(data);
//                var url = siteurl + 'xlsxfolder/' + jsonparse.filename;
//                window.open(url, '_blank');

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                    }
                });
                return false;
            }

        });
    })
    //===============validating new password form




    //===============on select all functionality
    $("#myModal_invitations #check").click(function () {
        if ($("#myModal_invitations #check").is(":checked") == true) {
            $("#myModal_invitations .css-checkbox").prop("checked", true);
        } else {
            $("#myModal_invitations .css-checkbox").prop("checked", false);
        }
    })

    $("#myModal_pendingrequest #checkpr").click(function () {
        if ($("#myModal_pendingrequest #checkpr").is(":checked") == true) {
            $("#myModal_pendingrequest .css-checkbox").prop("checked", true);
        } else {
            $("#myModal_pendingrequest .css-checkbox").prop("checked", false);
        }
    })




    $("#mytable").tablesorter();
    $('#leaderboardtable').tablesorter();
    $('#mytable th').click(function () {
        //========to fixing the role of other than the one clicked
        if ($(this).attr("id") == "role") {
            $('th').attr("id", "status").find(".statusdown").show();
            $('th').attr("id", "status").find(".statusup").hide();
        } else {
            $('th').attr("id", "role").find(".statusdown").show();
            $('th').attr("id", "role").find(".statusup").hide();
        }
        if ($(this).hasClass("headerSortDown") == true) {
            $(this).find(".statusup").show();
            $(this).find(".statusdown").hide();
        } else {
            $(this).find(".statusdown").show();
            $(this).find(".statusup").hide();
        }
    });
    //========function to click out side the menu to close menu
    $("body").click(function (event) {
        var clnew = $(event.target).attr('class');
        if (clnew != "img-responsive" && clnew != "sidebar-nav" && clnew != "logout") {
            $("div#wrapper").removeClass("toggled");
        }
    });
    //clicking outside will close the arrow box
    $(document).mouseup(function (e) {
        var container = $(".arrow_box");
        var clnew = $(e.target).attr('src');
        if (!clnew && !container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
    //=============on cancel of modal of delete button
    $(document).on("click", ".canceldelete", function () {
        $('.close').trigger('click');
    });
    //===============to go one page back with cancel
    $(document).on("click", "#clientformcancel, #orgformcancel", function () {
        window.history.back();
    })
    //===============binding username with email id 
    $('#UserEmail').bind('keypress keyup blur', function () {
        $('#UserUsername').val($(this).val());
    });
    //================on blur check email
    $('#UserCreateendorserForm #UserEmail').blur(function () {
        var email = $("#UserEmail").val();
        console.log(email);
        var orgid = $("#orgidendorser").val();
        $.ajax({
            type: 'POST',
            url: siteurl + 'ajax/findendorser',
            data: {targetemail: email, orgid: orgid},
            success: function (data, textStatus, xhr) {
//                console.log(data);
                var jsonresult = $.parseJSON(data);
//                console.log("TEST");
//                console.log(jsonresult); return false;

                var userdata = jsonresult[0]["User"];
                var userorgdata = jsonresult[0]["UserOrganization"];
                if (userorgdata != "") {
                    alert("User Already Exist in your list");
                    //pushing back to back page when users exist;
                    $("#UserEmail").val("");
                    $("#UserUsername").val("");
                    return false;
                }
                if (userdata != "") {
                    alert("User Already Exist, Fill the Remaining values");
                    $("#fnamelname").remove();
                    $("#dobmobile").remove();
                    $("#skillshobbies").remove();
                    $("#countrystate").remove();
                    $("#citystreet").remove();
                    $("#zip").remove();
                    $("#about").remove();
                    $("#passwordcpassword").remove();
//                    $("#UserFname").val(userdata["fname"]);
//                    $("#UserFname").attr("readonly", "readonly");
//                    $("#UserLname").val(userdata["lname"]);
//                    $("#UserLname").attr("readonly", "readonly");
//                    $("#datepicker_dob").val(userdata["dob"]);
//                    $("#datepicker_dob").attr("readonly", "readonly");
//                    $("#UserMobile").val(userdata["mobile"]);
//                    $("#UserMobile").attr("readonly", "readonly");
                    console.log(userdata["skills"]);
                    //$("#UserMobile").val(userdata["skills"]);

                } else {
                    return false;
                }
            },
        });
    });
    //===============datepicker for client form
    $("#datepicker_dob").datepicker(dateparameters);
    $('.datepickerrandc').each(function () {
        $(this).datepicker(dateparameters);
    });
    //=================on clicking of refresh
    $(document).on('click', '#refresh', function () {
        window.location.reload();
    });
    //==================onclick choose file for bulk links import
    $(document).on('click', '#choosefile_bulklinks', function () {
        $('#uploadlinksfile').trigger('click');
    });
    //=================check file type for bulk user import
    $(document).on('change', '#bulkuserbutton, #bulkimagesbutton', function () {
        var buttonid = $(this)[0]["id"];
        var filename = $(this).val();
        //=====splitting path from file name
        var Fname = filename.replace(/^.*[\\\/]/, '')
        var splittedFile = filename.split('.');
        var FileExt = filename.split('.').pop(-1);
        var fileType = splittedFile[1];
//        var fileType = fileType.toLowerCase();
        var fileType = FileExt.toLowerCase();
        if (fileType != 'csv') {
            alert("Not a Valid Format");
            $(this).val("");
            $('#uploadedfile').text("");
            return false;
        } else {
            if (buttonid == "bulkimagesbutton") {
                $("#uploadedimagesfile").remove();
                $('<div class="clearfix"></div><p id="uploadedimagesfile" class="truncate">' + Fname + '</p>').insertAfter('#ToolTip01');
            } else {
                $("#uploadedfile").remove();
                $('<div class="clearfix"></div><p id="uploadedfile" class="truncate">' + Fname + '</p>').insertAfter('#ToolTip02');
            }
        }
    });
    //=================check file type for bulk links import
    $(document).on('change', '#uploadlinksfile', function () {
        var filename = $(this).val();
        var splittedFile = filename.split('.');
        var fileType = splittedFile[1];
        var fileType = fileType.toLowerCase();
        if (fileType != 'csv') {
            alert("Not a Valid Format");
            $(this).val("");
            $('#uploadedfile').text("");
            return false;
        } else {
            $('<p id="uploadedlinksfile">' + $(this).val() + '</p>').insertAfter('#uploadfile_bulklinks');
        }
    });
    //========states filter by country name by ajax saurabh
    $(".country").change(function () {

        var optionSelected = $("option:selected", this);
        var countryId = this.value;
        country_id = countryId;
        var url = $(this).attr('data-url');
        $.ajax({
            url: url,
            data: {
                countryId: country_id
            },
            type: "POST",
            success: function (response) {

                if (response == "")
                {
                    $('.states').html('');
                    $('#selectstate').hide();
                    $('#selectstatetext').show();
                } else {
                    $('.states').html('');
                    $('#state_name').val('');
                    $('#selectstatetext').hide();
                    $('#selectstate').show();
                    optionsHtml = '<option value="">' + 'Select' + '</option>';
                    console.log(response);
                    r = response;
                    for (countryId in response) {
                        optionsHtml += '<option value="' + response[countryId] + '">' + response[countryId] + '</option>';
                    }
                    $('.states').html(optionsHtml);
                }
            }
        });
    });
    $('#client_upload_photo').bind("click", function () {
        $('#photo').click();
    });
    $("#org_remove_photo").click(function () {
        var current_image = $('#org_image_name').val();
        if (current_image == "") {
            $("#img_msg").html("No image selected");
        } else {
            $.ajax({
                url: siteurl + 'users/deleteorgimage',
                type: "POST",
                data: {image_name: current_image},
                success: function (data, textStatus, jqXHR) {
                    var parseData = JSON.parse(data);
                    if (parseData.status) {
                        //$("#img_msg").html("image has been removed successfully");
                        $('#org_image').attr('src', siteurl + parseData.imageloc);
                        $('#org_image_name').val("");
                        $('#photo').val("");
                    } else {
                        $("#img_msg").html(parseData.error);
                        // buttonclick.removeAttr('disabled');
                        return false;
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });
    // end
    // remove photo

    $("#client_remove_photo").click(function () {
        var current_image = $('#client_image_name').val();
        if (current_image == "")
        {
            $("#img_msg").html("No image selected");
        } else
        {
            $.ajax({
                url: siteurl + 'users/deleteimage',
                type: "POST",
                data: {image_name: current_image},
                success: function (data, textStatus, jqXHR) {
                    //data - response from server
                    //window.location = siteurl+'courses/progress';
                    var parseData = JSON.parse(data);
                    if (parseData.status) {

                        //$("#img_msg").html("image has been removed successfully");
                        //alert(parseData.imageloc);
                        $('#client_image').attr('src', siteurl + parseData.imageloc);
                        $('#client_image_name').val("");
                        $('#photo').val("");
                    } else {
                        $("#img_msg").html(parseData.error);
                        // buttonclick.removeAttr('disabled');
                        return false;
                    }
                }, error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }
    });
    $("#PostViewForm").validate({
        rules: {
            'data[Post][content]': {
                required: true
            }
            ,
        },
        messages: {
            'data[Post][content]': {
                required: "content is required"
            }
        }

    });
    $("#PostAddForm").validate({
        rules: {
            'data[Post][content]': {
                required: true
            },
        },
        messages: {
            'data[Post][content]': {
                required: "content is required"
            }
        }

    });
    $("#TopicAddForm").validate({
        rules: {
            'data[Topic][name]': {
                required: true
            },
            'data[Topic][content]': {
                required: true,
            },
        },
        messages: {
            'data[Topic][name]': {
                required: "Title is required"
            },
            'data[Topic][content]': {
                required: "content is required"
            }
        }
    });
    //Create New Client validations 
    $("#UserCreateclientForm").validate({
        errorPlacement: function (error, element) {
            if (element.attr("name") == "data[User][role]") {
                error.insertAfter("#userrole");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            },
            'data[User][fname]': {
                required: true,
            },
            'data[User][lname]': {
                required: true,
            },
            'data[User][role]': {
                required: true,
            },
//            'data[User][password]': {
//                required: true,
//                minlength: 8,
//            },
            'data[User][confirm_password]': {
                equalTo: '#UserPassword'
            },
        },
        messages: {
            'data[User][firstname]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][fname]': {
                required: "First Name is required",
            },
            'data[User][lname]': {
                required: "Last Name is required",
            },
            'data[User][role]': {
                required: "Please select role",
            },
//            'data[User][password]': {
//                required: "Password is required",
//            },
            'data[User][confirm_password]': {
                equalTo: "Confirm Password do not match",
            },
        }

    });
    //ends here

    //Create New Client validations 
    $("#UserEditclientForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            },
            'data[User][fname]': {
                required: true,
            },
            'data[User][lname]': {
                required: true,
            },
            'data[User][changepassword]': {
                minlength: 8,
            },
            'data[User][changepassword_confirm_password]': {
                equalTo: '#UserChangepassword',
            },
        },
        messages: {
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][fname]': {
                required: "First Name is required",
            },
            'data[User][lname]': {
                required: "Last Name is required",
            },
            'data[User][changepassword]': {
                minlength: "Atleast 8 characters are required",
            },
            'data[User][changepassword_confirm_password]': {
                equalTo: "Confirm Password do not match",
            },
        }
    });
    //Create New Client validations //Added by Babulal Prasad @04-june-2018
    $("#UserEditcuserForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            },
            'data[User][fname]': {
                required: true,
            },
            'data[User][lname]': {
                required: true,
            },
            'data[User][mobile]': {
                required: true,
                digits: true,
            },
//            'data[User][street]': {
//                required: true,
//            },
//            'data[User][city]': {
//                required: true,
//            },
//            'data[User][zip]': {
//                required: true,
//            },
//            'data[User][country]': {
//                required: true,
//            },
//            'data[User][state]': {
//                required: true,
//            },
        },
        messages: {
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][fname]': {
                required: "First Name is required",
            },
            'data[User][lname]': {
                required: "Last Name is required",
            },
            'data[User][mobile]': {
                required: "Mobile number required",
                digits: "only number allowed",
            },
//            'data[User][street]': {
//                required: "Street name required",
//            },
//            'data[User][city]': {
//                required: "City name required",
//            },
//            'data[User][zip]': {
//                required: "Zip required",
//            },
//            'data[User][country]': {
//                required: "County name required",
//            },
//            'data[User][state]': {
//                required: "State name required",
//            },
        }
    });
    //=================Create New Client validations 
    $("#UserCreateendorserForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            },
            'data[User][fname]': {
                required: true,
            },
            'data[User][lname]': {
                required: true,
            },
            'data[User][role]': {
                required: true,
            },
//            'data[User][password]': {
//                required: true,
//                minlength: 8
//            },
//            'data[User][confirm_password]': {
//                required: true,
//                equalTo: '#UserPassword',
//            },
        },
        messages: {
            'data[User][firstname]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][fname]': {
                required: "First Name is required",
            },
            'data[User][lname]': {
                required: "Last Name is required",
            },
            'data[User][role]': {
                required: "Role is required",
            },
            'data[User][zip]': {
                required: "Please select zip",
            },
//            'data[User][password]': {
//                required: "Password is required",
//            },
//            'data[User][confirm_password]': {
//                required: "Confirm Password is required",
//                equalTo: "Confirm Password do not match",
//            },
        }
    });
    //Create org validations starts here
    $("#OrgCreateorgForm").validate({
        errorPlacement: function (error, element) {
            if (element.attr("name") == "data[Org][corevalues][]") {
                error.insertAfter("#corevaluesdropdown .select-style");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            'data[Org][name]': {
                required: true,
            },
            'data[Org][corevalues][]': {
                required: true,
            },
            'data[Org][phone_number]': {
                required: true,
            },
            'data[Org][zip]': {
                required: true,
            },
            'data[Org][street]': {
                required: true,
            },
            'data[Org][state]': {
                required: true,
            },
            'data[Org][city]': {
                required: true,
            },
            'data[Org][country]': {
                required: true,
            },
        },
        messages: {
            'data[Org][name]': {
                required: "Organization Name is required",
            },
            'data[Org][corevalues][]': {
                required: "Atleast a Core Value is required",
            },
            'data[Org][phone_number]': {
                required: "Organization phone number is required",
            },
            'data[Org][zip]': {
                required: "Organization zip is required",
            },
            'data[Org][street]': {
                required: "Organization street is required",
            },
            'data[Org][state]': {
                required: "Organization state is required",
            },
            'data[Org][city]': {
                required: "Organization city name is required",
            },
            'data[Org][country]': {
                required: "Organization country is required",
            },
        },
        submitHandler: function (form) {
            var finalcheck = new Array();
            var validate = false;
            $("#addcoretable tr").each(
                    function () {
                        if (typeof $(this).attr("id") != "undefined") {
                            if ($(this).find("#cvactive").val() == 'active' && $(this).find("#saveunsave").val() == "save") {
                                validate = true;
                            }
                        }
                    }
            )
            finalcheck.push({'value': "corevalue", 'validate': validate});
            if (finalcheck[0]["validate"] == true) {
                form.submit();
            } else {
                alertbootbox("Atleast a Value needs to be save and active for Core Value");
                return false;
            }

        },
    });
    $("#OrgEditorgForm").validate({
        rules: {
            'data[Org][name]': {
                required: true,
            },
            'data[Org][corevalues][]': {
                required: true,
            },
            'data[Org][phone_number]': {
                required: true,
            },
            'data[Org][zip]': {
                required: true,
            },
            'data[Org][street]': {
                required: true,
            },
            'data[Org][state]': {
                required: true,
            },
            'data[Org][city]': {
                required: true,
            },
            'data[Org][country]': {
                required: true,
            },
            'data[Org][manager_code]': {
                required: true,
            },
        },
        messages: {
            'data[Org][name]': {
                required: "Organization Name is required",
            },
            'data[Org][corevalues][]': {
                required: "Select Corevalue",
            },
            'data[Org][phone_number]': {
                required: "Organization phone number is required",
            },
            'data[Org][zip]': {
                required: "Organization zip is required",
            },
            'data[Org][street]': {
                required: "Organization street is required",
            },
            'data[Org][state]': {
                required: "Organization state is required",
            },
            'data[Org][city]': {
                required: "Organization city name is required",
            },
            'data[Org][country]': {
                required: "Organization country is required",
            },
            'data[Org][manager_code]': {
                required: "Organization manager code is required",
            },
        },
        submitHandler: function (form) {
            var finalcheck = new Array();
            var validate = false;
            $("#addcoretable tr").each(
                    function () {
                        if (typeof $(this).attr("id") != "undefined") {
                            if ($(this).find("#cvactive").val() == 'active' && $(this).find("#saveunsave").val() == "save") {
                                validate = true;
                            }
                        }
                    }
            )
            finalcheck.push({'value': "corevalue", 'validate': validate});
            if (finalcheck[0]["validate"] == true) {
                form.submit();
            } else {
                alert("Atleast a Value needs to be save and active for Core Value");
                return false;
            }
        },
    });
    //News letter validation

    $("#UserNewsletterForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true
            }
            ,
        },
        messages: {
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"

            }
        }

    });
    $("#userregister").validate({
        rules: {
            'data[User][firstname]': {
                required: true,
            },
            'data[User][email]': {
                required: true,
                email: true,
            }
            ,
            'data[User][password]': {
                required: true,
                minlength: 6,
            },
        },
        messages: {
            'data[User][firstname]': {
                required: "Name is required",
            },
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][password]': {
                required: "Password is required",
                minlength: "At least {6} character",
            },
        }

    });
    $("#UserLoginForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            }
            ,
            'data[User][password]': {
                required: true,
                minlength: 8,
            }
        },
        messages: {
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][password]': {
                required: "Password is required",
                minlength: "At least {8} character",
            },
        }

    });
    // UserphotoProfileForm validation
    $("#UserphotoSetimageForm").validate({
        rules: {
            'data[Userphoto][Userphoto]': {
                required: true,
            }

        },
        messages: {
            'data[Userphoto][Userphoto]': {
                required: "Please select a image",
                email: "Invalid email"
            },
        }

    });
    //===================global settings form
//    $("#submit_setting").click(function () {
//        tinyMCE.triggerSave();
//        var x = tinyMCE.activeEditor.getContent({format: 'text'})
//        var abc = x.trim();
//        if (abc == "") {
//            $("#UserTandc").val("");
//        }
//
//        $("#UserSettingForm").submit();
//
//    });

    $("#submit_general_setting").click(function () {
        $("#generalSettingForm").submit();
    });
    $("#generalSettingForm").validate({
        rules: {
            'data[general][value]': {
                required: true,
            },
        },
        messages: {
            'data[general][value]': {
                required: "Please Select Min Endorsement of 1",
            },
        }
    });
    $("#tandc_submit_setting").click(function () {
        tinyMCE.triggerSave();
        //var x = tinyMCE.activeEditor.getContent({format: 'text'})
        var x = tinyMCE.get('UserTandc').getContent({format: 'text'});
        var abc = x.trim();
        if (abc == "") {
            $("#UserTandc").val("");
        }
        $("#termsandconditionsSettingForm").submit();
    });
    //====announcement for superadmin
    $("#mailingorg_submit_setting").click(function () {
        var counter = 0;
//        $(".mailingcbclass").each(function () {
//            if ($(this).is(":checked") == true) {
//                counter++;
//            }
//        })
        $(".announcementcheckbox").each(function () {
            if ($(this).is(":checked") == true) {
                counter++;
            }
        })
        if (counter == 0) {
            alertbootbox("Atleast select a organization");
            return false;
        }
        var oFile = document.getElementById("MailingOrgAttachment").files[0];
        if (oFile != undefined) {
            if (oFile.size > 10485760) // 2 mb for bytes.
            {
                //alert("File size must under 2mb!");
                alertbootbox("File size must under 10mb!");
                return;
            }
        }
        tinyMCE.triggerSave();
        var x = tinyMCE.activeEditor.getContent({format: 'text'})
        var abc = x.trim();
        if (abc == "") {
            $("#UserMailingbox").val("");
        }
        $("#MailingOrgSettingForm").submit();
    });
    //Announcement edit for superadmin
    $("#mailingorg_submit_setting_edit").click(function () {
        var counter = 0;
//        $(".mailingcbclass").each(function () {
//            if ($(this).is(":checked") == true) {
//                counter++;
//            }
//        })
        $(".announcementcheckbox").each(function () {
            if ($(this).is(":checked") == true) {
                counter++;
            }
        })

        if (counter == 0) {
            alertbootbox("Atleast select a organization");
            return false;
        }
        var oFile = document.getElementById("MailingOrgAttachment").files[0];
        if (oFile != undefined) {
            if (oFile.size > 10485760) // 2 mb for bytes.
            {
                //alert("File size must under 2mb!");
                alertbootbox("File size must under 10mb!");
                return;
            }
        }
        tinyMCE.triggerSave();
        var x = tinyMCE.activeEditor.getContent({format: 'text'})
        var abc = x.trim();
        if (abc == "") {
            $("#UserMailingbox").val("");
        }
        $("#MailingOrgAnnouncementeditForm").submit();
    });
    $("#MailingOrgAnnouncementeditForm").validate({
        ignore: [],
        rules: {
            'data[User][mailingbox]': {
                required: true,
            }
        },
        messages: {
            'data[User][mailingbox]': {
                required: "Some text should be there to mail",
            }
        }
    });
    $("#MailingOrgSettingForm").validate({
        ignore: [],
        rules: {
            'data[User][mailingbox]': {
                required: true,
            }
        },
        messages: {
            'data[User][mailingbox]': {
                required: "Some text should be there to mail",
            }
        }
    });
    //====announcement for orgadmin

    $("#mailingorgadmin_submit_setting").click(function () {
        var counter = 0;
//        $(".mailingcbclass").each(function () {
//            if ($(this).is(":checked") == true) {
//                counter++;
//            }
//        });

        $(".announcementcheckbox").each(function () {
            if ($(this).is(":checked") == true) {
                counter++;
            }
        });
        if (counter == 0) {
            alertbootbox("Atleast select a organization");
            return false;
        }

        tinyMCE.triggerSave();
        //var x = tinyMCE.activeEditor.getContent({format: 'text'})
        var x = tinyMCE.activeEditor.getContent({format: 'raw'})
//        console.log(x); //return false;
        var abc = x.trim();
//        console.log(abc);
        if (abc == "") {
            $("#UserMailingbox").val("");
        } else {
            $("#messagebox").val(abc);
        }
        $("#MailingOrgAnnouncementsForm").submit();
    });
    $("#MailingOrgAnnouncementsForm").validate({
        ignore: [],
        rules: {
            'data[User][mailingbox]': {
                required: true,
            }
        },
        messages: {
            'data[User][mailingbox]': {
                required: "Some text should be there to mail",
            }
        }
    });
    //=======terms and settings global settings super admin
    $("#termsandconditionsSettingForm").validate({
        ignore: [],
        rules: {
            'data[User][tandc]': {
                required: true,
            }
        },
        messages: {
            'data[User][tandc]': {
                required: "Terms and Conditions are required",
            }
        }
    });
    //===============validating admin subscription form and force submit
    $('#adminsubscriptionMode').change(function () {
        var subtype = $(this).val();
        $("#suadmin_subscription_trial").hide();
        //$("#suadmin_subscription_paid").hide();
        $("#suadmin_subscription_amt").hide();
        $("#amt").val("");
        $("#adminsubscriptionTrialDuration").val("");
        if (subtype == "trial") {
            $("#amt").val(1);
            $("#suadmin_subscription_trial").show();
            // $("#suadmin_subscription_paid").hide();
            $("#suadmin_subscription_amt").hide();
        } else if (subtype == "paid") {
            $("#adminsubscriptionTrialDuration").val(1);
            $("#suadmin_subscription_trial").hide();
            //$("#suadmin_subscription_paid").show();
            $("#suadmin_subscription_amt").show();
        }
    });
    $('#downgradeadminsubscriptionNewest').change(function () {
        var subtype = $(this).val();
        if (subtype == "no") {
            $("#select_user").val("yes");
            $("#downgrade_active_users1").show();
        } else if (subtype == "yes") {
            jQuery(".checkselect").removeAttr('checked');
            $("#select_user").val("no");
            $("#downgrade_active_users1").hide();
        }
    });
    $('#bulkactiveuserOldestActive').change(function () {
        var subtype = $(this).val();
        if (subtype == "no") {

            $("#bulk_active_users1").show();
        } else if (subtype == "yes") {
            // jQuery(".checkselect").removeAttr('checked');

            $("#bulk_active_users1").hide();
        }
    });
    $(document).on("click", "#adminsubscriptionsubmit", function () {
        //$("#adminsubscriptionsubmit").click(function () {
        $("#adminsubscriptionIndexForm").submit();
    });
    $(document).on("click", "#downgradeadminsubscriptionsubmit", function () {
        //$("#adminsubscriptionsubmit").click(function () {

        $("#downgradeadminsubscriptionIndexForm").submit();
    });
    $(document).on("click", "#updateadminsubscriptionsubmit", function () {
        //$("#adminsubscriptionsubmit").click(function () {
        $("#upgradeadminsubscriptionIndexForm").submit();
    });
    $(document).on("click", "#overwriteadminsubscriptionsubmit", function () {
        //$("#adminsubscriptionsubmit").click(function () {
        $("#overwriteadminsubscriptionIndexForm").submit();
    });
    $(document).on("click", "#adminterminatesubscriptionsubmit", function () {
        //$("#adminsubscriptionsubmit").click(function () {
        $("#adminterminatesubscriptionsubmit").prop("disabled", true);
        if ($(this).attr("type") == "mannual") {
            $("#adminterminatesubscriptionIndexForm").submit();
        } else {
            var orgId = $("#terminate_org_id").val();
            var optnew = $('input[name="data[adminterminatesubscription][option]"]:checked', '#adminterminatesubscriptionIndexForm').val();
            //alert($('input[name="data[adminterminatesubscription][option]"]:checked', '#adminterminatesubscriptionIndexForm').val());
            var url = siteurl + "subscription/cancel/" + orgId;
            $.ajax({
                type: "POST",
                url: url,
                data: {organizationId: orgId},
                dataType: 'json',
                success: function (response) {
                    if (response.success == true) {
                        var elementId = response.og;
                        var upHtml = "";
                        //  alert(response.showPurchase);
                        if (1) {

                            $.ajax({
                                type: "POST",
                                url: siteurl + 'ajax/terminate',
                                data: {terminate_org_id: elementId, adminterminatesubscription: optnew},
                                dataType: 'json',
                                success: function (response) {

                                    if (response.status) {

                                        var uphtml = "";
                                        if (optnew == 1) {
                                            var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + elementId + ')">Sell Subscription</button>';
                                        } else {
                                            var uphtml = '<div class="msg"> Subscription canceled</div>';
                                        }

                                        var page_ndorse_url = window.location.href;
                                        if (page_ndorse_url.search("info") > 0) {
                                            window.location.reload();
                                        } else {
                                            $("#purchase_" + elementId).html(uphtml);
                                            $("#myModal2_terminatesubscription").modal("hide")
                                        }
                                    }

                                    // bootbox.alert(response.msg);

                                },
                                error: function (response) {

                                }
                            });
                        }

                    }



                },
                error: function (response) {

                }
            });
        }

    });
    $(document).on("click", ".activeuserbulksubmit", function () {
        var bulk_org_id = $("#bulk_org_id").val();
        //$("#adminsubscriptionsubmit").click(function () {
        // alert($(this).attr("recentuser"));
        if ($(this).attr("recentuser") == 1) {
            var active_users_no = $("#active_users_no").val();
            if (active_users_no < 1) {
                alert("user number required");
            } else {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/bulkactiveusers',
                    data: {org_id: bulk_org_id, qty: active_users_no, option: "recent", select_user_id: ""},
                    success: function (data) {
                        console.log(data);
                        var parseData = JSON.parse(data);
                        // is_deleted
                        $("#myModal2_bulkactiveuser").modal("hide");
                        var page_ndorse_url = window.location.href;
                        if (parseData.update == 1) {
                            alertbootboxcb("Users activated successfully.", function () {
                                window.location.reload();
                            });
//                            bootbox.alert("Users activated successfully.", function () {
//                                window.location.reload();
//                            });
                            // window.location.reload();
                        } else {

                            alertbootbox('You cannot activate the users since maximum limit to activate the users is reached. Please upgrade your subscription and then try again.');
                            // $("#myModal2_bulkactiveuser .modal-title").text("Bulk Active Users");
                            //alert("no pool available.please purchase/upgrade subscription");
                        }

                    }
                });
            }
        } else if ($(this).attr("recentuser") == 0) {
            var selectedid = "";
            var selectcount = 0;
            jQuery(".checkactiveselectuser").each(function () {
                //  var strsearch = jQuery(this).attr('username').toLowerCase();


                if ($(this).is(':checked'))
                {
                    selectcount++;
                    if (selectedid != "")
                    {
                        selectedid += "," + $(this).val();
                    } else {
                        selectedid += $(this).val();
                    }
                }
            });
            if (selectcount == 0) {
                alert('please select atleast one user');
            } else {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/bulkactiveusers',
                    data: {org_id: bulk_org_id, qty: 0, option: "no", select_user_id: selectedid},
                    success: function (data) {
                        console.log(data);
                        $("#myModal2_bulkactiveuser").modal("hide");
                        var parseData = JSON.parse(data);
                        var page_ndorse_url = window.location.href;
                        if (parseData.update == 1) {
                            alertbootboxcb("Users activated successfully.", function () {
                                window.location.reload();
                            });
//                            bootbox.alert("Users activated successfully.", function () {
//                                window.location.reload();
//                            });

                        } else {


                            // $("#myModal2_confirm").modal("show");

                            alertbootbox('You cannot activate the users since maximum limit to activate the users is reached. Please upgrade your subscription and then try again.');
                        }

                    }
                });
            }
        }
        // $("#bulkactiveuser").submit();

    });
    $(document).on("click", ".inactiveuserbulksubmit", function () {
        var inbulk_org_id = $("#inbulk_org_id").val();
        //$("#adminsubscriptionsubmit").click(function () {
        // alert($(this).attr("recentuser"));
        if ($(this).attr("recentuser") == 1) {
            var inactive_users_no = $("#inactive_users_no").val();
            if (inactive_users_no < 1) {
                alert("user number required");
            } else {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/bulkinactiveusers',
                    data: {org_id: inbulk_org_id, qty: inactive_users_no, option: "recent", select_user_id: ""},
                    success: function (data) {
                        console.log(data);
                        var parseData = JSON.parse(data);
                        // is_deleted

                        var page_ndorse_url = window.location.href;
                        $("#myModal2_bulkinactiveuser").modal("hide");
                        if (page_ndorse_url.search("info") > 0) {

                            if (parseData.downgrade == 1) {
                                // window.location.reload();
                                $("#myModal2_bulkinactiveuser").modal("hide");
                                //$("#confirmpurchase").attr("purchase","downgrade");
                                //$("#myModal2_confirm").modal("show");
                                //  
                                //   $('#myModal2_confirm .modal-title').text('U want to downgrade subscription.');
                                // bootbox.alert("users inactive successfully.if u want to downgrade subscription please downgrade subscription")
                                if (parseData.subscription_type == 'web') {

                                    bootbox.confirm({
                                        title: 'Users inactivated successfully. Do you want to downgrade the subscription by ' + parseData.downgrade_users + ' users?',
                                        message: ' ',
                                        buttons: btnObj,
                                        closeButton: false,
                                        callback: function (result) {
                                            if (result) {
                                                $("#bt_updateSubscription #userCount").val(parseData.downgrade_users);
                                                $("#bt_updateSubscription #reload").val('reload');
                                                $(".js_updateSubscription[act='downgrade']").click();
                                                bootbox.confir
                                            } else {
                                                window.location.reload();
                                            }
                                        }
                                    });
//                                    $(".bootbox-confirm .modal-body button").removeClass("bootbox-close-button");
//                                    $(".bootbox-confirm .modal-body button").addClass("hidden");

                                } else {
                                    alertbootboxcb("Users inactivated successfully.", function () {
                                        window.location.reload();
                                    });
//                                    bootbox.alert("Users inactivated successfully.", function () {
//                                        window.location.reload();
//                                    });
                                }
                            } else {
                                alertbootboxcb("users inactive successfully.", function () {
                                    window.location.reload();
                                });
//                                bootbox.alert("users inactive successfully.", function () {
//                                    window.location.reload();
//                                });
                            }

                            //                      bootbox.confirm({
                            //    title: 'Are you sure you want to downgrade the subscription?',
                            //    message: 'You will not be able to revert transaction!',
                            //    buttons: btnObj,
                            //    callback: function(result) {
                            //       if (result) {
                            //           window.location.reload();
                            //        }else{
                            //            window.location.reload();
                            //        }
                            //    }
                            //});
                            // window.location.reload(); 
                        } else {
                            alertbootbox("no quota available");
                            //bootbox.alert("no quota available");
                        }

                    }
                });
            }
        } else if ($(this).attr("recentuser") == 0) {
            var selectedid = "";
            var selectcount = 0;
            jQuery(".checkinactiveselectuser").each(function () {
                //  var strsearch = jQuery(this).attr('username').toLowerCase();


                if ($(this).is(':checked'))
                {
                    selectcount++;
                    if (selectedid != "")
                    {
                        selectedid += "," + $(this).val();
                    } else {
                        selectedid += $(this).val();
                    }
                }
            });
            if (selectcount == 0) {
                alert("please select atleast one user");
            } else {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/bulkinactiveusers',
                    data: {org_id: inbulk_org_id, qty: 0, option: "no", select_user_id: selectedid},
                    success: function (data) {
                        console.log(data);
                        var parseData = JSON.parse(data);
                        var page_ndorse_url = window.location.href;
                        if (parseData.downgrade == 1) {
                            // window.location.reload();
                            $("#myModal2_bulkinactiveuser").modal("hide");
                            //$("#confirmpurchase").attr("purchase","downgrade");
                            //$("#myModal2_confirm").modal("show");
                            //  
                            //   $('#myModal2_confirm .modal-title').text('U want to downgrade subscription.');
                            // bootbox.alert("users inactive successfully.if u want to downgrade subscription please downgrade subscription")
                            if (parseData.subscription_type == 'web') {

                                bootbox.confirm({
                                    title: 'Users inactivated successfully. Do you want to downgrade the subscription by ' + parseData.downgrade_users + ' users?',
                                    message: ' ',
                                    buttons: btnObj,
                                    closeButton: false,
                                    callback: function (result) {
                                        if (result) {
                                            $("#bt_updateSubscription #userCount").val(parseData.downgrade_users);
                                            $("#bt_updateSubscription #reload").val('reload');
                                            $(".js_updateSubscription[act='downgrade']").click();
                                        } else {
                                            window.location.reload();
                                        }
                                    }
                                });
//                                $(".bootbox-confirm .modal-body button").removeClass("bootbox-close-button");
//                                $(".bootbox-confirm .modal-body button").addClass("hidden");

                            } else {
                                alertbootboxcb("Users inactivated successfully", function () {
                                    window.location.reload();
                                });
                            }
                        } else {
                            alertbootboxcb("users inactive successfully.", function () {
                                window.location.reload();
                            });
                        }

                    }
                });
            }
        }
        // $("#bulkactiveuser").submit();

    });
    $("#bulkactiveuser").validate({
        rules: {
            'data[bulkactiveuser][active_users_no]': {
                required: true,
                min: 1,
                number: true
            },
        },
        messages: {
            'data[bulkactiveuser][active_users_no]': {
                required: "number of users cannot be empty"
            }

        },
        submitHandler: function (form) {
            var bulk_org_id = $("#bulk_org_id").val();
            var active_users_no = $("#active_users_no").val();
            var optionusers = $("#bulkactiveuserOldestActive").val();
            if (optionusers == "no") {
                var selectedid = "";
                var selectcount = 0;
                jQuery(".checkboxsearchuseractive").each(function () {
                    //  var strsearch = jQuery(this).attr('username').toLowerCase();


                    if ($(this).is(':checked'))
                    {
                        selectcount++;
                        if (selectedid != "")
                        {
                            selectedid += "," + $(this).val();
                        } else {
                            selectedid += $(this).val();
                        }
                    }
                });
                if (selectcount != active_users_no)
                {
                    // alert(selectcount+" "+active_users_no)
                    alertbootbox("Please select/unselect  users .")
                    return false;
                }
            }

            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/bulkactiveusers',
                data: {org_id: bulk_org_id, qty: active_users_no, option: optionusers, select_user_id: selectedid},
                success: function (data) {
                    console.log(data);
                    var parseData = JSON.parse(data);
                    // is_deleted

                    var page_ndorse_url = window.location.href;
                    if (page_ndorse_url.search("info") > 0) {
                        alertbootboxcb("users inactive successfully.", function () {
                            window.location.reload();
                        });
                    } else {
                        $("#purchase_" + down_org_id).html(uphtml);
                        $("#myModal2_downgradesubscription").modal("hide");
                    }

                }
            });
        }

    });
    $("#downgradeadminsubscriptionIndexForm").validate({
        rules: {
            'data[downgradeadminsubscription][users]': {
                required: true,
                min: 1,
                number: true
            },
            'data[downgradeadminsubscription][amt]': {
                min: 0,
                number: true
            },
        },
        messages: {
            'data[downgradeadminsubscription][users]': {
                required: "number of users cannot be empty"
            }

        },
        submitHandler: function (form) {
            var down_org_id = $("#down_org_id").val();
            var users_qty = $("#downgrade_users").val();
            var amt = $("#downgrade_amt").val();
            console.log(users_qty + " " + $("#pool_qty").val());
            var callbackflag = 0;
            // alert(users_qty+" "+$("#pool_qty").val());
            if (users_qty > parseInt(($("#pool_qty").val()))) {
                alertbootbox("You cannot downgrade the subscription since number of users to downgrade are more than the maximum users allowed.");
                return false;
            }
            var inactuserndorse = 0;
            if ($("#select_user").val() != "") {
                inactuserndorse = 1;
            }
            //  alert($("#pool_qty").val());
            // alert(parseInt($("#user_quota").val()) + " sdd  "+parseInt(users_qty)+" ---  "+parseInt($("#pool_qty").val()));

            // down_qty = (parseInt($("#user_quota").val()) + parseInt(users_qty)) - parseInt($("#pool_qty").val());
            down_qty = parseInt($("#pool_qty").val()) + 10 - (parseInt($("#user_quota").val()) + parseInt(users_qty));
            //alert(down_qty);
            if (down_qty < 0) {
                alertbootbox("You cannot downgrade the subscription since number of users to downgrade are more than the maximum users allowed. Please inactivate " + Math.abs(down_qty) + " users and then try again.");
                return;
            }
            if (amt == "") {
                amt = 0;
            }
            $("#downgradeadminsubscriptionsubmit").prop("disabled", true);
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/downgradesubscriptionadmin',
                data: {org_id: down_org_id, qty: users_qty, amt: amt},
                success: function (data) {
                    console.log(data);
                    var parseData = JSON.parse(data);
                    //$("#downgrade_users").val("");
                    // alert("Are you want to terminate subscription");
                    // create popup
                    //$("#disp_user_option").hide();
                    //$("#downgrade_active_users1").hide();
                    $("#myModal2_downgradesubscription").modal("hide")
                    console.log(users_qty + " " + parseInt($("#pool_qty").val()));
                    if (0)
                    {

                        // $("#select_user").val("");

                        bootbox.confirm({
                            title: 'Are you sure you want to terminate the subscription?',
                            message: 'You will not be able to revert transaction!',
                            buttons: btnObj,
                            closeButton: false,
                            callback: function (result) {
                                if (result) {
                                    callbackflag = 1;
                                    $.ajax({
                                        type: "POST",
                                        url: siteurl + 'ajax/terminate',
                                        data: {terminate_org_id: down_org_id, adminterminatesubscription: 1},
                                        success: function (data) {
                                            console.log(data);
                                            var parseData = JSON.parse(data);
                                            var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + down_org_id + ')">Sell Subscription</button>';
                                            var page_ndorse_url = window.location.href;
                                            if (page_ndorse_url.search("info") > 0) {
                                                window.location.reload();
                                            } else {
                                                $("#purchase_" + down_org_id).html(uphtml);
                                                $("#myModal2_downgradesubscription").modal("hide")
                                                $("#available_quota_" + down_org_id).html(10);
                                            }

                                        }
                                    });
                                } else {
                                    var page_ndorse_url = window.location.href;
                                    if (page_ndorse_url.search("info") > 0) {
                                        window.location.reload();
                                    } else {
                                        //$("#purchase_" + down_org_id).html(uphtml);
                                        $("#myModal2_downgradesubscription").modal("hide")
                                        $("#available_quota_" + down_org_id).html(10);
                                    }

                                }
                            }
                        });
//                        $(".bootbox-confirm .modal-body button").removeClass("bootbox-close-button");
//                        $(".bootbox-confirm .modal-body button").addClass("hidden");
                        // return false;
                    } else {
                        var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + down_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + down_org_id + ',' + (parseData.qty) + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + down_org_id + ')" >Terminate Subscription</button>';
                        $("#myModal2_downgradesubscription").modal("hide");
                        var page_ndorse_url = window.location.href;
                        if (page_ndorse_url.search("info") > 0) {
                            alertbootboxcb("Subscription downgraded successfully", function () {
                                window.location.reload();
                            });
                        } else {
                            $("#purchase_" + down_org_id).html(uphtml);
                            alertbootbox("Subscription downgraded successfully");
                            $("#available_quota_" + down_org_id).html((parseData.qty + 10));
                        }
                    }
                }
            });
        }

    });
    $("#upgradeadminsubscriptionIndexForm").validate({
        rules: {
            'data[upgradeadminsubscription][users]': {
                required: true,
                min: 1,
                number: true
            },
            'data[upgradeadminsubscription][amt]': {
                required: true,
                min: 1,
                number: true
            },
        },
        messages: {
            'data[upgradeadminsubscription][users]': {
                required: "users number cannot be empty"
            }
            ,
            'data[upgradeadminsubscription][amt]': {
                required: "amount cannot be empty"
            }
        },
        submitHandler: function (form) {
            var up_org_id = $("#up_org_id").val();
            var users_qty = $("#upgrade_users").val();
            var amt = $("#upgrade_amt").val();
            var oldest = $("#upgradeadminsubscriptionOldest").val();
            $("#updateadminsubscriptionsubmit").prop("disabled", true);
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/updatesubscriptionadmin',
                data: {org_id: up_org_id, qty: users_qty, amt: amt, oldest: oldest},
                success: function (data) {
                    console.log(data);
                    var parseData = JSON.parse(data);
                    var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + up_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + up_org_id + ',' + (parseData.qty) + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + up_org_id + ')" >Terminate Subscription</button>';
                    $("#available_quota_" + up_org_id).html(parseData.qty + 10);
                    var page_ndorse_url = window.location.href;
                    $("#myModal2_upgradesubscription").modal("hide")

                    if (page_ndorse_url.search("info") > 0) {

                        alertbootboxcb("Subscription uprgaded successfully.", function () {
                            window.location.reload();
                        });
                    } else {
                        $("#purchase_" + up_org_id).html(uphtml);
                        alertbootbox("Subscription uprgaded successfully");
                    }

                }
            });
        }

    });
// overwrite
    $("#overwriteadminsubscriptionIndexForm").validate({
        rules: {
            'data[overwriteadminsubscription][users]': {
                required: true,
                min: 1,
                number: true
            },
            'data[overwriteadminsubscription][amt]': {
                required: true,
                min: 1,
                number: true
            },
        },
        messages: {
            'data[overwriteadminsubscription][users]': {
                required: "users number cannot be empty"
            }
            ,
            'data[overwriteadminsubscription][amt]': {
                required: "amount cannot be empty"
            }
        },
        submitHandler: function (form) {
            var overwrite_org_id = $("#overwrite_org_id").val();
            var users_qty = $("#overwrite_users").val();
            var amt = $("#overwrite_amt").val();
            var url = siteurl + "subscription/cancel/" + overwrite_org_id;
            $.ajax({
                type: "POST",
                url: url,
                data: {organizationId: overwrite_org_id},
                dataType: 'json',
                success: function (response) {
                    if (response.success == true) {
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'ajax/overwritesubscriptionadmin',
                            data: {org_id: overwrite_org_id, qty: users_qty, amt: amt},
                            success: function (data) {
                                console.log(data);
                                var parseData = JSON.parse(data);
                                var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + parseData.org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + parseData.org_id + ',' + (users_qty) + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + parseData.org_id + ')" >Terminate Subscription</button>';
                                var page_ndorse_url = window.location.href;
                                $("#myModal2_overwritesubscription").modal("hide")
                                if (page_ndorse_url.search("info") > 0) {
                                    alertbootboxcb("Manual Subscription created successfully.", function () {
                                        window.location.reload();
                                    });
                                } else {
                                    $("#orgstatus_" + parseData.org_id).attr("type", "ndorse");
                                    alertbootboxcb("Subscription Override successfully.", function () {
                                        $("#available_quota_" + parseData.org_id).html((parseInt(users_qty) + 10));
                                        $("#purchase_" + parseData.org_id).html(uphtml);
                                    });
                                }

                            }
                        });
                    } else {
                        alert(response.msg);
                    }
                },
                error: function (response) {

                }
            });
        }

    });
//
    //===============validating new password form
    $("#adminsubscriptionIndexForm").validate({
        rules: {
            'data[adminsubscription][mode]': {
                required: true

            },
            'data[adminsubscription][trial_duration]': {
                required: true

            },
            'data[adminsubscription][users]': {
                required: true,
                min: 1,
                number: true
            },
            'data[adminsubscription][amount]': {
                required: true,
                min: 1,
                number: true
            },
        },
        messages: {
            'data[adminsubscription][mode]': {
                required: "Please select Mode"
            },
            'data[adminsubscription][users]': {
                required: "users number cannot be empty"
            }
            ,
            'data[adminsubscription][trial_duration]': {
                required: "please select time"
            },
            'data[adminsubscription][amount]': {
                required: "amount cannot be empty"
            }
        },
        submitHandler: function (form) {
            var sale_org_id = $("#sale_org_id").val();
            var mode = $("#adminsubscriptionMode").val();
            var users_qty = $("#users").val();
            var duration = $("#adminsubscriptionTrialDuration").val();
            var amt = $("#amt").val();
            $("#adminsubscriptionsubmit").prop("disabled", true);
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/addsubscriptionadmin',
                data: {org_id: sale_org_id, mode: mode, qty: users_qty, duration: duration, amt: amt},
                success: function (data) {
                    console.log(data);
                    msg = "Subscription created successfully.";
                    if (mode == "trial") {
                        msg = "Free trial subscription created successfully.";
                        var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradeSubscriptionTrial(' + sale_org_id + ')" >Upgrade</button>' +
                                '&nbsp;<button class="btn btn-xs btn-info" onclick="convertToPaidManual(' + sale_org_id + ', ' + users_qty + ', ' + users_qty * annual_price_per_user + ')" >Convert to paid</button>' +
                                '&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial(' + sale_org_id + ')" >Terminate Subscription</button>';
                    } else {
                        var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + sale_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + sale_org_id + ',' + users_qty + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + sale_org_id + ')" >Terminate Subscription</button>';
                    }
                    var parseData = JSON.parse(data);
                    var page_ndorse_url = window.location.href;
                    $("#myModal2_purchasesubscription").modal("hide");
                    if (page_ndorse_url.search("info") > 0) {
                        alertbootboxcb(msg, function () {
                            window.location.reload();
                        });
                    } else {

                        $("#orgstatus_" + sale_org_id).attr("type", "ndorse");
                        alertbootboxcb(msg, function () {
                            $("#purchase_" + sale_org_id).html(uphtml);
                            $("#available_quota_" + sale_org_id).html(parseInt(users_qty) + 10);
                        });
                    }

                }
            });
        }

    });
    $("#adminterminatesubscriptionIndexForm").ajaxForm({
        url: siteurl + "ajax/terminate",
        beforeSend: function () {

        },
        success: function (data) {

            var parseData = JSON.parse(data);
            if (parseData.status) {
                var del_org_id = parseData.org_id;
                termmsg = "";
                if (parseData.deleted == 2) {
                    termmsg = "Please note that the subscription will remain active and will be terminated only when the current billing cycle is over.";
                    var uphtml = '<button class="btn btn-xs btn-danger" onclick="revertsubscription(' + del_org_id + ')" >Revert Subscription</button>';
                } else {
                    var uphtml = ' <button class="btn btn-xs btn-info" onclick="purchasesubscription(' + del_org_id + ')">Sell Subscription</button>';
                    $("#available_quota_" + del_org_id).html(10);
                }

                var page_ndorse_url = window.location.href;
                $("#myModal2_terminatesubscription").modal("hide")
                if (page_ndorse_url.search("info") > 0) {
                    alertbootboxcb("Subscription terminated successfully." + termmsg, function () {
                        window.location.reload();
                    });
                } else {
                    $("#orgstatus_" + del_org_id).attr("type", "ndorse");
                    alertbootboxcb("Subscription terminated successfully." + termmsg, function () {
                        $("#purchase_" + del_org_id).html(uphtml);
                    });
                }

            } else {

                return false;
            }
        },
        error: function (msg) {
            return false;
        }
    });
//    $("#UserSettingForm").validate({
//        ignore: [],
//        rules: {
//            'data[User][value]': {
//                required: true,
//            },
//            'data[User][tandc]': {
//                required: true,
//            }
//        },
//        messages: {
//            'data[User][value]': {
//                required: "Please Select Min Endorsement of 1",
//            },
//            'data[User][tandc]': {
//                required: "Terms and Conditions are required",
//            }
//        }
//    });

    $('#UserDepartment , #UserSkills, #UserHobbies , #UserJobTitle').change(function () {
        var divselectid = $(this).attr("id");
        var selval1 = String($(this).val());
        var selval = selval1.split(",");
        if (selval.indexOf("other") > 0)
        {

            for (var i = 0, len = selval.length; i < len; i++)
            {
                if (selval[i] != "other")
                {
                    $('#' + divselectid + ' option[value=' + selval[i] + ']').attr('selected', false);
                }
            }
            $("#other_" + divselectid).show();
        } else if (selval == "other")
        {
            $("#other_" + divselectid).show();
        } else
        {
            $("#other_" + divselectid).hide();
        }

    });
    $("#OrgphotoSetorgimageForm").change(function () {

        $("#OrgphotoSetorgimageForm").submit();
    });
    $("#OrgphotoSetorgcpImageForm").change(function () {

        $("#OrgphotoSetorgcpImageForm").submit();
    });
    $("#photo").change(function () {
        $("#UserphotoSetimageForm").submit();
    });
    $("#clientformsubmit").click(function () {
        $("#UserCreateclientForm").submit();
    });
    $("#endorserformsubmit").click(function () {
        $("#UserCreateendorserForm").submit();
    });
    $("#editclientformsubmit").click(function () {
        $("#UserEditclientForm").submit();
    });
    $("#editcuserformsubmit").click(function () {
        $("#UserEditcuserForm").submit();
    });
    $("#orgformsubmit").click(function () {
        $("#OrgCreateorgForm").submit();
    });
    $("#editorgformsubmit").click(function () {
        $("#OrgEditorgForm").submit();
    });
    $("#UserphotoSetimageForm").ajaxForm({
        url: userprofile,
        success: function (data) {
            var parseData = JSON.parse(data);
            if (parseData.status) {
                //$("#img_msg").html("image has been updated successfully");
                $('#client_image').attr('src', imgurl + "small/" + parseData.imageloc);
                $('#client_image_name').val(parseData.imageloc);
            } else {
                $("#img_msg").html(parseData.error);
                return false;
            }
        },
        error: function (msg) {
            return false;
        }
    });
//    console.log("orguploadimage : " + orguploadimage);
    $("#OrgphotoSetorgimageForm").ajaxForm({
        url: orguploadimage,
        beforeSend: function () {

        },
        success: function (data) {
            console.log("after upload org image");
            console.log(data);
            var parseData = JSON.parse(data);
            if (parseData.status) {

                //$("#img_msg").html("image has been updated successfully");
                $('#org_image').attr('src', orgimgurl + "small/" + parseData.imageloc);
                $('#org_image_name').val(parseData.imageloc);
            } else {
                $("#img_msg").html(parseData.error);
                return false;
            }
        },
        error: function (msg) {
            return false;
        }
    });
    $("#UserProfessionalForm").validate({
        rules: {
            'data[User][type]': {
                required: true,
            },
            'data[User][firstname]': {
                required: true,
            },
            'data[User][email]': {
                required: true,
                email: true,
            }
            ,
            'data[User][password]': {
                required: true,
                minlength: 6,
            },
        },
        messages: {
            'data[User][type]': {
                required: "Please select type",
            },
            'data[User][firstname]': {
                required: "Name is required",
            },
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][password]': {
                required: "Password is required",
                minlength: "At least {6} character",
            },
        }

    });
//    // change password validation
//    $("#UserChangePasswordForm").validate({
//        rules: {
//            'data[User][current_password]': {
//                required: true,
//                minlength: 6,
//            },
//            'data[User][newpassword]': {
//                required: true,
//                minlength: 6,
//            },
//            'data[User][cpassword]': {
//                required: true,
//                minlength: 6,
//                equalTo: '#UserNewpassword'
//            },
//        },
//        messages: {
//            'data[User][current_password]': {
//                required: "Current Password is required",
//                minlength: "At least {6} character",
//            },
//            'data[User][newpassword]': {
//                required: "Password is required",
//                minlength: "At least {6} character",
//            },
//            'data[User][cpassword]': {
//                required: "Confirm password is required",
//                minlength: "At least {6} character",
//                equalTo: "Must equal to password.",
//            },
//        }
//
//    });
    // end

    //contact us form validation
//    $("#ContentContactusForm").validate({
//        rules: {
//            'data[Content][full_name]': {
//                required: true,
//            },
//            'data[Content][email]': {
//                required: true,
//                email: true,
//            },
//            'data[Content][enquiry]': {
//                required: true,
//            }
//
//        },
//        messages: {
//            'data[Content][full_name]': {
//                required: "Name is required"
//
//            },
//            'data[Content][email]': {
//                required: "Email is required",
//                email: "Invalid email"
//            },
//            'data[Content][enquiry]': {
//                required: "Query field cannot be empty",
//            }
//
//        }
//
//    });
//
//    //Validations for recommendation
//
//    $("#CoursesCompleteForm").validate({
//        rules: {
//            'data[Courses][email1]': {
//                email: true,
//                required: true,
//            },
//            'data[Courses][email2]': {
//                email: true,
//            },
//            'data[Courses][email3]': {
//                email: true,
//            },
//            'data[Courses][email4]': {
//                email: true,
//            },
//            'data[Courses][email5]': {
//                email: true,
//            }
//
//        },
//        messages: {
//            'data[Courses][email1]': {
//                email: "Invalid email",
//                required: "Email is required"
//            },
//            'data[Courses][email2]': {
//                email: "Invalid email"
//            },
//            'data[Courses][email3]': {
//                email: "Invalid email"
//            },
//            'data[Courses][email4]': {
//                email: "Invalid email"
//            },
//            'data[Courses][email5]': {
//                email: "Invalid email"
//            }
//
//        }
//
//    });
//
//
//
//
//    // check login form
//    $("#login_link").click(function () {
//        $('#register_nutrition').hide();
//        if ($('#login_nutrition, #overlay_login').css('display') == "none")
//        {
//
//            $('#login_nutrition, #overlay_login').css('display', 'block');
//
//        } else
//        {
//            $('#login_nutrition,#register_nutrition, #overlay_login').hide();
//        }
//        return false;
//    });
//    // end
//
//
//    $(document).on('click', '.rbtn', function () {
//
//        result = $(this).val();
//
//        if (result == 'yes') {
//            setNoToAllOptions();
//            $(this).prop("checked", true);
//        }
//    });
//
//
//
//    $('.txt-decimal').keyup(function () {
//        value = $.trim($(this).val());
//
//        if (value == '') {
//            $(this).val(0);
//            //value = 0;
//            value = '';
//        }
//
//
//        if (!/^[0-9]+(\.\d{0,3})?$/.test(value))//validate
//        {
//            $(this).val('');
//        }
//
//    });
//
//    $(document).on('keyup', '.txt-int', function () {
//        value = $.trim($(this).val());
//        if (value == '') {
//            $(this).val(0);
//            value = 0;
//        }
//
//        status = isInteger(value);
//
//        if (status && value != 0) {
//
//            $(this).val(Math.floor(parseInt(value)));
//
//        } else {
//
//            $(this).val('');
//        }
//    });
//    $(document).on('keydown', '.txt-int', function (e) {
//
//        // Allow: backspace, delete, tab, escape, enter and .
//        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
//                // Allow: Ctrl+A
//                        (e.keyCode == 65 && e.ctrlKey === true) ||
//                        // Allow: home, end, left, right
//                                (e.keyCode >= 35 && e.keyCode <= 39)) {
//                    // let it happen, don't do anything
//                    return;
//                }
//                // Ensure that it is a number and stop the keypress
//                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
//                    e.preventDefault();
//                }
//            });
//
//    function checksaveandactive() {
//        var checksaveunsave = new Array("department", "jobtitle", "cv");
//        var checktotalrows = new Array("department", "jobtitle", "core");
//
//        for (tmp in checksaveunsave) {
//            if ($("." + checksaveunsave[tmp] + "saveunsave").val() == "unsave") {
//                alert(checksaveunsave[tmp] + " is Not Saved Yet");
//                return false;
//            }
//        }
//    }

    $("#UserAddSuperAdminForm").validate({
        rules: {
            'data[User][email]': {
                required: true,
                email: true,
            },
            'data[User][fname]': {
                required: true,
            },
            'data[User][lname]': {
                required: true,
            },
            'data[User][password]': {
                required: true,
                minlength: 8,
            },
            'data[User][confirm_password]': {
                equalTo: '#UserPassword'
            },
        },
        messages: {
            'data[User][email]': {
                required: "Email is required",
                email: "Invalid email"
            },
            'data[User][fname]': {
                required: "First Name is required",
            },
            'data[User][lname]': {
                required: "Last Name is required",
            },
            'data[User][password]': {
                required: "Password is required",
                minlength: "Password Must be of atleast 8 characters"
            },
            'data[User][confirm_password]': {
                equalTo: "Confirm Password do not match",
            },
        }
    });
    $("#superAdminFormSubmit").click(function () {
        $("#UserAddSuperAdminForm").submit();
    })

    $('#UserAddSuperAdminForm #UserEmail').blur(function () {
        var email = $("#UserEmail").val();
        $.ajax({
            type: 'POST',
            url: siteurl + 'ajax/ifEmailExist',
            data: {email: email},
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    $("#UserEmail").val("");
                    $("#UserUsername").val("");
                    $("#UserEmail").focus();
                    alertbootbox(response.msg);
                }
            }
        });
    });
    $("#convertSubscriptionForm").validate({
        rules: {
            'data[convertSubscription][amount]': {
                required: true

            },
            'data[convertSubscription][users]': {
                required: true,
                min: function () {
                    return Number(convertMinUsers);
                },
                number: true
            }
        },
        messages: {
            'data[convertSubscription][users]': {
                required: "Please enter number of users to subscribe",
                min: function () {
                    return "You cannot enter less than " + convertMinUsers + " users";
                }
            }
            ,
            'data[convertSubscription][amount]': {
                required: "Please enter amount."
            }
        },
        submitHandler: function (form) {
            $("#convertSubscriptionSubmit").prop("disabled", true);
            var orgId = $("#convertToPaidOrgId").val();
            var usersCount = $("#convertUsers").val();
            var amount = $("#convertAmt").val();
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/convertToPaid',
                data: {orgId: orgId, usersCount: usersCount, amount: amount},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var msg = "Subscription converted to paid successfully";
                        var page_ndorse_url = window.location.href;
                        $("#myModal2_convertToPaid").modal("hide");
                        if (page_ndorse_url.search("info") > 0) {
                            alertbootboxcb(msg, function () {
                                window.location.reload();
                            });
                        } else {
                            var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + orgId + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + orgId + ',' + usersCount + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + orgId + ')" >Terminate Subscription</button>';
                            $("#orgstatus_" + orgId).attr("type", "ndorse");
                            alertbootboxcb(msg, function () {
                                $("#purchase_" + orgId).html(uphtml);
                                $("#available_quota_" + orgId).html((parseInt(usersCount) + freeUserPool));
                            });
                        }
                    } else {
                        alertbootbox("Subcription cannot be converted to paid. Please try again.");
                    }
                }
            });
        }

    });
    $('input[type=radio][class=js_userStatusRadio]').change(function () {
//        $("input[type=radio][class=js_inviteRadio]").removeAttr('checked');

        if (this.value != 1) {
            $("input[type=radio][class=js_inviteRadio][value=0]").prop('checked', true);
            jQuery('.js_inviteRadio').attr('disabled', 'disabled');
        } else {
            jQuery('.js_inviteRadio').removeAttr('disabled');
            $("input[type=radio][class=js_inviteRadio][value=1]").prop('checked', true);
        }
    });
});
// Document ready end

$(document).on("click", ".js_cancelSubscription", function (e) {
    var orgId = $(this).attr('og');
    bootbox.confirm({
        title: 'Are you sure you want to cancel the subscription?',
        message: 'You will not be able to revert this action!',
        buttons: btnObj,
        closeButton: false,
        callback: function (result) {
            if (result) {
                var url = siteurl + "subscription/cancel/" + orgId;
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {organizationId: orgId},
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == true) {
//                            var elementId = response.og;
//                            var upHtml = "";
//                            if (response.showPurchase) {
//                                upHtml = '<a href="' + siteUrl + 'subscription/btpurchase/' + orgId + '">Purchase Subscription</a>';
//                            } else {
//                                upHtml = '<div class="msg">Subscription canceled</div>';
//                            }
//                            $("#js_orgAction_" + elementId).html(upHtml);
                            alertbootboxcb(response.msg, function () {
                                window.location.reload();
                            });
                        } else {
                            alertbootbox(response.msg);
                        }



                    },
                    error: function (response) {

                    }
                });
            }
        }
    });
});
$(document).on("click", ".js_updateSubscription", function (e) {
    var action = $(this).attr('act');
    var orgId = $(this).attr('og');
//    $("#updateadminsubscriptionIndexForm").validate().resetForm();
    $("#bt_updateSubscription button[type=submit], input[type=submit]").prop("disabled", false);
//    $("#bt_updateSubscription #userCount").val("");
    $("#bt_updateSubscription #action").val(action);
    $("#bt_updateSubscription #updateType").html(action);
    $("#bt_updateSubscription #organizationId").val($(this).attr('og'));
    $("#bt_updateSubscription").modal("show")

    var title = "";
    if (action == 'upgrade') {
        title = "Upgrade Subscription";
        $("#bt_updateSubscription .modal-title").text(title);
    } else {
        $.ajax({
            type: "POST",
            url: siteurl + "ajax/getAllowedDowngrade",
            data: {organizationId: orgId},
            dataType: 'json',
            success: function (response) {
                title = "Downgrade Subscription (Max users allowed: " + response.allowedUsers + ")";
                $("#bt_updateSubscription .modal-title").text(title);
            }
        });
    }


});
//=======ajax call for attached image
$(document).on("click", ".attachedimage", function () {
    var endorsementid = $(this).attr("data-eid");
    var type = $(this).attr("type");
    $("#myModalViewImages").modal("show");
    $("#myModalViewImages .modal-body").html("");
    $.ajax({
        type: "POST",
        url: siteurl + "ajax/getattachedimagespopup",
        data: {endorsementid: endorsementid, type: type},
        dataType: 'json',
        success: function (response) {
            console.log(response["result"]);
            if (response["result"] == "true") {
                var data = response["data"];
                for (tmp in data) {
                    $("#myModalViewImages .modal-body").append("<div class='total-nDorsementes'><div class='imagesattached'><img class='attachment img-responsive'  src = '" + data[tmp] + "'></div></div>");
                }
            } else {
                alertbootbox("Something went wrong.")
            }
        }
    });
});
//===on clicking image to download in all endorsement
$(document).on("click", ".imagesattached", function () {
    var checkclass = $(this).children("img").hasClass("checkedimage-allendorsement");
    if (checkclass == false) {
        var imagesrc = $(this).children(".attachment").attr("src");
        $(this).append("<img data-cheked='" + imagesrc + "' class='checkedimage-allendorsement' src='" + siteurl + "img/selected-org.png'/>")
    } else if (checkclass == true) {
        $(this).children(".checkedimage-allendorsement").remove();
    }
})

//======on clicking of download images


$(document).on("click", "#allendorsements-attachedimages", function () {
    var imagestodownload = []
    $("#myModalViewImages .modal-body .imagesattached").each(function () {
        var checkclass = $(this).children("img").hasClass("checkedimage-allendorsement");
        if (checkclass == true) {
            var images = $(this).children("img.checkedimage-allendorsement").attr("data-cheked")
            imagestodownload.push(images);
        }
    });
    if (imagestodownload.length == 0) {
        alertbootbox("Select Image To Download.");
    } else if (imagestodownload.length == 1) {
        window.open(siteurl + "/organizations/downloadimage/" + btoa(imagestodownload[0]));
    } else {
        $.ajax({
            type: "POST",
            url: siteurl + "ajax/downloadattachedimages",
            data: {imagestodownload: imagestodownload},
            success: function (response) {
                if (response == "tmp.zip") {
                    window.open(siteurl + "/app/webroot/zipfiles/tmp.zip", '_self');
                } else {
                    window.open(response, '_blank');
                }
            }
        });
    }
//$("#myModalViewImages").modal("hide");

})

$(document).on("click", "#addattachemnt_announcement", function () {
    $("#MailingOrgAttachment").trigger("click");
})

$(document).on("change", "#MailingOrgAttachment", function () {
    $("#attachedfile").text("");
    var value = $(this).val();
//    var fileObj = $(this).get(0).files[0];
    var fileObj = this.files[0];
    var mimeType = fileObj.type;
    var error = false;
    if (mimeType.split('/')[0] != 'image') {
        var validExtension = ["ppt", "pptx", "doc", "docx", "xls", "xlsx", "pdf"];
        var fileName = fileObj.name;
        var extension = fileName.split('.').pop();
        extension = extension.toLowerCase();
        if (validExtension.indexOf(extension) == -1) {
            $(this).val("");
            delay(function () {
                $("#attachedfile").remove();
            }, 10);
            alertbootbox("Select a valid file.");
            return;
        }
    }

    if (fileObj.size > 10485760) {
        $(this).val("");
        delay(function () {
            $("#attachedfile").remove();
        }, 10);
        alertbootbox("File size must under 10mb!");
        return;
    }

    $("<span id='attachedfile' class='attach-img-name'>" + value + "</span>").insertAfter("#addattachemnt_announcement");
})

function dowloadUserList(orgId) {
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/saveUserSpreadsheet',
        data: {orgid: orgId},
        dataType: 'json',
        success: function (response) {
//            var jsonparse = $.parseJSON(data);
            var url = siteurl + 'xlsxfolder/' + response.filename;
            window.open(url, '_self');
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}


function bulkReinvite(org_id) {
    console.log(org_id);
    //$("#upgradeadminsubscriptionIndexForm").validate().resetForm();
    $("#reinvite_org_id").val(org_id);
    $("#bulkReinvite").html("");
    $("#inactive_users_no").val("");
    var userquota = 0;
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/getActiveUserList',
        data: {org_id: org_id},
        success: function (data) {
            console.log(data);
            var parseData = JSON.parse(data);
            var userdata = parseData.fresult;
            //  downgrade_active_users

            for (tmp in userdata) {
                userquota++;
                var key = tmp;
                var fname = userdata[tmp]["fname"];
                var lname = userdata[tmp]["lname"];
                var email = userdata[tmp]["email"];
                var username = fname + ' ' + lname;
                var userid = userdata[tmp]["id"];
                var admin = "";
                if (userdata[tmp]["user_role"] == 2)
                {
                    admin = " (Admin)";
                }
                //$('<div class="checkbox"><input type="checkbox" name="pendingrequests[]" id="checkpr' + key + '"  class="css-checkbox" value="' + email + '" rel="' + key + '"/><label class="css-label label-one" for="checkpr' + key + '">' + fname + '</label><label class="css-label label-two" for="checkpr' + key + '">' + lname + '</label><label class="css-label label-three" for="checkpr' + key + '">' + email + '</label></div>').appendTo("#myModal_pendingrequest #pendingdata");
                $('<div class="checkboxsearchuserreinvite checkbox" username="' + username.toLowerCase() + '" id="' + userid + '"><input type="checkbox" class="checkReinviteSelectUser css-checkbox" name="reinviteuser[]" id="checkpr' + userid + '"   value="' + userid + '" rel="' + userid + '"/>&nbsp; <label class="css-label" for="checkpr' + userid + '">' + fname + ' ' + lname + admin + '</label></div>').appendTo("#myModal2_bulkReinvite #bulkReinvite");
            }
            // alert(userquota);
            if (userquota > 0) {
                $("#myModal2_bulkReinvite").modal("show")
                $("#myModal2_bulkReinvite .modal-title").text("Bulk Re-Invite Users");
            }

        }
    });
}


$(document).on("click", ".reinviteuserbulksubmit", function () {
    var reinvite_org_id = $("#reinvite_org_id").val();
    var selectedid = "";
    var selectcount = 0;
    jQuery(".checkReinviteSelectUser").each(function () {
        if ($(this).is(':checked')) {
            selectcount++;
            if (selectedid != "")
            {
                selectedid += "," + $(this).val();
            } else {
                selectedid += $(this).val();
            }
        }
    });
    if (selectcount == 0) {
        alertbootbox("Please select atleast one user");
    } else {
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/bulkReinviteUsers',
            data: {orgId: reinvite_org_id, userIds: selectedid},
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $("#myModal2_bulkReinvite").modal("hide")
                    alertbootbox("Invitations sent successfully.");
                }
            }
        });
    }
// $("#bulkactiveuser").submit();

});
$(document).on("click", ".inviteOtherOrg", function () {
    var userId = $(this).attr('rel');
    $(".checkOrgInviteSelect").removeAttr('checked');
    $("#myModal2_inviteToOtherOrg #inviteToOtherOrgsDiv").html("");
    $("#myModal2_inviteToOtherOrg").find("#searchAllOrgs").val("");
    $.ajax({
        type: "GET",
        url: siteurl + 'ajax/getOrgList?userId=' + userId,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                var orgList = response.orgList;
                for (orgId in orgList) {
                    $('<div class="checkboxOrgInvite checkbox" orgName="' + orgList[orgId] + '"  id="' + orgId + '"><input type="checkbox" class="checkOrgInviteSelect css-checkbox" name="orgId[]" id="checkOrg_' + orgId + '"   value="' + orgId + '" rel="' + orgId + '"/>&nbsp; <label class="css-label" for="checkOrg_' + orgId + '">' + orgList[orgId] + '</label></div>').appendTo("#myModal2_inviteToOtherOrg #inviteToOtherOrgsDiv");
                }
                $("#myModal2_inviteToOtherOrg").find("#inviteUserId").val(userId);
                $("#myModal2_inviteToOtherOrg").modal("show")
            }
        }
    });
});
$(document).on("keyup", "#searchAllOrgs", function (event) {

    var keycode = (event.keyCode ? event.keyCode : event.which);
    //if(keycode == '13'){
    $(".checkboxOrgInvite").show();
    var searchtext1 = $('#searchAllOrgs').val();
    itemSearchAllOrgs(searchtext1);
    //}

});
function itemSearchAllOrgs(searchtext) {
    console.log(searchtext);
    if (searchtext != "") {
        // AllitemDisplay();
        jQuery("div.checkboxOrgInvite").each(function () {
            var strsearch = jQuery(this).attr('orgName');
            strsearch = strsearch.toLowerCase();
            if (strsearch.indexOf(searchtext.toLowerCase()) >= 0)
            {
                jQuery(this).show();
            } else {
                //checkpr
                $("#checkOrg_" + jQuery(this).attr("id")).removeAttr('checked');
                jQuery(this).hide();
            }
        });
        //jQuery('.breakfast_step .product_items').tinyscrollbar({ thumbSize: 68 });

    } else {
        $(".checkboxOrgInvite").show();
    }
}
$(document).on("change", ".checkOrgInviteSelect", function () {
    $(".checkOrgInviteSelect").not(this).attr("checked", false);
});
$(document).on("click", "#inviteToOtherOrgSubmit", function () {
    console.log('in');
    var invite_user_id = $("#inviteUserId").val();
    var selectedid = "";
    var selectcount = 0;
    jQuery(".checkOrgInviteSelect").each(function () {
        if ($(this).is(':checked')) {
            selectedid = $(this).val();
//                selectcount++;
//                if (selectedid != "")
//                {
//                    selectedid += "," + $(this).val();
//                } else {
//                    selectedid += $(this).val();
//                }
        }
    });
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/orgInviteUser',
        data: {userId: invite_user_id, orgId: selectedid},
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $("#myModal2_inviteToOtherOrg").modal("hide")
                alertbootbox("Invitations sent successfully.");
            }
        }
    });
    // $("#bulkactiveuser").submit();

});
function upgradeSubscriptionTrial(orgId) {
    $("#upgradeTrialSubscriptionSubmit").prop("disabled", false);
    $("#upgradeTrialSubscriptionForm").validate().resetForm();
    $("#trialUpgradeOrgId").val(orgId);
    $("#trialUsers").val("");
    $("#trialDuration").val("");
    $("#myModal2_upgradesubscriptiontrial").modal("show")
    $("#myModal2_upgradesubscriptiontrial .modal-title").text("Upgrade Trial Subscription");
}

$("#upgradeTrialSubscriptionForm").validate({
    rules: {
        'data[upgradeTrialSubscription][trial_duration]': {
            required: true

        },
        'data[upgradeTrialSubscription][users]': {
            required: true,
            min: 1,
            number: true
        }
    },
    messages: {
        'data[upgradeTrialSubscription][users]': {
            required: "Please enter number of users to upgrade"
        }
        ,
        'data[upgradeTrialSubscription][trial_duration]': {
            required: "Please select time duration"
        }
    },
    submitHandler: function (form) {
        var orgId = $("#trialUpgradeOrgId").val();
        var users = $("#trialUsers").val();
        var duration = $("#trialDuration").val();
        $("#upgradeTrialSubscriptionSubmit").prop("disabled", true);
        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/upgradeTrialSubscription',
            data: {orgId: orgId, users: users, duration: duration},
            success: function (response) {
                if (response.success) {
                    msg = "Free trial subscription upgraded successfully.";
                    //                    if (mode == "trial") {
                    //                        msg = "Free trial subscription created successfully.";
                    //                        var uphtml = '<button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial(' + sale_org_id + ')" >Terminate Subscription</button>';
                    //                    } else {
                    //                        var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + sale_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + sale_org_id + ',' + users_qty + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + sale_org_id + ')" >Terminate Subscription</button>';
                    //                    }
                    //                    var parseData = JSON.parse(data);

                    var page_ndorse_url = window.location.href;
                    $("#myModal2_upgradesubscriptiontrial").modal("hide");
                    if (page_ndorse_url.search("info") > 0) {
                        alertbootboxcb(msg, function () {
                            window.location.reload();
                        });
                    } else {

                        //                        $("#orgstatus_" + sale_org_id).attr("type", "ndorse");

                        alertbootboxcb(msg, function () {
                            //                            $("#purchase_" + sale_org_id).html(uphtml);

                            $("#available_quota_" + orgId).html(response.poolAvailable);
                        });
                    }
                }

            }
        });
    }

});
$(document).on("click", "#upgradeTrialSubscriptionSubmit", function () {
//        $("#upgradeTrialSubscriptionForm").submit();

    var orgId = $("#trialUpgradeOrgId").val();
    var users = $("#trialUsers").val();
    var duration = $("#trialDuration").val();
    if (($.trim(users) == "" || $.trim(users) == 0) && $.trim(duration) == "") {
        alertbootbox("Please enter either users or time duration to upgrade the subscription");
        return;
    }

    $("#upgradeTrialSubscriptionSubmit").prop("disabled", true);
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/upgradeTrialSubscription',
        dataType: 'json',
        data: {orgId: orgId, users: users, duration: duration},
        success: function (response) {
            if (response.success) {
                msg = "Free trial subscription upgraded successfully.";
//                    if (mode == "trial") {
//                        msg = "Free trial subscription created successfully.";
//                        var uphtml = '<button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial(' + sale_org_id + ')" >Terminate Subscription</button>';
//                    } else {
//                        var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradesubscription(' + sale_org_id + ')">Upgrade</button>&nbsp;<button class="btn btn-xs btn-info" onclick="downgradesubscription(' + sale_org_id + ',' + users_qty + ')">Downgrade</button>&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscription(' + sale_org_id + ')" >Terminate Subscription</button>';
//                    }
//                    var parseData = JSON.parse(data);

                var page_ndorse_url = window.location.href;
                $("#myModal2_upgradesubscriptiontrial").modal("hide");
                if (page_ndorse_url.search("info") > 0) {
                    alertbootboxcb(msg, function () {
                        window.location.reload();
                    });
                } else {

//                        $("#orgstatus_" + sale_org_id).attr("type", "ndorse");

                    alertbootboxcb(msg, function () {
//                            $("#purchase_" + sale_org_id).html(uphtml);

                        $("#available_quota_" + orgId).html(response.poolAvailable);
                    });
                    var poolPurchased = response.poolAvailable - freeUserPool;
                    var uphtml = '<button class="btn btn-xs btn-info" onclick="upgradeSubscriptionTrial(' + orgId + ')" >Upgrade</button>' +
                            '&nbsp;<button class="btn btn-xs btn-info" onclick="convertToPaidManual(' + orgId + ', ' + poolPurchased + ', ' + poolPurchased * annual_price_per_user + ')" >Convert to paid</button>' +
                            '&nbsp;<button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial(' + orgId + ')" >Terminate Subscription</button>';
                    $("#purchase_" + orgId).html(uphtml);
                }
            }

        }
    });
});
var convertMinUsers;
function convertToPaidManual(orgId, userCount, amount) {
    amount = userCount * annual_price_per_user;
    convertMinUsers = userCount;
    $("#convertSubscriptionForm").validate().resetForm();
    $("#myModal2_convertToPaid").find("#convertToPaidOrgId").val(orgId);
    $("#myModal2_convertToPaid").find("#convertUsers").val(userCount);
    $("#myModal2_convertToPaid").find("#convertAmt").val(amount);
    $("#myModal2_convertToPaid").modal('show');
}

$(document).on("click", "#convertSubscriptionSubmit", function () {
    $("#convertSubscriptionForm").submit();
});
function dowloadAllUserList() {
    $("#reportsLoader").show();
    $.ajax({
        type: "POST",
        url: siteurl + 'reports/users',
        dataType: 'json',
        success: function (response) {
            $("#reportsLoader").hide();
            //            var jsonparse = $.parseJSON(data);
            var url = siteurl + 'xlsxfolder/' + response.filename;
            window.open(url, '_self');
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

function dowloadAllOrganizationsList() {
    $("#reportsLoader").show();
    $.ajax({
        type: "POST",
        url: siteurl + 'reports/organizations',
        dataType: 'json',
        success: function (response) {
            $("#reportsLoader").hide();
            //            var jsonparse = $.parseJSON(data);
            var url = siteurl + 'xlsxfolder/' + response.filename;
            window.open(url, '_self');
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

function generateJoinCode(orgId) {
    $.ajax({
        type: "POST",
        url: siteurl + 'ajax/generateJoinCode',
        data: {orgId: orgId},
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                alertbootbox("Join organization code: " + response.code);
            } else {
                alertbootbox("There is some issue in generating join code. Please try again.")
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}

$(document).on("change", "#userresetShowPassword", function () {
    if (this.checked) {
        $("#userresetInfoForm").find("input[type=password]").prop("type", "text");
    } else {
        $("#userresetInfoForm").find("input[type=text]").prop("type", "password");
    }

});


 