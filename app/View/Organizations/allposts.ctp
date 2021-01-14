<?php
echo $this->Html->script("highcharts");
echo $this->Html->script("modules/exporting");
echo $this->Html->script("modules/no-data-to-display");
?>
<?php
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
    "country" => @$companydetail["country"],
);
$orgname = @$companydetail['name'];
$orgid = $organization_id;
$industry = array('Posts' => 'Posts', 'Users' => 'Users');
?>

<div class="row row-padding"> <?php echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'other')); ?>
    <div class="col-md-7 row comp-name"> 
        <h3 style="color: burlywood;"><strong>Posts Clicks Report</strong></h3>
    </div>

    <?php /* ?>
      <div class="col-md-2">
      <?php
      $org_image = $companydetail['image'];
      if($org_image==""){
      echo $this->Html->image('img1.png',array('class'=>"img-responsive", 'width' => '175'));
      //echo $this->Html->image($companydetail['healthurl'],array('class'=>"img-responsive smiley", "width" => "40"));
      }else{
      $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR  .$org_image;
      echo $this->Html->image($org_imagenew, array('width'=>'175','id'=>'org_image'));
      //echo $this->Html->image($companydetail['healthurl'],array('class'=>"img-responsive smiley", "width" => "40"));
      }

      ?>
      </div>
      <div class="col-md-3 comp-name">
      <?php

      echo '<h2>'.$this->Html->link($orgname,array('controller'=>'users','action'=>'editorg',$orgid));
      echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid))).'</h2>';
      echo '<h3>'.$companydetail['shortname'].'</h3>'
      ?>
      <p><?php echo $companydetail["street"]; if(!empty($companydetail["street"])){echo ", ";}?> <?php echo $companydetail["city"];?></p>
      <p><?php echo $companydetail["state"];if(!empty($companydetail["state"])){echo ", ";}?> <?php echo $companydetail["zip"];?></p>
      </div>
      <?php */ ?>
    <?php echo $this->Form->Create("daterangerandc"); ?>

    <div class="col-md-7">
        <div class="row date-range" style="margin-top:85px;">
            <div class="col-md-3" >
                <h4 class="date-range">Select Date Range</h4>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <input id="startdaterandc" readonly="readonly" placeholder="Start Date" name="startdaterandc" type='text'value="<?php echo $this->Time->Format($datesarray["startdate"], DATEFORMAT); ?>" class="form-control datepickerrandc"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <input id="enddaterandc" name="enddaterandc" readonly="readonly" placeholder="End Date" type='text' value="<?php echo $this->Time->Format($datesarray["enddate"], DATEFORMAT); ?>" class="form-control datepickerrandc"/>
                </div>
            </div>
            <div class="col-md-3">
                <!--                <button type="submit" class="btn btn-info btn-xs datesubmitter">Apply</button>-->
                <button id="resetdates" title="Click to Reset Date"  class="btn btn-info btn-xs resetendorsementsfilters" type="button">Reset Date</button>
            </div>

            <!--            <div class="col-md-4" id="userlistdiv" style="display: block;">
                            <div class="select-style"> <?php echo $this->Form->input('user_id', array('empty' => 'All Users', 'label' => false, 'options' => $orgUserList, 'selected' => $selected_user_id, 'class' => 'form-control')); ?> </div>
                        </div>-->
        </div>
        <div class="form-group">
