function donotdelete(area, cb) {
    var tableid = $(area).closest('table')[0].id;
    var rowlength = $("#" + tableid + " tr").length;
    console.log(rowlength);
    //==========to atleast maintain a row for department
    if (rowlength <= 2) {
        alertbootbox("Atleast A Row Needs To Be There");
        return false;
    }else{
        cb();
    }
}
/*
 * Core Values
 */

//=========================saving core values
$(document).on("click", ".savecorevalues", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var rowsplit = rowid.split('_');
    var rownum = rowsplit[1];
    if ($("#" + rowid + " #corevalue").is(":visible")) {
        alertbootbox("Already Saved");
        return false;
    }
    var value = $("#" + rowid + " #OrgCorevalues").val();
    if (value == "other") {
        value = $("#" + rowid + " #OrgOtherDepartment").val();
        $("#" + rowid + " #OrgOtherDepartment").css("display", "none");
    }
    var startvalue = value;
    //value = trimAndLowerCaseString(value);
    if (value == "") {
        alertbootbox("Please Select Core values First");
        $("#" + rowid + " #OrgOtherDepartment").css("display", "block");
        return false;
    }
    
    var rowlength = $("#addcoretable tr").length;
    var othervalues = new Array();
    for (var i = 1; i < rowlength; i++) {
        if (i != rownum) {
            othervalues.push(trimAndLowerCaseString($('#addcorerow_' + i + ' #corevalue').text()));
        }
    }
    if ($.inArray(trimAndLowerCaseString(value), othervalues) != "-1") {
        alertbootbox("value already exist in core values");
        return false;
    }
    var colorcode = $("#" + rowid + " #colorpick").val();
    $("#" + rowid + " #colorpick").attr("type", "text");
    $("#" + rowid + " #colorpick").attr("value", colorcode);
    $("#" + rowid + " #saveunsave").val("save");                     
    $("#" + rowid + " #OrgCorevalues").css("display", "none");
    
    if ($("#" + rowid + " p#corevalue").length) {
        $("#" + rowid + " p#corevalue").css("display", "block");
        $("#" + rowid + " p#corevalue").text(value);
    } else {
        $("<p id='corevalue'>" + startvalue + "</p>").appendTo("#" + rowid + " #corevaluesdropdown");
    }
    
});

//===============Editing core values
$(document).on("click", "#editcorevalues", function () {
    
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    //$("#" + rowid + " #colorpick").attr("type", "color");
    if ($("#" + rowid + " #OrgCorevalues").is(":visible")) {
        alertbootbox("Please Save First Before Editing");
        return false;
    }
    console.log("ROW ID : "+rowid);
    var value = $("#" + rowid + " p#corevalue").text();
    
    $("#" + rowid + " #saveunsave").val("unsave");
    $("#" + rowid + " #OrgCorevalues").css("display", "block");
    $("#" + rowid + " p#corevalue").css("display", "none");
    for (tmpvalue in js_arraycv) {
        if (tmpvalue == value) {
            console.log("Matched : "+value);
            return false;
        }
    }
    $("#" + rowid + " #othercv").css("display", "block");
    $("#" + rowid + " #saveunsave").val("unsave");
    $("#" + rowid + " #OrgCorevalues option[value=other]").prop("selected", "true");
    $("#" + rowid + " #OrgOtherDepartment").val(value).css("display", "block");
});

//==========================Deleting core values
$(document).on("click", "#deletecorevalues", function () {
    var obj = this;
    donotdelete(obj, function(){
        var rowvalue = $(obj).closest('tr');
        var rowid = rowvalue[0].id;
        $("#" + rowid).remove();
    });
});
$(document).on('change', '#OrgCorevalues', function () {
    //==========================find the row
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    if ($("#" + rowid + " #OrgCorevalues").val() == "other") {
        $("#" + rowid + " #othercv").css("display", "block");
        $("#" + rowid + " #othercv").attr("type", "text");
        $("#" + rowid + " #OrgOtherDepartment").css("display", "block");
    } else {
        $("#" + rowid + " #othercv").css("display", "none");
    }
});
$(document).on("change", ".OrgCvactivestatus", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var stats = $("#" + rowid + " #cvactive").val();
    if (stats == "active") {
        $("#" + rowid + " #cvactive").val("inactive");
    } else if (stats == "inactive") {
        $("#" + rowid + " #cvactive").val("active");
    }
});

/*
 * Adding editing deleting new Entity
 */

