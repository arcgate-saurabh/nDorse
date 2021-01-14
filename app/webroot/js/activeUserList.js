//document ready start
var endorseSelected = {
    "user": [],
    "department": [],
    'entity': []
};

$(document).on("keyup", "#searchendorsements", function () {
    console.log($(this).val());
    $(document).find('.content-tab').addClass('temp');
    if ($(this).val().length >= 1) {
        var keyword = $(this).val();
        $(".suggestion").remove();
        $(".no-data-search").remove();
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/searchActiveUser',
            data: {
                keyword: keyword,
                limit: 50
            },
            success: function (response) {
                $("#endorsementlist").html(response);
            }
        });
    } else {
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/searchActiveUser',
            data: {
                keyword: '',
                limit: 50,
            },
            success: function (response) {
//                console.log(response);
                $("#endorsementlist").html(response);
            }
        });
        $("#nDorse-search").addClass('hidden');
    }
});

$(document).on("click", ".close-suggestion", function () {
    $(".suggestion").addClass('hidden');
});

$(document).on("click", ".js_searched", function () {
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
    var endorse_designation = $(this).find('h3').html();
    var endorse_dept = $(this).find('h4').html();
    //var endorseImage = $(this).find('#endorse_image').val();
    var endorseImage = $(this).find('.endorseimage').attr("src");

    console.log('id : ' + id);
    console.log('type : ' + type);
    console.log('image : ' + image);
    console.log('h2 : ' + h2_tag);
    console.log('endorse_designation : ' + endorse_designation);
    console.log('endorse_dept : ' + endorse_dept);
    console.log('endorseImage : ' + endorseImage);

    $(document).find('.content-tab').removeClass('temp');

    $(".suggestion").remove();
    $(".no-data-search").remove();
    $('.selected-user').find('h6').html(h2_tag);
    $('#selected_endorse_image').attr("src", endorseImage);
    $('.selected-user').find('h2').html(h2_tag);
    $("#endorsementSearchKeyGuest").val('');
    $(".selected_endorse_designation").html(endorse_designation);
    $(".selected_endorse_dept").html(endorse_dept);

    $("#selected_endorse_id").val(id);
    $("#selected_endorse_name").val(h2_tag);
    $("#selected_endorse_type").val(type);

});

$(document).on("click", "#selectedUserImage", function () {
    $(document).find('.content-tab').addClass('temp');
    $('.selected-user').find('h6').html('');
    $('#selected_endorse_image').attr("src", '');
    $('.selected-user').find('h2').html('');
    $("#endorsementSearchKeyGuest").val('');
    $(".selected_endorse_designation").html('');
    $(".selected_endorse_dept").html('');

    $("#selected_endorse_id").val('');
    $("#selected_endorse_type").val('');
});

var jscall = false;
$(window).scroll(function () {
    //  if($(window).scrollTop() + $(window).height() == $(document).height()) {
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
        var keyword = $("#searchendorsements").val();
        console.log("pagenumber : "+pagenumber + "  // totaluserpages : " + totaluserpages);
        if (pagenumber <= totaluserpages) {
            var curl = siteurl + 'cajax/searchActiveUser';
            var formData = {page: pagenumber};
            formData = {page: pagenumber, keyword: keyword, limit: 50};
            if (jscall == false) {
                jscall = true;
                $.ajax({
                    url: curl,
                    type: "POST",
                    data: formData,
                    beforeSend: function () {
                        $(".hiddenloader").removeClass("hidden");
                    },
                    success: function (response)
                    {
                        $("#endorsementlist").append(response);
                        pagenumber = pagenumber + 1;
                        console.log("NEW PAGE NUMBER : " + pagenumber);
                        jscall = false;
                        $(".hiddenloader").addClass("hidden");
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {

                    }
                });
            }
        }else{
            $(".hiddenloader").addClass("hidden");
        }
    }
});