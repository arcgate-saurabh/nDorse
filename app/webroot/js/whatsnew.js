var dateparameters = {
    showOn: "button",
    buttonImage: siteurl + "img/calendar.png",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    required: true,
    dpDate: true,
    showAnim: "fadeIn",
    showButtonPanel: true,
    maxDate: '+0d',
    yearRange: "-100:+0",
    //dateFormat: 'yy-mm-dd'
    dateFormat: 'MM yy',
    

};
//===on clicking dates
$(document).on("click", "#topendorsedate", function () {
    var dateentered = $("#datepicker_topendorsedate").val();
    if (dateentered.length == 0) {
        alertbootbox("Date Cannot be empty.")
        return false;
    } else {
        var month = dateentered.split(" ")[0];
        var year = dateentered.split(" ")[1];
        $.ajax({
            type: "POST",
            url: siteurl + 'cajax/getwhatsnewdata',
            data: {month: month, year: year},
            success: function (data, textStatus, xhr) {
                if (data.length > 0) {
                    $("#topendorsedata").html("");
                    $("#topendorsedata").html(data);
                }
            },
        });
    }
})

//=reset dates 
$(document).on("click", "#resettopendorsedate", function () {
    $("#datepicker_topendorsedate").val("");
    $("#topendorsedata").html(firstdata);
})


var firstdata = "";
$(document).ready(function () {
  
    //==removing today button fron date picker
  

    firstdata = $("#topendorsedata").html();
    $("#datepicker_topendorsedate").val("");
    $('#datepicker_topendorsedate').datepicker(dateparameters).focus(function () {
        var thisCalendar = $(this);
        $('.ui-datepicker-calendar').detach();
        $(".ui-datepicker-current").css("display", "none");
        $('.ui-datepicker-close').click(function () {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            thisCalendar.datepicker('setDate', new Date(year, month, 1));
        });
    });
});