//==========================saving new entity
$(document).on('click', '.saveentity', function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    if ($('#' + rowid + ' .corevalue').is(':visible')) {
        alertbootbox("Already Saved");
        return false;
    }
    var startvalue = $("#" + rowid + " .entitytextbox").val();
    var value = trimAndLowerCaseString($("#" + rowid + " .entitytextbox").val());
    if (value == "") {
        alertbootbox("Enter Entity value");
        return false;
    }
    var allvalues = new Array();
    $("#addentitytable tr").each(function () {
        if (typeof ($(this).attr("id")) != "undefined") {
            if ($(this).find(".entitytextbox").attr("type") == "hidden") {
                var values = $(this).find(".entitytextbox").val();
                allvalues.push(trimAndLowerCaseString(values));
            }
        }
    })
    if ($.inArray(trimAndLowerCaseString(value), allvalues) != "-1") {
        alertbootbox("value already Saved");
        return false;
    }
    $("#" + rowid + " .entitytextbox").attr("type", "hidden");
    //$("#" + rowid + " .entitytextbox").css("display", "none");
    $("<p class='corevalue'>" + startvalue + "</p>").appendTo("#" + rowid + " .entityvaluestextbox");
    $("#" + rowid + " .entitysaveunsave").val("save");
});

//editing new entity
$(document).on('click', '.editentity', function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    if ($('#' + rowid + ' .entitytextbox').is(':visible')) {
        alertbootbox("Save First Before Edit Saved");
        return false;
    }
    var value = $("#" + rowid + " .corevalue").text();
    $("#" + rowid + " .corevalue").remove();
    $("#" + rowid + " .entitytextbox").attr("type", "text");
    $("#" + rowid + " .entitytextbox").val(value);
    $("#" + rowid + " .entitysaveunsave").val("unsave");
});

//==========================on tick or untick of checkbox
$(document).on("change", ".entitycheckbox", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var stats = $("#" + rowid + " .entityactive").val();
    if (stats == "active") {
        $("#" + rowid + " .entityactive").val("inactive");
    } else if (stats == "inactive") {
        $("#" + rowid + " .entityactive").val("active");
    }
});

///==========================/deleting the row
$(document).on('click', '.deleteentity', function () {
    var tableid = $(this).closest('table')[0].id;
    var rowlength = $("#" + tableid + " tr").length;
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    //==========to atleast maintain a row for department
    if (rowlength == 2) {
        $("#addentitydiv").hide();
        return false;
    }
    $("#" + rowid).remove();
});

$(document).on("change", "#OrgCvactivestatus", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var stats = $("#" + rowid + " #cvactive").val();
    if (stats == "active") {
        $("#" + rowid + " #cvactive").val("inactive");
    } else if (stats == "inactive") {
        $("#" + rowid + " #cvactive").val("active");
    }
});

/*
 * Adding editing deleting new department
 */

//=====================saving new department
$(document).on("click", ".savedepartment", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    $('#'+rowid).find("label.error").remove();
    var rowsplit = rowid.split('_');
    var rownum = rowsplit[1];
    var value = $("#" + rowid + " .departmentvalues").val();
    
    if (value == "other") {
        value = $("#" + rowid + " .other_department").val();
    }
    var startvalue = value;
    value = trimAndLowerCaseString(value);
    if (value == "") {
        alertbootbox("Please Select Department First");
        return false;
    }
    if ($("#" + rowid + " p.departmentv").is(":visible")) {
        alertbootbox("Already Saved");
        return false;
    }
    var rowlength = $("#adddepartmenttable tr").length;
    var totalvalues = new Array();
    for (var i = 1; i < rowlength; i++) {
        if (i != rownum) {
            totalvalues.push(trimAndLowerCaseString($('#adddepartment_' + i + ' .departmentv').text()));
        }
    }
    if ($.inArray(trimAndLowerCaseString(value), totalvalues) != "-1") {
        alertbootbox("value already exist");
        return false;
    }
    $("#" + rowid + " .departmentsaveunsave").val("save");
    $("#" + rowid + " .departmentvalues").css("display", "none");
    $("#" + rowid + " .other_department").css("display", "none");
    $("<p class='departmentv'>" + startvalue + "</p>").appendTo("#" + rowid + " .departmentselectrow");
});


//Editing departments
$(document).on("click", ".editdepartment", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    if ($("#" + rowid + " .departmentvalues").is(":visible")) {
        alertbootbox("Please Save First Before Editing");
        return false;
    }
    $("#" + rowid + " .departmentsaveunsave").val("unsave");
    $("#" + rowid + " .other_department").css("display", "block");
    $("#" + rowid + " .departmentvalues").css("display", "block");
    $("#" + rowid + " p.departmentv").remove();
});

