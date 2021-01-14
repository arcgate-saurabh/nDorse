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
?>

<div class="row row-padding"> <?php echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'other')); ?>
    <input type="hidden" value="<?php echo $organization_id; ?>" id="randcorgid">
    <div class="col-md-7"> 
        <?php echo $this->Html->link("New Reports*", array('controller' => 'organizations', 'action' => 'orgreport', $orgid), array('class' => 'btn btn-warning')); ?>
        <?php echo $this->Html->link("All nDorsements", array('controller' => 'organizations', 'action' => 'allendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Guest nDorsements", array('controller' => 'organizations', 'action' => 'guestendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Posts", array('controller' => 'organizations', 'action' => 'allposts', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All DAISY Nomination", array('controller' => 'organizations', 'action' => 'daisyendorsements', $orgid), array('class' => 'btn btn-success')); ?>

        <div class="row date-range" style="margin-top:85px;">
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
        </div>
    </div>
</div>

<div>
    <div class="reportFilterForm" style="margin-top: 15px;">
        <label>Filter&nbsp;Reports</label>
        <select class="form-control select-report-type">
            <option value="1">Leader Board</option>
            <option value="2">nDorsement History By Day</option>
            <option value="3">Weekly nDorsement History By Department</option>
            <option value="4">nDorsement History By Department</option>
            <option value="5">nDorsement History By Title</option>
            <!--<option value="6">nDorsement History By Sub Org</option>-->
        </select>
    </div>
</div>

<div class="clearfix">
</div>
<div style="display:none">
    <img name="img_val" id="img_val"  >
</div>
<section id="chart_1" class="charts">
    <div class="row">
        <div class="col-md-12 charts-height" id="chart_1_table_data">
            <?php echo $this->Element("leaderboarddata"); ?>
        </div>
    </div>
</section>
<!-- Chart 2 START-->
<section id="chart_2" class="charts hidden">
    <div class="row">
        <div class="col-md-12 charts-height">
            <div class="row">
                <div class="" style="position:absolute; right:60px; z-index:10;">
                    <div class="btn-controll">
                        <?php
                        echo $this->Html->image('fullview.png', array('class' => "img-responsive full-view", "onclick" => "reportsandchartszoom(" . $organization_id . ", 'history_by_day')"));
                        ?>
                    </div>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example chart-container">
                <div class="table-responsive" id="chart_2_table_data">

                </div>
            </div>
        </div>
    </div>
</section>

 <!--Chart 3 Start-->
<section id="chart_3" class="charts hidden">
    <div class="row">
        <div class="col-md-12 charts-height">
            <div class="row">
                <div class="" style="position:absolute; right:60px; z-index:10">
                    <?php
                    echo $this->Html->image('fullview.png', array('class' => "img-responsive full-view", "onclick" => "reportsandchartszoom(" . $organization_id . ", 'history_by_department')"));
                    ?>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example" id="chart_3_table_data">

            </div>
        </div>
    </div>
</section>
<!-- Chart 3 END

 Chart 4 Start -->
<section id="chart_4" class="charts hidden">
    <div class="row">
        <div class="col-md-12 charts-height">
            <div class="row">
                <div class="" style="position:absolute; right:60px; z-index:10">
                    <?php
                    echo $this->Html->image('fullview.png', array('class' => "img-responsive full-view", "onclick" => "reportsandchartszoom(" . $organization_id . ", 'by_department')"));
                    ?>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example" >
                <div class="table-responsive" id="chart_4_table_data">

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart 4 END 


 job title charts
 Chart 5 START -->
<section id="chart_5" class="charts hidden">
    <div class="row">
        <div class="col-md-12 charts-height">
            <div class="row">
                <div class="" style="position:absolute; right:60px; z-index:10">
                    <?php
                    echo $this->Html->image('fullview.png', array('class' => "img-responsive full-view", "onclick" => "reportsandchartszoom(" . $organization_id . ", 'by_jobtitle')"));
                    ?>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive" id="chart_5_table_data">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart 5 END 

 Chart 6 Start -->
<section id="chart_6" class="charts hidden">
    <div class="row">
        <div class="col-md-12 charts-height">
            <div class="row">
                <div class="" style="position:absolute; right:60px; z-index:10">
                    <?php
                    echo $this->Html->image('fullview.png', array('class' => "img-responsive full-view", "onclick" => "reportsandchartszoom(" . $organization_id . ", 'by_suborganization')"));
                    ?>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive" id="chart_6_table_data">

                </div>
            </div>
        </div>
    </div>
</section>
<!-- Chart 6 END -->



<!--<section>
    <div class="row">
        <div class="col-md-12">
            <iframe width="933" height="700" src="https://app.powerbi.com/view?r=eyJrIjoiYjBjN2QzYmYtOWYzNC00YTIzLWExNjctYjRiYmZlZjUxNTBiIiwidCI6Ijk3NWE2OWQ4LTBkOTMtNDNiYy04NDc2LWE3NDhhMDFlZDVjZCIsImMiOjJ9" frameborder="0" allowFullScreen="true"></iframe>
        </div>
    </div>
</section>-->

<div class="modal fade" id="myModal2_commonrandc" role="dialog">
    <div class="modal-dialog"> 
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center">
                <h4 class="modal-title"></h4>
                <div id="bodytext"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('.select-report-type').on("change", function () {
            $('.reports-section').fadeOut(200);
            var reportNumber = $(this).val();

            console.log(reportNumber);
            var reportName = 'leaderboard';
            var organizationID = '<?php echo $organization_id; ?>';

            switch (reportNumber) {
                case '1':
                    reportName = 'leaderboard';
                    break;
                case '2':
                    reportName = 'ndorsement_history_day';
                    break;
                case '3':
                    reportName = 'weekly_ndorsement_histoty_dept';
                    break;
                case '4':
                    reportName = 'ndorsement_histoty_dept';
                    break;
                case '5':
                    reportName = 'ndorsement_histoty_title';
                    break;
                case '6':
                    reportName = 'ndorsement_histoty_sub_org';
                    break;

                default:
                    reportName = 'leaderboard';
                    break;
            }


            var startdaterandc = $("#startdaterandc").val();
            var enddaterandc = $("#enddaterandc").val();
//            reportNumber = 1;
            $.ajax({
                type: "POST",
                url: siteurl + 'reports/' + reportName,
                dataType: 'html',
                data: {organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
                success: function (response) {
                    console.log(response);

                    $(".charts").addClass('hidden');
                    $("#chart_" + reportNumber + "_table_data").html('');
                    $("#chart_" + reportNumber + "_table_data").html(response);
                    $("#chart_" + reportNumber).removeClass('hidden');
                    $("#leaderboardtable").tablesorter();
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });



        });

    });

</script>