<!--            <input type="hidden" name="report_type" class="report_type" value="Posts">-->

            <!--            <div class="col-md-3">
                            <div class="input-group">
                                <div id="radioBtn" class="btn-group">
                                    <a class="btn btn-primary btn-sm active postclick" data-value="Users" data-toggle="fun" data-title="Y">Post click data of Org</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 hidden">
                            <div class="input-group">
                                <div id="radioBtn" class="btn-group">
                                    <a class="btn btn-primary btn-sm notActive postclick"data-value="Posts" data-toggle="fun" data-title="X">Post click data of Users</a>
                                </div>
                            </div>
                        </div> -->

            <!-- <div class="Post-Data hidden">
                <span class="radio">
                    <div class="input radio">
                        <div class="col-md-3">
                            <input type="radio" name="PostData" id="OrgPost"  checked="checked" >
                                <label for="OrgPost">Post click data of Org</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" name="PostData" id="UserPost" value="0">
                                <label for="UserPost">Post click data of Users</label>
                        </div>
                    </div>
                </span>
            </div> -->
            <button type="submit" class="hidden btn btn-info btn-xs datesubmitter">Apply</button>
        </div>

        <div  class="hidden"> 
            <!--<div class="col-md-1"></div> -->
            <div class="col-md-2 report-by">
                <h4 class="labelCus">Report By</h4>
            </div>
            <div class="col-md-3">
                <div class="select-style"> <?php // echo $this->Form->input('report_type', array('empty' => 'Select Type', 'label' => false, 'options' => $industry, 'selected' => $selectedType, 'class' => 'form-control'));            ?> </div>
            </div>
            <div class="col-md-4" id="userlistdiv" style="display: block;">
                <div class="select-style"> <?php // echo $this->Form->input('user_id', array('empty' => 'All Users', 'label' => false, 'options' => $orgUserList, 'selected' => $selected_user_id, 'class' => 'form-control'));           ?> </div>
            </div>
            <!--<button type="submit" class="btn btn-info btn-xs datesubmitter">Apply</button>-->
        </div>

    </div>
    <?php
    $userChecked = $postChecked = '';
    if ($selectedType == 'Users') {
        $userChecked = 'checked="cheked"';
    } else {
        $postChecked = 'checked="cheked"';
    }
    ?>
    <div class="col-md-12 Post-Data">
        <span class="radio">
            <div class="input radio">
                <div class="col-md-4 text-right ">
                    <input type="radio" name="report_type" id="OrgPost" class="postclick"  value="Posts" <?php echo $postChecked; ?>>
                    <label for="OrgPost">POST DATA</label>
                </div>
                <div class="col-md-3 text-left">
                    <input type="radio" name="report_type" class="postclick" id="UserPost" value="Users" <?php echo $userChecked; ?>>
                    <label for="UserPost">USER DATA</label>
                </div>
        </span>

        <?php if ($selectedType == 'Users') { ?>
            <div class="col-md-4" id="userlistdiv" style="display: block; margin-left:-26px;">
                <div class="select-style"> <?php echo $this->Form->input('user_id', array('empty' => 'All Users', 'label' => false, 'options' => $orgUserList, 'selected' => $selected_user_id, 'class' => 'js-example-basic-single form-control')); ?> </div>
            </div>
        <?php } else { ?>
            <div class="col-md-4" id="userlistdiv" style="display: block; margin-left:-26px; ">
                <div class="select-style"> <?php echo $this->Form->input('user_id', array('label' => false, 'options' => $orgUserList, 'selected' => $selected_user_id, 'class' => 'js-example-basic-single form-control')); ?> </div>
            </div>
        <?php } ?>
        <button type="submit" class="btn btn-success datesubmitter">Apply</button>
    </div>
    <?php echo $this->Form->End(); ?>
