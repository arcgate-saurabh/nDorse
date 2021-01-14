$(document).on('ready', function () {
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    //$(".dots").click(function(e) {
    $(document).on("click", ".dots", function (e) {
        var rel = $(this).attr("rel");
        $("." + rel).toggle();
        $('.dots').not(this).each(function () {
            $(this).siblings(".arrow_box").hide();
        });
    });
    //$('[data-toggle="popover"]').popover();

    $(document).on("click", ".pending-announcement", function (event) {
        //console.log($(this).attr("announcement_id"));
        var clnew = $(event.target).attr('class');
        var deleteClass = 'delete-announcement';
        if (clnew == undefined) {
            window.location.href = siteurl + "users/announcementedit/" + $(this).attr("announcement_id");
        }
        if (clnew.indexOf(deleteClass) != -1) {

            var announcementId = $(this).attr("announcement_id");
            var announcement_block_div_id = "announcement_" + announcementId;
            console.log(announcementId);
            $.confirm({
                title: false,
                content: 'Deleted Announcement will no longer.',
                type: 'red',
                columnClass: 'medium',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function () {
                            $.ajax({
                                type: "POST",
                                url: siteurl + 'users/announcementdelete',
                                data: {announcementId: announcementId},
                                success: function (data, textStatus, xhr) {
                                    //console.log(data); return false;
                                    var jsonparser = $.parseJSON(data);

                                    var status = jsonparser["success"];
                                    //console.log(status); return false;
                                    if (status) {
                                        $("#" + announcement_block_div_id).fadeOut('slow');
                                    }
                                },
                            });
                        }
                    },
                    cancel: function () {
                    }
                }
            });


        } else {
            window.location.href = siteurl + "users/announcementedit/" + $(this).attr("announcement_id");
        }
    });

    $(document).on("click", ".pending-announcement-admin", function (event) {
        //console.log($(this).attr("announcement_id"));
        var clnew = $(event.target).attr('class');
        var deleteClass = 'delete-announcement';
        if (clnew == undefined) {
            window.location.href = siteurl + "organizations/announcementedit/" + $(this).attr("announcement_id");
        }
        if (clnew.indexOf(deleteClass) != -1) {

            var announcementId = $(this).attr("announcement_id");
            var announcement_block_div_id = "announcement_" + announcementId;
            console.log(announcementId);
            $.confirm({
                title: false,
                content: 'Deleted Announcement will no longer.',
                type: 'red',
                columnClass: 'medium',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function () {
                            $.ajax({
                                type: "POST",
                                url: siteurl + 'organizations/announcementdelete',
                                data: {announcementId: announcementId},
                                success: function (data, textStatus, xhr) {
                                    //console.log(data); return false;
                                    var jsonparser = $.parseJSON(data);

                                    var status = jsonparser["success"];
                                    //console.log(status); return false;
                                    if (status) {
                                        $("#" + announcement_block_div_id).fadeOut('slow');
                                    }
                                },
                            });
                        }
                    },
                    cancel: function () {
                    }
                }
            });


        } else {
            window.location.href = siteurl + "organizations/announcementedit/" + $(this).attr("announcement_id");
        }
    });

    setTimeout(function () {
        var oldMessage = $('#oldmessage').val();
        tinyMCE.activeEditor.setContent(oldMessage);
    }, 2000);


    //======SAVE EXCEL MANAGER REPORT

    $("#exportmanagerreport").on("click", function () {
        var startdate = $("#datepicker_start").val();
        var enddate = $("#datepicker_end").val();
        var orgid = $("#orgid").val();
        var orgname = $("#orgname").val();
        var department_id = $("#department_id").val();
        var facility_id = $("#facility_id").val();

        $.ajax({
            type: "POST",
            url: siteurl + 'ajax/managerReportSpreadsheet',
            data: {startdate: startdate, enddate: enddate, orgid: orgid, facility_id: facility_id, department_id: department_id, orgname: orgname},
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



});







