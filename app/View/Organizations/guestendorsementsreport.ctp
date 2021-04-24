<?php
echo $this->Html->script("highcharts");
echo $this->Html->script("modules/exporting");
echo $this->Html->script("modules/no-data-to-display");
?>
<?php
$data = array(
    "textcenter" => "All Guest nDorsements",
    "righttabs" => "3",
    "orgid" => $organization_id,
    "video_feature" => $orgdata['Organization']['featured_video_enabled'],
    "customer_portal" => $orgdata['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgdata['Organization']['enable_daisy_portal']
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
    <div class="col-md-7">
        <div class="row date-range" style="margin-top:85px;">
            <div class="col-md-3" >
                <h4 class="date-range">Select Date Range</h4>
            </div>
            <?php echo $this->Form->Create("daterangerandc"); ?>
            <div class="col-md-3">
                <div class="form-group">
                    <input id="startdaterandc" readonly="readonly" placeholder="Start Date" name="startdaterandc" type='text'value="<?php echo $this->Time->Format($datesarray["startdate"], DATEFORMAT); ?>" class="form-control datepickerrandc"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php //echo DATEFORMAT."/". $datesarray["enddate"]; echo $this->Time->Format($datesarray["enddate"], DATEFORMAT); ?>
                    <input id="enddaterandc" name="enddaterandc" readonly="readonly" placeholder="End Date" type='text' value="<?php echo $this->Time->Format($datesarray["enddate"], DATEFORMAT); ?>" class="form-control datepickerrandc"/>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-info btn-xs datesubmitter">Apply</button>
                <button id="resetdates" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters" type="button">Reset Date</button>
            </div>
            <?php echo $this->Form->End(); ?> </div>
    </div>
</div>
<br>
<?php if (!empty($jobtitles) || !empty($departments) || !empty($entities)) { ?>
    <div class="row" style="color:#fff">
        <div class="col-md-3">
            <button type="button" class="btn btn-info btn-xs" data-toggle="collapse" id="filterby" data-target="#filter-nDorsements" title="Click to filter the data">Filter By</button>
            <button type="button" class="btn btn-info btn-xs resetendorsementsfilters" data-toggle="collapse" title="Click to Reset Filter">Reset Filters</button>
        </div>
        <div class="clearfix"></div>
        <div id="filter-nDorsements" class="collapse">
            <div class="col-md-2" style="overflow:hidden">
                <h4>Title </h4>
                <?php
                if (empty($jobtitles)) {
                    echo $this->Form->input('Job Title', array('empty' => 'No Data Available', 'id' => 'jobtitlefilterempty', 'multiple' => 'multiple', 'label' => false, 'options' => $jobtitles, 'class' => 'form-control'));
                    //echo $this->Form->input('Job Title', array('empty' => 'No Data Available', 'id' => 'jobtitlefilterempty', 'multiple' => 'multiple', 'label' => false, 'option' => $jobtitles, 'class' => 'form-control')); 
                } else {
                    echo $this->Form->input('Job Title', array('id' => 'jobtitlefilter', 'multiple' => 'multiple', 'label' => false, 'options' => $jobtitles, 'class' => 'form-control'));
                }
                ?> </div>
            <div class="col-md-2" style="overflow:hidden">
                <h4>Department </h4>
                <?php
                if (empty($departments)) {
                    echo $this->Form->input('Departments', array('empty' => 'No Data Available', 'id' => 'departmentfilterempty', 'multiple' => 'multiple', 'label' => false, 'options' => $departments, 'class' => 'form-control'));
                } else {
                    echo $this->Form->input('Departments', array('id' => 'departmentfilter', 'multiple' => 'multiple', 'label' => false, 'options' => $departments, 'class' => 'form-control'));
                }
                ?>
            </div>
            <div class="col-md-2" style="overflow:hidden">
                <h4>Sub Organization </h4>
                <?php
                if (empty($entities)) {
                    echo $this->Form->input('entities', array('empty' => 'No Data Available', 'id' => 'entityfilterempty', 'multiple' => 'multiple', 'label' => false, 'options' => $entities, 'class' => 'form-control'));
                } else {
                    echo $this->Form->input('entities', array('id' => 'entityfilter', 'multiple' => 'multiple', 'label' => false, 'options' => $entities, 'class' => 'form-control'));
                }
                ?> 
            </div>
            <div class="col-md-2">
                <div style="vertical-align:bottom; display:table-cell; height:170px;">
                    <h4></h4>
                    <button type="submit" id="submitguestfilterendorsement" class="btn btn-info">Apply</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="row"> 
    <div class="col-md-3"> </div>
</div>
<div class="clearfix"></div>
<section>
    <?php //pr($allvaluesendorsement);?>
    <div class="row">
        <div class="search-icn col-md-12" style="margin-bottom:10px;">
            <a id="samplelink" class="hidden" href='test.php'>Link</a> 
          <!--  <input type="text" class="form-control" id="searchallendorsement" onkeyup="searchallendorsement(this.value)" placeholder="Filter Items...">-->
            <input type="text" class="form-control" id="searchallguestendorsement" placeholder="Filter Items...">
        </div>
        <div class="col-md-12 charts-height" id="allendorsementsdata">
            <div data-example-id="striped-table"  class="row bs-example">
                <div class="pull-left col-md-3">
                    <h6><strong>Total Nominations:-<span id="totalendorsements"><?php echo count($allvaluesendorsement); ?></span></strong></h6>
                </div>
                <div class="leaderborad"> All Guest nDorsements</div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake.png" alt="" /> </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <?php //echo $this->Html->Link("Save as Spreadsheet", array("controller" => "organizations", "action" => "export", '?' => array('orgid' => $organization_id, 'information' => 'allendorsement'))); ?>
                                </li>
                                <li><a href="javascript:void(0)" onclick="saveallendorsement('allendorsementsearching',<?php echo $organization_id; ?>, 'allendorsements', 'guest')">Save as Spreadsheet</a></li>
                                <li><a href="javascript:void(0)" rel="allendorsements" class="btn-Preview-Image">Print</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="table-responsive"> 
                    <?php // pr($allvaluesendorsement); //exit; ?>
                  <!--<p>Total Endorsements:-<span id="totalendorsements"><?php //echo count($allvaluesendorsement);             ?></span></p> -->
                    <div class="scroll-header">
                        <table class="table table-striped table-hover" >
                            <thead>
                            <div id="loadingdata" style="text-align: center"></div>
                            <tr id="tableheader">
                                <th><div class="col-endor">nDorser</div></th>
                                <th><div class="col-endor">nDorsed</div></th>
                                
                                <th><div class="col-endor">nDorsed Department  /Unit</div></th>
                                <th><div class="col-endor">nDorsed Sub center /Facility</div></th>
                                <th><div class="endor-date">nDorsement Date</div></th>
                                <th style="text-align:center"><div class="endor-date">Core Values</div></th>
                                <?php
                                foreach ($orgcorevaluesandcode as $key => $corevalues) {
                                    echo '<th title="' . $corevalues["name"] . '" class="iffyTip1">' . $corevalues["name"] . '</th>';
                                }
                                ?>
                                <th><div class="comment-div">Comments</div></th>
                                <th><div class="comment-div" style="text-align: center !important;">Status</div></th>
                                <!--<th><div class="">Attachment</div></th>-->
                                <!--<th><div class="">Emojis</div></th>-->
<!--                                <th><div class="">Reply to nDorsement</div></th>
                                <th><div class="">Re-reply to nDorsement</div></th>-->
                            </tr>
                            </thead>

                            <tbody id="allendorsementsearching">
                                <?php // pr($allvaluesendorsement); //exit?>
                                <?php echo $this->Element("allguestendorsementslisting", array("allvaluesendorsement" => $allvaluesendorsement, 'showAttachments' => true,'portal' =>'daisy','DAISYAwards'=> $DAISYAwards)); ?>
                                <?php //echo $this->Element("alldaisyendorsementslisting", array("allvaluesendorsement" => $allvaluesendorsement, 'showAttachments' => true, 'portal' => 'daisy', 'DAISYAwards' => $DAISYAwards)); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" id="endorsementorgid" value="<?php echo $organization_id; ?>">
<div class="clearfix"></div>
<div class="modal fade" id="myModalViewImages" tabindex="-1" role="dialog" 
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <!-- Modal Header -->

            <div class="modal-header">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <h3>Attached Images</h3>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">

            </div>
            <div class="clearfix"></div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button id="allendorsements-attachedimages" type="button" class="btn btn-blue pull-left"> Download </button>
                <button type="button" class="btn btn-blue pull-left" data-dismiss="modal"> Close </button>

            </div>
        </div>
    </div>
</div>
