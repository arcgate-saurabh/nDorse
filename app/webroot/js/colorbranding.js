
$(document).ready(function () {

    $(document).on("click", "#orgformcancel", function () {
        //alert('test');
        window.history.back();
    });

    $(document).on("click", "#editorgcolorsubmit", function () {
        $("#OrganizationColorsettingsForm").submit();
    });
});