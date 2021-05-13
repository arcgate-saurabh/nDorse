<?php
echo $this->Html->script("highcharts");
echo $this->Html->script("modules/exporting");
echo $this->Html->script("modules/no-data-to-display");
$data = array(
    "textcenter" => "Organization Info",
    "righttabs" => "3",
    "orgid" => $organization_id
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));
$orgdetails = array(
    "id" => $organization_id,
    "image" => $companydetail["image"],
    "name" => $companydetail["name"],
    "sname" => $companydetail["shortname"],
    "street" => $companydetail["street"],
    "city" => $companydetail["city"],
    "state" => $companydetail["state"],
    "zip" => $companydetail["zip"],
    "country" => $companydetail["country"],
);
$orgname = $companydetail['name'];
$orgid = $organization_id;

//pr($subCenterArray);
?>

<div class="row "> 
    <?php //  echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'other'));   ?>
    <div class="col-md-5 col-xs-6">
        <div class="comp-name">
            <?php
            echo '<h2>' . $this->Html->link($orgdetails["name"], array('controller' => 'organizations', 'action' => 'info', $orgdetails["id"]), array("target" => '_blank')) . '</h2>';
            ?>
            <h4><?php echo $orgdetails["sname"]; ?></h4>
            <b>Total Number of nDorsements : <?php echo $allvaluesendorsement; ?>
                <br/>
                Total nDorsements for Current Month : <?php echo $allvaluesendorsementMonthly; ?> </b>
            <br/> 
            <br/>


        </div>
    </div>


    <input type="hidden" value="<?php echo $organization_id; ?>" id="randcorgid">
    <div class="col-md-7"> 
        <?php // echo $this->Html->link("New Reports*", array('controller' => 'organizations', 'action' => 'orgreport', $orgid), array('class' => 'btn btn-warning'));  ?>
        <?php echo $this->Html->link("All nDorsements", array('controller' => 'organizations', 'action' => 'allendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Guest nDorsements", array('controller' => 'organizations', 'action' => 'guestendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Posts", array('controller' => 'organizations', 'action' => 'allposts', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All DAISY Nomination", array('controller' => 'organizations', 'action' => 'daisyendorsements', $orgid), array('class' => 'btn btn-success')); ?>

        <!--        <div class="row date-range" style="margin-top:15px;">
                    <div class="col-md-3" >
                        <h4 class="date-range">Select Date Range</h4>
                    </div>
        <?php echo $this->Form->Create("daterangerandc"); ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input id="startdaterandc" readonly="readonly" name="startdaterandc" type='text'value="<?php echo $this->Time->Format($datesarray["startdate"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="Start Date"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input id="enddaterandc" readonly="readonly" name="enddaterandc" type='text' value="<?php echo $this->Time->Format($datesarray["enddate"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="End Date"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-xs btn-info datesubmitter">Apply</button>
                        <button id="resetdates" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters" type="button">Reset Date</button>
        <?php echo $this->Form->End(); ?> </div>
                </div>-->
    </div>
</div>
<div style="display:none"><img name="img_val" id="img_val"  ></div>

<!--<section id="" class="" style="">-->
<div class="col-md-12 row-padding" style="border-bottom: 0px;">
    <br/>
    <div class="clearfix"></div>

    <ul class="nav nav-tabs">
        <li class="<?php echo ($activeTab == 'data_summary') ? "active" : ""; ?>">
            <a data-toggle="tab" href="#data_summary" style="border: 1px solid aliceblue;">Data Summary</a>
        </li>
        <li <?php echo ($activeTab == 'menu1') ? "active" : ""; ?>>
            <a data-toggle="tab" href="#menu1"  style="border: 1px solid aliceblue;" id="leaderboardreporttabs" >Leaderboard/nDorsement Data</a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="data_summary" class="tab-pane fade in active">
            <div class="col-md-12" style="margin-top: 10px; "> 
                <div class="col-md-7" style="float: right;"> 
                    <div class="row date-range" style="margin-top:15px;">
                        <div class="col-md-3" >
                            <h4 class="date-range">Select Date Range</h4>
                        </div>
                        <?php echo $this->Form->Create("daterangerandc"); ?>
                        <input type="hidden" value="data_summary" name="reporttab" />
                        <div class="col-md-3">
                            <div class="form-group">
                                <input id="startdaterandc" readonly="readonly" name="startdaterandc" type='text'value="<?php echo $this->Time->Format($datesarray["startdate"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="Start Date"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input id="enddaterandc" readonly="readonly" name="enddaterandc" type='text' value="<?php echo $this->Time->Format($datesarray["enddate"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="End Date"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-xs btn-info datesubmitter">Apply</button>
                            <button id="resetdates" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters" type="button">Reset Date</button>
                            <?php echo $this->Form->End(); ?> </div>
                    </div>
                </div>


                <?php echo $this->Element("overallgraph"); ?> 
            </div>
        </div>
        <div id="menu1" class="tab-pane fade">


            <div class="row"> 
                <input type="hidden" value="<?php echo $organization_id; ?>" id="randcorgid">
                <div class="col-md-12">

                    <div class="col-md-7" style="float: left;"> 
                        <div class="row date-range" id="leaderboarddatepicker" style="margin-top:15px;">
                            <div class="col-md-3" >
                                <h4 class="date-range">Select Date Range</h4>
                            </div>
                            <?php echo $this->Form->Create("daterangerandc"); ?>
                            <input type="hidden" value="menu1" name="reporttab" />
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="startdaterandc_1" readonly="readonly" name="startdaterandc_1" type='text'value="<?php echo $datesarray1["startdate_1"]; ?>" class="form-control datepickerrandc" placeholder="Start Date"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="enddaterandc_1" readonly="readonly" name="enddaterandc_1" type='text' value="<?php echo $this->Time->Format($datesarray1["enddate_1"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="End Date"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-xs btn-info datesubmitter leaderboardreportfilterbutton">Apply</button>
                                <button id="resetdates1" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters1 leaderboardreportResetButton" type="button">Reset Date</button>
                                <?php echo $this->Form->End(); ?> </div>
                        </div>
                        <div class="row date-range" id="leaderboarddatepicker2" style="margin-top:15px;">
                            <div class="col-md-3" >
                                <h4 class="date-range">Select Date Range</h4>
                            </div>
                            <?php echo $this->Form->Create("daterangerandc"); ?>
                            <input type="hidden" value="menu1" name="reporttab" />
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="startdaterandc_2" readonly="readonly" name="startdaterandc_2" type='text'value="<?php echo $datesarray1["startdate_1"]; ?>" class="form-control datepickerrandc" placeholder="Start Date"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="enddaterandc_2" readonly="readonly" name="enddaterandc_2" type='text' value="<?php echo $this->Time->Format($datesarray1["enddate_1"], DATEFORMAT) ?>" class="form-control datepickerrandc" placeholder="End Date"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-xs btn-info datesubmitter leaderboardreportfilterbutton2">Apply</button>
                                <button id="resetdates1" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters2 leaderboardreportResetButton" type="button">Reset Date</button>
                                <?php echo $this->Form->End(); ?> </div>
                        </div>
                    </div>


                    <div class="reportFilterForm" style="margin-top: 1%;">
                        <label>Filter&nbsp;Reports</label>
                        <select class="form-control select-report-type">
                            <option value="2">By Leaderboard</option>
                            <option value="1">By History</option>
                            <!--<option value="3">By Department</option>-->
                        </select>
                        <input type="hidden" id="searchleaderboard" value="" /><br/>

                        <button id="saveasspreadsheetleaderboard-new" class="btn btn-xs btn-info" style="padding: 8px 12px;font-size: 13px;margin-left: 10px;">Save As SpreadSheet</button>
                        <div class="reportloader" id="export-loader-img" style="display: none;"></div>
                        <?php
                        //echo '<li><a href="javascript:void(0)" id="saveasspreadsheetleaderboard-new">Save As SpreadSheet</a></li>';
                        ?>
                    </div>

                </div>
            </div>
            <div style="display:none"><img name="img_val" id="img_val"  ></div>

            <!--Loader HIDE SHOW-->
            <div class="reportloader" id="content-loading-img" style="display: none;">Report Loading...</div>
            <!--Loader HIDE SHOW-->

            <section id="report_1" class="reports-section reports" style="display: none;">
                <?php //echo $this->Element("leaderboard_barchart");  ?> 
            </section>

            <section id="report_2" class="reports-section reports" style="">
                <div class="row">
                    <div class="col-md-12"> 
                        <?php //echo $this->Element("leaderboard_grid");  ?> 
                    </div>
                </div>
            </section>

            <section id="report_3" class="reports-section reports" style="display: none;">
                <div class="row">
                    <div class="col-md-12"> 
                        <?php //echo $this->Element("leaderboard_paichart"); ?> 
                    </div>
                </div>
            </section>

        </div>

        <div class="clearfix"></div>
    </div>    
</div>

<!--</section>-->

<script>

    $(document).ready(function () {
//        $('#startdaterandc_1').daterangepicker({}, onSelect);
//        $('#enddaterandc_1').daterangepicker({opens: "left"}, onSelect);
    });



    $(document).on("click", ".leaderboardreportResetButton", function () {
        $('.reports-section').fadeOut(200);
        var reportNumber = '2';
        $('#report_' + reportNumber).fadeIn(800);
        var organizationID = '<?php echo $organization_id; ?>';
        $("#startdaterandc_1").val('');
        $("#enddaterandc_1").val('');
        var startdaterandc = "";
        var enddaterandc = "";
        console.log("#report_" + reportNumber);
        if (reportNumber == 1) {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "none");
            $(document).find("#leaderboarddatepicker").css("display", "none");
            $(document).find("#leaderboarddatepicker2").css("display", "none");

        } else {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "block");
            $(document).find("#leaderboarddatepicker").css("display", "block");
        }

        switch (reportNumber) {
            case '1':
                reportName = 'ndorsement_history_day_weeks';
                break;
            case '2':
                reportName = 'ndorsement_history_leaderboard';
                break;
            case '3':
                reportName = 'ndorsement_history_department';
                break;
            default:
                reportName = 'ndorsement_history_day_weeks';
                break;
        }


        $.ajax({
            type: "POST",
            url: siteurl + 'reports/' + reportName,
            dataType: 'html',
            data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
            success: function (response) {
//                console.log(response);
//                return false;
//                $(".reports").css("display", "none");
                $("#report_" + reportNumber).html('');
                $("#report_" + reportNumber).html(response);
                $("#report_" + reportNumber).css("display", "block");

                $(document).find("#report_" + reportNumber).css("display", "block");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                return false;
            }
        });

    });

    $(document).on("click", ".leaderboardreportfilterbutton", function () {
        $('.reports-section').fadeOut(200);
        var reportNumber = '2';
        $('#report_' + reportNumber).fadeIn(800);
        var organizationID = '<?php echo $organization_id; ?>';
        var startdaterandc = $("#startdaterandc_1").val();
        var enddaterandc = $("#enddaterandc_1").val();
        console.log("#report_" + reportNumber);
        if (reportNumber == 1) {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "none");
            $(document).find("#leaderboarddatepicker").css("display", "none");
            $(document).find("#leaderboarddatepicker2").css("display", "none");

        } else {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "block");
            $(document).find("#leaderboarddatepicker").css("display", "block");
        }

        switch (reportNumber) {
            case '1':
                reportName = 'ndorsement_history_day_weeks';
                break;
            case '2':
                reportName = 'ndorsement_history_leaderboard';
                break;
            case '3':
                reportName = 'ndorsement_history_department';
                break;
            default:
                reportName = 'ndorsement_history_day_weeks';
                break;
        }

        $(".hiddenloader").removeClass("hidden");
        $("#content-loading-img").show();
        $(".leaderBoardReports").hide();

        $.ajax({
            type: "POST",
            url: siteurl + 'reports/' + reportName,
            dataType: 'html',
            data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
            success: function (response) {
//                console.log(response);
//                return false;
//                $(".reports").css("display", "none");
                $(".leaderBoardReports").show();
                $("#report_" + reportNumber).html('');
                $("#report_" + reportNumber).html(response);
                $("#report_" + reportNumber).css("display", "block");
                $(".hiddenloader").addClass("hidden");
                $(document).find("#report_" + reportNumber).css("display", "block");
                $("#content-loading-img").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $(".hiddenloader").addClass("hidden");
                return false;
            }
        });

    });
    $(document).on("click", ".leaderboardreportfilterbutton2", function () {

        var reportNumber = 1;

        $('.reports-section').fadeOut(200);
        $('#report_' + reportNumber).fadeIn(800);
        var organizationID = '<?php echo $organization_id; ?>';
        var startdaterandc = $("#startdaterandc_2").val();
        var enddaterandc = $("#enddaterandc_2").val();
        console.log("#report_" + reportNumber);
        if (reportNumber == 1) {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "none");
            $(document).find("#leaderboarddatepicker").css("display", "none");
            $(document).find("#leaderboarddatepicker2").css("display", "block");

        } else {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "block");
            $(document).find("#leaderboarddatepicker").css("display", "block");
            $(document).find("#leaderboarddatepicker2").css("display", "none");
        }

        switch (reportNumber) {
            case '1':
                reportName = 'ndorsement_history_day_weeks';
                break;
            case '2':
                reportName = 'ndorsement_history_leaderboard';
                break;
            case '3':
                reportName = 'ndorsement_history_department';
                break;
            default:
                reportName = 'ndorsement_history_day_weeks';
                break;
        }

        $(".hiddenloader").removeClass("hidden");
        $.ajax({
            type: "POST",
            url: siteurl + 'reports/' + reportName,
            dataType: 'html',
            data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
            success: function (response) {
//                console.log(response);
//                return false;
//                $(".reports").css("display", "none");
//                renderGraph1();
                $(document).find("#report_" + reportNumber).html('');
                $(document).find("#report_" + reportNumber).html(response);
                $(document).find("#report_" + reportNumber).css("display", "block");
                $(".hiddenloader").addClass("hidden");
                setTimeout(function () {
                    renderGraph1();
                }, 1000);


            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $(".hiddenloader").addClass("hidden");
                return false;
            }
        });

    });


    $(document).on("click", "#leaderboardreporttabs", function () {

//            alert("TSET");
        //$('.select-report-type').on("change", function () {
        $('.reports-section').fadeOut(200);
        var reportNumber = '2';
        $('#report_' + reportNumber).fadeIn(800);
        var organizationID = '<?php echo $organization_id; ?>';
        var startdaterandc = $("#startdaterandc").val();
        var enddaterandc = $("#enddaterandc").val();
        console.log("#report_" + reportNumber);
        if (reportNumber == 1) {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "none");
            $(document).find("#leaderboarddatepicker").css("display", "none");
            $(document).find("#leaderboarddatepicker2").css("display", "none");

        } else {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "block");
            $(document).find("#leaderboarddatepicker").css("display", "block");
            $(document).find("#leaderboarddatepicker2").css("display", "none");
        }

        switch (reportNumber) {
            case '1':
                reportName = 'ndorsement_history_day_weeks';
                break;
            case '2':
                reportName = 'ndorsement_history_leaderboard';
                break;
            case '3':
                reportName = 'ndorsement_history_department';
                break;
            default:
                reportName = 'ndorsement_history_day_weeks';
                break;
        }
        $("#content-loading-img").show();
        $(".hiddenloader").removeClass("hidden");
        $.ajax({
            type: "POST",
            url: siteurl + 'reports/' + reportName,
            dataType: 'html',
            data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
            success: function (response) {
//                console.log(response);
//                return false;
//                $(".reports").css("display", "none");
                $("#report_" + reportNumber).html('');
                $("#report_" + reportNumber).html(response);
                $("#report_" + reportNumber).css("display", "block");
                $(".hiddenloader").addClass("hidden");
                $(document).find("#report_" + reportNumber).css("display", "block");
                $("#content-loading-img").hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $(".hiddenloader").addClass("hidden");
                return false;
            }
        });



    });

    $(document).on("change", ".select-report-type", function () {
//            alert("TSET");
        //$('.select-report-type').on("change", function () {
        $('.reports-section').fadeOut(200);
        var reportNumber = $(this).val();
        $('#report_' + reportNumber).fadeIn(800);
        var organizationID = '<?php echo $organization_id; ?>';
        var startdaterandc = $("#startdaterandc").val();
        var enddaterandc = $("#enddaterandc").val();
        console.log("#report_" + reportNumber);
        if (reportNumber == 1) {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "none");
            $(document).find("#leaderboarddatepicker").css("display", "none");
            $(document).find("#leaderboarddatepicker2").css("display", "block");

        } else {
            $(document).find("#saveasspreadsheetleaderboard-new").css("display", "block");
            $(document).find("#leaderboarddatepicker").css("display", "block");
            $(document).find("#leaderboarddatepicker2").css("display", "none");
        }

        switch (reportNumber) {
            case '1':
                reportName = 'ndorsement_history_day_weeks';
                break;
            case '2':
                reportName = 'ndorsement_history_leaderboard';
                break;
            case '3':
                reportName = 'ndorsement_history_department';
                break;
            default:
                reportName = 'ndorsement_history_day_weeks';
                break;
        }

        $(".hiddenloader").removeClass("hidden");
        $.ajax({
            type: "POST",
            url: siteurl + 'reports/' + reportName,
            dataType: 'html',
            data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
            success: function (response) {
//                console.log(response);
//                return false;
//                $(".reports").css("display", "none");
                $("#report_" + reportNumber).html('');
                $("#report_" + reportNumber).html(response);
                $("#report_" + reportNumber).css("display", "block");

                $(document).find("#report_" + reportNumber).css("display", "block");
                $(".hiddenloader").addClass("hidden");

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                $(".hiddenloader").addClass("hidden");
                return false;
            }
        });


    });
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


    function activeTab(tab) {
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }
    activeTab('<?php echo $activeTab; ?>');



</script>
