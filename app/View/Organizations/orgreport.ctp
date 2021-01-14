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

<div class="row row-padding"> 
    <?php echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'other')); ?>
    <input type="hidden" value="<?php echo $organization_id; ?>" id="randcorgid">
    <div class="col-md-7"> 
        <?php echo $this->Html->link("All nDorsements", array('controller' => 'organizations', 'action' => 'allendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Guest nDorsements", array('controller' => 'organizations', 'action' => 'guestendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All Posts", array('controller' => 'organizations', 'action' => 'allposts', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("All DAISY Nomination", array('controller' => 'organizations', 'action' => 'daisyendorsements', $orgid), array('class' => 'btn btn-success')); ?>
        <?php echo $this->Html->link("New Reports Overall*", array('controller' => 'organizations', 'action' => 'orgreportoverall', $orgid), array('class' => 'btn btn-warning')); ?>

        <div class="row date-range" style="margin-top:15px;">
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
    <div class="col-md-5">
      <!--  <div class="reportFilterForm">
            <label>Filter&nbsp;Reports</label>
            <select class="form-control select-report-type">
                <option value="2">By Leaderboard</option>
                <option value="1">By History</option>
                <option value="3">By Department</option>
            </select>
        </div> -->
    </div>
</div>
<div style="display:none"><img name="img_val" id="img_val"  ></div>




<section id="report_1" class="reports-section reports" style="display: none;">
    <?php echo $this->Element("leaderboard_barchart"); ?> 
</section>

<section id="report_2" class="reports-section reports" style="">
    <div class="row">
        <div class="col-md-12"> 
            <?php echo $this->Element("leaderboard_grid"); ?> 
        </div>
    </div>
</section>

<section id="report_3" class="reports-section reports" style="display: none;">
    <div class="row">
        <div class="col-md-12"> 
            <?php echo $this->Element("leaderboard_paichart"); ?> 
        </div>
    </div>
</section>





<script>

    $(document).ready(function () {

       



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
    

</script>