</div>
<br>
<div class="clearfix row row-padding"></div>
<section>
    <div class="row">
        <!--        <div class="search-icn col-md-12" style="margin-bottom:10px;"> <a id="samplelink" class="hidden" href='test.php'>Link</a> 
                    <input type="text" class="form-control" id="searchallendorsement" onkeyup="searchallendorsement(this.value)" placeholder="Filter Items...">
                    <input type="text" class="form-control" id="searchallendorsement" placeholder="Filter Items...">
                </div>-->
        <?php if ($selectedType == 'Users') { ?>

            <?php if (!empty($jobtitles) || !empty($departments) || !empty($entities)) { ?>
                <div class="" style="color:#fff;">
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
                            ?> </div>
                        <div class="col-md-2">
                            <div style="vertical-align:bottom; display:table-cell; height:170px;">
                                <h4></h4>
                                <button type="submit" id="submitfilterpost" class="btn btn-success">Apply</button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } ?>
            <div class="col-md-3 mt10 mb15 ml15"> </div>
            <div class="clearfix"></div>
            <div class="search-icn col-md-12" style="margin-bottom:10px;"> <a id="samplelink" class="hidden" href='test.php'>Link</a> 
                <input type="text" class="form-control" id="searchallpost" placeholder="Filter Items...">
            </div>



            <div class="col-md-12 charts-height" id="allendorsementsdata">
                <?php //  pr($allPostData);    ?>
                <div data-example-id="striped-table"  class="row bs-example">
                    <div class="pull-left col-md-3">

                        <h6><strong>Total Users:-<span id="totalendorsements"><?php echo count($allPostData); ?></span></strong></h6>
                    </div>
                    <div class="leaderborad"> Post Click Data : <?php echo $selectedUserName = ($selectedUserName == 'All') ? 'All Users' : $selectedUserName; ?></div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake.png" alt="" /> </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <?php //echo $this->Html->Link("Save as Spreadsheet", array("controller" => "organizations", "action" => "export", '?' => array('orgid' => $organization_id, 'information' => 'allendorsement')));    ?>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="saveallposts('allendorsementsearching',<?php echo $organization_id; ?>, 'allpostsusers', 'both', '<?php echo $selectedUserName; ?>')">Save as Spreadsheet</a></li>
                                    <li><a href="javascript:void(0)" rel="allendorsements" class="btn-Preview-Image">Print</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="table-responsive"> 
                        <?php // pr($allPostData); //exit;    ?>
                      <!--<p>Total Endorsements:-<span id="totalendorsements"><?php //echo count($allPostData);              ?></span></p> -->
                        <div class="scroll-header">
                            <table class="table table-striped table-hover" >
                                <thead>
                                <div id="loadingdata" style="text-align: center"></div>
                                <tr id="tableheader">
                                    <th><div class="col-endor"> User</div></th>
                                    <th><div class="col-endor"> Title</div></th>
                                    <th><div class="col-endor"> Department</div></th>
                                    <th><div class="col-endor"> Sub Org</div></th>
                                    <th style="text-align:center"><div class="endor-date"> Total Post Clicks</div></th>
                                    <th style="text-align:center"><div class="col-endor">Total Clicks on Paper Clip </div></th>
                                    <th style="text-align:center"><div class="col-endor">Total Attachment Clicks</div></th>
                                    <th style="text-align:center"><div class="endor-date">Total Clicks (Paper Clip + Attachments)</div></th>
                                    <th style="text-align:center"><div class="endor-date">Total Post Like Clicks</div></th>
                                </tr>
                                </thead>

                                <tbody id="allendorsementsearching">
                                    <?php echo $this->Element("allpostslisting", array("allPostData" => $allPostData, 'showAttachments' => true)); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else if ($selectedType == 'Posts') { ?>
            <div class="col-md-12 charts-height" id="allendorsementsdata">
                <?php
                //pr($allPostData); EXIT; 
                $total_clicks = $total_attachment_clicks = $total_attachment_pin_clicks = 0;
                foreach ($allPostData as $index => $data) {
                    $total_clicks = $total_clicks + $data[0]['total_post_click'];
                    $total_attachment_clicks = $total_attachment_clicks + $data[0]['total_attachment_click'];
                    $total_attachment_pin_clicks = $total_attachment_pin_clicks + $data[0]['total_attachment_pin_click'];
                }
                ?>
                <div data-example-id="striped-table"  class="row bs-example">
                    <div class="pull-left col-md-3">

                        <h6><strong>Total Posts:-<span id="totalendorsements"><?php echo count($allPostData); ?></span></strong></h6>
                        <h6><strong>Total Post Clicks (PC):-<span id="totalendorsements"><?php echo $total_clicks; ?></span></strong></h6>
                        <h6><strong>Total Click Post Attachment Paper Clip:-<span id="totalendorsements"><?php echo $total_attachment_pin_clicks; ?></span></strong></h6>
                        <h6><strong>Total Clicks Post PDF Attachment:-<span id="totalendorsements"><?php echo $total_attachment_clicks; ?></span></strong></h6>
                    </div>
                    <div class="leaderborad"> Report : <?php
                        $selectedUserName = ($selectedUserName == 'All') ? 'All Users' : $selectedUserName;
                        echo $selectedUserName;
                        ?></div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake.png" alt="" /> </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <?php //echo $this->Html->Link("Save as Spreadsheet", array("controller" => "organizations", "action" => "export", '?' => array('orgid' => $organization_id, 'information' => 'allendorsement')));    ?>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="saveallposts('allendorsementsearching',<?php echo $organization_id; ?>, 'allposts', 'both', '<?php echo $selectedUserName; ?>')">Save as Spreadsheet</a></li>
                                    <li><a href="javascript:void(0)" rel="allendorsements" class="btn-Preview-Image">Print</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="table-responsive postreport"> 
                        <?php // pr($allPostData); //exit;    ?>
                      <!--<p>Total Endorsements:-<span id="totalendorsements"><?php //echo count($allPostData);              ?></span></p> -->
                        <table class="table table-striped table-hover" >
                            <thead>
                                <tr id="tableheader">
                                    <th style="text-align:center;width: 40%;"><div class="col-endor" style="text-align:center;"> Post</div></th>
                                    <th style="text-align:center;width: 20%;"><div class="col-endor" style="text-align:center;"> Post Clicked</div></th>
                                    <th style="text-align:center;width: 20%;"><div class="col-endor" style="text-align:center;"> Clicked on Paper Clip</div></th>
                                    <th style="text-align:center;width: 20%;"><div class="col-endor" style="text-align:center;"> Clicked on Attachment</div></th>
                                    <!--<th style="text-align:center;width: 20%;"><div class="col-endor" style="text-align:center;">Date & Time Stamp</div></th>-->
                                </tr>
                            </thead>
                        </table>
                        <div class="scroll-header">
                            <table class="table table-striped table-hover" >
                                <!--<thead><div id="loadingdata" style="text-align: center"></div></thead>-->
                                <!--                                <div id="loadingdata" style="text-align: center"></div>
                                                                <tr id="tableheader">
                                                                    <th style="text-align:center;width: 40%;"><div class="col-endor" style="text-align:center;"> Post</div></th>
                                                                    <th style="text-align:center;"><div class="col-endor" style="text-align:center;"> Has this user clicked on this post (Yes /No)</div></th>
                                                                    <th style="text-align:center;"><div class="col-endor" style="text-align:center;"> Has this user clicked on Paper Clip or Web App Attachment Yes/No</div></th>
                                                                    <th style="text-align:center"><div class="col-endor" style="text-align:center;">Date & Time Stamp</div></th>
                                                                </tr>
                                                                </thead>-->

                                <tbody id="allendorsementsearching">
                                    <?php echo $this->Element("allpostsuserslisting", array("allPostData" => $allPostData, 'showAttachments' => true)); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
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
<script>
    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function () {
        $('.js-example-basic-single').select2();
    });
    $(".postclick").on("click", function () {
//        var reportType = $(this).attr("data-value");
//        $(".report_type").val(reportType);
        $(".datesubmitter").click();
    });


//
//    $(document).ready(function () {
//        var reportType = "<?php echo $selectedType; ?>";
//        if (reportType == 'Users') {
//            $("#userlistdiv").show();
//        } else {
//            $("#userlistdiv").hide();
//        }
//    });
//
//    $("#daterangerandcReportType").on("change", function () {
//        var reportType = $(this).val();
//        console.log(reportType);
//        if (reportType == 'Users') {
//            $("#userlistdiv").fadeIn('medium');
//        } else {
//            $("#userlistdiv").fadeOut('medium');
//        }
//    });

    $('#radioBtn a').on('click', function () {
        var sel = $(this).data('title');
        var tog = $(this).data('toggle');
        $('#' + tog).prop('value', sel);

        $('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
        $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
    })
</script>