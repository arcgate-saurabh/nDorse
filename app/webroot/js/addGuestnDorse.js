//document ready start
var endorseSelected = {
    "user": [],
    "department": [],
    'entity': []
};

$(document).on("keyup", "#endorsementSearchKeyGuest", function () {
    //    console.log(endorseSelected);
    $(document).find('.content-tab').addClass('temp');
    if ($(this).val().length >= 2) {
        var keyword = $(this).val();
        var org_id = $("#org_id").val();
        console.log(org_id);

        $(".suggestion").remove();
        $(".no-data-search").remove();

        console.log("before 1 : " + siteurl);

        if (siteurl.indexOf('localhost') > -1) {
            if (siteurl.indexOf('https') > -1) {
                siteurl = siteurl.replace("https", "http");
            }
        }
        if (siteurl.indexOf('ndorse.net') > -1) {
            if (siteurl.indexOf('https') > -1) {

            } else {
                //siteurl = siteurl.replace("http", "https");
            }
        }

        console.log("after : " + siteurl);

        $.ajax({
            type: "POST",
            url: siteurl + 'guest/searchInOrgGuest',
            data: {
                keyword: keyword,
                endorseSelected: endorseSelected,
                limit: 50,
                orgId: org_id
            },
            success: function (response) {
//                console.log(response); return false;
                $(".suggestion").remove();
                $(".no-data-search").remove();
                $("#nDorse-search-data").after(response);
            }
        });
    } else {
        $("#nDorse-search").addClass('hidden');
    }
});

$(document).on("keyup", "#daisySearchKeyGuest", function () {
    //    console.log(endorseSelected);
    $(document).find('.content-tab').addClass('temp');
    if ($(this).val().length >= 2) {
        var keyword = $(this).val();
        var org_id = $("#org_id").val();
        console.log(org_id);

        $(".suggestion").remove();
        $(".no-data-search").remove();

        console.log("before 1 : " + siteurl);

        if (siteurl.indexOf('localhost') > -1) {
            if (siteurl.indexOf('https') > -1) {
                siteurl = siteurl.replace("https", "http");
            }
        }
        if (siteurl.indexOf('ndorse.net') > -1) {
            if (siteurl.indexOf('https') > -1) {

            } else {
                //siteurl = siteurl.replace("http", "https");
            }
        }

        console.log("after : " + siteurl);

        $.ajax({
            type: "POST",
            url: siteurl + 'daisy/searchInOrgGuest',
            data: {
                keyword: keyword,
                endorseSelected: endorseSelected,
                limit: 50,
                orgId: org_id
            },
            success: function (response) {

//                console.log(response); return false;
                $(".suggestion").remove();
                $(".no-data-search").remove();
                $("#fistname-div").after(response);
//                $("#nDorse-search-data").after(response);
            }
        });
    } else {
        $("#nDorse-search").addClass('hidden');
    }
});


$(document).on("keyup", "#endorseDepartmentName", function () {
    //    console.log(endorseSelected);

    $(document).find('.content-tab').addClass('temp');
    if ($(this).val().length >= 2) {
        var keyword = $(this).val();
        var org_id = $("#org_id").val();
        console.log(org_id);

        $(".suggestion").remove();
        $(".no-data-search").remove();

        console.log("before 1 : " + siteurl);

        if (siteurl.indexOf('localhost') > -1) {
            if (siteurl.indexOf('https') > -1) {
                siteurl = siteurl.replace("https", "http");
            }
        }
        if (siteurl.indexOf('ndorse.net') > -1) {
            if (siteurl.indexOf('https') > -1) {

            } else {
                //siteurl = siteurl.replace("http", "https");
            }
        }

        console.log("after : " + siteurl);

        $.ajax({
            type: "POST",
            url: siteurl + 'daisy/searchInOrgGuestDept',
            data: {
                keyword: keyword,
                endorseSelected: endorseSelected,
                limit: 50,
                orgId: org_id
            },
            success: function (response) {

//                console.log(response); return false;
                $(".suggestion").remove();
                $(".no-data-search").remove();
                $("#dept-div").after(response);
//                $("#nDorse-search-data").after(response);
            }
        });
    } else {
        $("#nDorse-search").addClass('hidden');
    }
});

$(document).on("click", ".close-suggestion", function () {
    $(".suggestion").addClass('hidden');
});

$(document).on("click", ".js_searched", function () {
    alert("selected");
    var endorsementfor = $(this).attr("data-endorsementfor");
    var endorsedId = $(this).attr("data-endorsedid");
    var name = $(this).find(".js_searchedName").html();
    var endorseType = $("#endorseType").val();
    if (endorseType == "private" && (endorsementfor == 'department' || endorsementfor == "entity")) {
        alertbootbox("A Private nDorsement can only be sent to another User.");
    } else {
        endorseSelected[endorsementfor].push(endorsedId);

        var addHtml = '<span class="js_selectedValue" data-endorsedid="' + endorsedId + '" data-endorsementfor="' + endorsementfor + '">' + name + ' <a href="javascript:void(0);" class="js_removeSelected" rel="' + endorsementfor + "_" + endorsedId + '">X</a></span>';

        $("#selectedValues").append(addHtml);
        $("#endorsementAddForm").append('<input type="hidden" class="js_endorsee" name="endorsee[' + endorsementfor + '][]" value="' + endorsedId + '" id="' + endorsementfor + "_" + endorsedId + '">');
        $(".selected-values ").removeClass('hidden');
    }
    $("#nDorse-search").addClass('hidden');

    $("#endorsementSearchKey").val("");
});