//===============================Deleting departments
$(document).on("click", ".deletedepartment", function () {
        var tableid = $(this).closest('table')[0].id;
        var rowlength = $("#" + tableid + " tr:visible").length;
        var obj = this;
    //donotdelete(obj, function(){
        var rowvalue = $(obj).closest('tr');
        if (rowlength == 2) {
            $("#adddepartmentdiv").hide();
            return false;
        }
        var rowid = rowvalue[0].id;
        $("#" + rowid).remove();
    //});
});

//=======================on changing select box department
$(document).on('change', '.departmentvalues', function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    console.log($("#" + rowid + " .departmentvalues").val());
    if ($("#" + rowid + " .departmentvalues").val() == "other") {
        $("#" + rowid + " .other_department").attr("type", "text");
    } else {
        $("#" + rowid + " .other_department").attr("type", "hidden");
    }
});

$(document).on("change", ".entitycheckbox", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var stats = $("#" + rowid + " .departmentactive").val();
    if (stats == "active") {
        $("#" + rowid + " .departmentactive").val("inactive");
    } else if (stats == "inactive") {
        $("#" + rowid + " .departmentactive").val("active");
    }
});

/*
 * Adding editing deleting new jobtitle
 */
//saving new department
$(document).on("click", ".savejobtitle", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var rowsplit = rowid.split('_');
    var rownum = rowsplit[1];
    var value = $("#" + rowid + " .jobtitlevalues").val();
    if (value == "other") {
        value = $("#" + rowid + " .other_jobtitle").val();
    }
    if (value == "") {
        alertbootbox("Please Select Job Title First");
        return false;
    }
    var startvalue = value;
    value = trimAndLowerCaseString(value);
    if ($("#" + rowid + " p.jobtitlev").is(":visible")) {
        alertbootbox("Already Saved");
        return false;
    }
    var rowlength = $("#addjobtitletable tr").length;
    var totalvalues = new Array();
    for (var i = 1; i < rowlength; i++) {
        if (i != rownum) {
            totalvalues.push(trimAndLowerCaseString($('#addjobtitle_' + i + ' .jobtitlev').text()));
        }
    }
    if ($.inArray(trimAndLowerCaseString(value), totalvalues) != "-1") {
        alertbootbox("value already exist");
        return false;
    }
    $("#" + rowid + " .jobtitlesaveunsave").val("save");
    $("#" + rowid + " .jobtitlevalues").css("display", "none");
    $("#" + rowid + " .other_jobtitle").css("display", "none");
    $("<p class='jobtitlev'>" + startvalue + "</p>").appendTo("#" + rowid + " .jobtitleselectrow");
});

//========================Deleting jobtitles
$(document).on("click", ".deletejobtitle", function () {
    var tableid = $(this).closest('table')[0].id;
    var rowlength = $("#" + tableid + " tr:visible").length;
    var obj = this;
    //donotdelete(obj, function(){
        var rowvalue = $(obj).closest('tr');
        if (rowlength == 2) {
            $("#addjobtitlediv").hide();
            return false;
        }
        var rowid = rowvalue[0].id;
        $("#" + rowid).remove();
    //});
});

//=========================on changing select box jobtitle
$(document).on('change', '.jobtitlevalues', function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    console.log($("#" + rowid + " .jobtitlevalues").val());
    if ($("#" + rowid + " .jobtitlevalues").val() == "other") {
        $("#" + rowid + " .other_jobtitle").attr("type", "text");
    } else {
        $("#" + rowid + " .other_jobtitle").attr("type", "hidden");
    }
});

$(document).on("change", ".jobtitlecheckbox", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    var stats = $("#" + rowid + " .jobtitleactive").val();
    if (stats == "active") {
        $("#" + rowid + " .jobtitleactive").val("inactive");
    } else if (stats == "inactive") {
        $("#" + rowid + " .jobtitleactive").val("active");
    }
});

//==================Editing jobtitles
$(document).on("click", ".editjobtitle", function () {
    var rowvalue = $(this).closest('tr');
    var rowid = rowvalue[0].id;
    if ($("#" + rowid + " .jobtitlevalues").is(":visible")) {
        alertbootbox("Please Save First Before Editing");
        return false;
    }
    $("#" + rowid + " .jobtitlesaveunsave").val("unsave");
    $("#" + rowid + " .other_jobtitle").css("display", "block");
    $("#" + rowid + " .jobtitlevalues").css("display", "block");
    $("#" + rowid + " p.jobtitlev").remove();
});