$(document).on("click", ".js_closeSearch", function () {
    $("#nDorse-search").html("");
    $("#endorsementSearchKey").val("");
    $("#nDorse-search").addClass('hidden');
});

$(document).on("click", ".js_removeSelected", function () {
    var id = $(this).attr('rel');
    var splitted = id.split("_");
    var endorsementFor = splitted[0];
    var endorsedId = splitted[1];
    var index = endorseSelected[endorsementFor].indexOf(endorsedId);
    endorseSelected[endorsementFor].splice(index, 1);
    $("#" + id).remove();
    $(this).closest(".js_selectedValue").remove();
    if ($("#selectedValues").find(".js_selectedValue").length == 0) {
        $('.selected-values').addClass('hidden');
    }
});

$(document).on("click", ".js_clearAll", function () {
    endorseSelected = {
        "user": [],
        "department": [],
        'entity': []
    };
    $("#selectedValues").html("");
    $("input.js_endorsee").remove();
    $('.selected-values').addClass('hidden');
});

$(document).on("click", ".select-guest", function () {
    var id = $(this).attr('data-id');
    var type = $(this).attr('data-type');
    var image = $(this).find('img').attr('src');
    var h2_tag = $(this).find('h2').html();

    var fistName = $(this).find('#endorse_firstname').val();
    var lastName = $(this).find('#endorse_lastname').val();
    var deptName = $(this).find('#endorse_department').val();
    var deptID = $(this).find('#endorse_department_id').val();

    var endorse_designation = $(this).find('h3').html();
    var endorse_dept = $(this).find('h4').html();
    //var endorseImage = $(this).find('#endorse_image').val();
    var endorseImage = $(this).find('.endorseimage').attr("src");


    $("#default_user_checked").prop('checked', false);


    console.log('id : ' + id);
    console.log('type : ' + type);
    console.log('image : ' + image);
    console.log('h2 : ' + h2_tag);
    console.log('endorse_designation : ' + endorse_designation);
    console.log('endorse_dept : ' + endorse_dept);
    console.log('endorseImage : ' + endorseImage);
    console.log('fistName : ' + fistName);
    console.log('lastName : ' + lastName);
    console.log('deptName : ' + deptName);
    console.log('deptID : ' + deptID);

    $(document).find('.content-tab').removeClass('temp');

    $(".suggestion").remove();
    $(".no-data-search").remove();
    $('.selected-user').find('h6').html(h2_tag);
    $('#selected_endorse_image').attr("src", endorseImage);
    $('.selected-user').find('h2').html(h2_tag);
    $("#endorsementSearchKeyGuest").val('');


    $("#daisySearchKeyGuest").val(fistName);
    $("#endorseLastName").val(lastName);
    $("#endorseDepartmentName").val(deptName);
    $("#selected_dept_id").val(deptID);


    $(".selected_endorse_designation").html(endorse_designation);
    $(".selected_endorse_dept").html(endorse_dept);

    $("#selected_endorse_id").val(id);
    $("#selected_endorse_name").val(h2_tag);
    $("#selected_endorse_type").val(type);

});

$(document).on("click", ".select-dept", function () {
    var id = $(this).attr('data-id');
    var type = $(this).attr('data-type');
    var image = $(this).find('img').attr('src');
    var h2_tag = $(this).find('h2').html();

    var deptName = $(this).find('h2').html();
    var deptID = $(this).find('#endorse_department_id').val();
    var endorse_dept = $(this).find('h2').html();

    console.log('id : ' + id);
    console.log('type : ' + type);
    console.log('h2 : ' + h2_tag);
    console.log('endorse_dept : ' + endorse_dept);
    console.log('deptName : ' + deptName);
    console.log('deptID : ' + deptID);

    $(document).find('.content-tab').removeClass('temp');

    $(".suggestion").remove();
    $(".no-data-search").remove();

    $("#endorseDepartmentName").val(deptName);
    $("#selected_dept_id").val(deptID);

    $(".selected_endorse_dept").html(h2_tag);


});

$(document).on("click", "#selectedUserImage", function () {
    $(document).find('.content-tab').addClass('temp');
    $('.selected-user').find('h6').html('');
    $('#selected_endorse_image').attr("src", '');
    $('.selected-user').find('h2').html('');
    $("#endorsementSearchKeyGuest").val('');
    $("#daisySearchKeyGuest").val('');
    $("#selected_endorse_name").val('');
    $(".selected_endorse_designation").html('');
    $(".selected_endorse_dept").html('');

    $("#selected_endorse_id").val('');
    $("#selected_endorse_type").val('');
});