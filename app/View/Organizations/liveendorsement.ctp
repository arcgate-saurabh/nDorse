<?php
$data = array(
    "textcenter" => "Live nDorsement",
    "righttabs" => "3",
    "orgid" => $orgdata['Organization']['id'],
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
    "id" => $orgdata['Organization']['id'],
    "image" => $orgdata['Organization']['image'],
    "name" => $orgdata['Organization']['name'],
    "sname" => $orgdata['Organization']['short_name'],
    "street" => $orgdata['Organization']['street'],
    "city" => $orgdata['Organization']['city'],
    "state" => $orgdata['Organization']['state'],
    "zip" => $orgdata['Organization']['zip'],
    "country" => $orgdata['Organization']['country'],
);
$org_image = $orgdata['Organization']['image'];
?>

<!-- Page Content -->

<div id="page-content-wrapper">
    <div class="lady-lake">
        <div class="row" style="border-bottom:1px solid #fff; padding:20px 0" >
            <input type="hidden" id="orgid" value="<?php echo $orgdata['Organization']['id']; ?>">
            <?php echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'others')); ?>
            <?php /*
              <div class="col-md-2">
              <?php
              $org_image = $orgdata['Organization']['image'];
              if($org_image==""){
              echo $this->Html->image('img1.png',array('class'=>"img-responsive", 'width' => '175'));
              //echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));

              }else{
              $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR  .$org_image;
              echo $this->Html->image($org_imagenew,array('width'=>'175','id'=>'org_image'));
              //echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));
              }
              ?>
              </div>
              <div class="col-md-3 comp-name">
              <?php
              $orgname = $orgdata['Organization']['name'];
              $orgid = $orgdata['Organization']['id'];

              echo '<h2>'.$this->Html->link($orgname,array('controller'=>'users','action'=>'editorg',$orgid));
              echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid))).'</h2>';
              echo '<h3>'.$orgdata['Organization']['short_name'].'</h3>';?>
              <p><?php echo $companydetail["street"]; if(!empty($companydetail["street"])){echo ",";}?> <?php echo $companydetail["city"];?></p>
              <p><?php echo $companydetail["state"];if(!empty($companydetail["street"])){echo ",";}?> <?php echo $companydetail["zip"];?></p>

              </div>

              <?php */ ?>

            <div class="col-md-4 org-status">
                <div id="orgstatus_<?php echo $orgdata['Organization']['id']; ?>">
                    <h3>Organization Status: <?php echo ($orgdata['Organization']['status'] == 1) ? "Active" : "Inactive"; ?></h3>
                </div>
                <table>
                    <tr>
                        <td>Total Users:</td>
                        <td id="totalusers"><?php echo $companydetail["totalusers"]; ?></td>
                    </tr>
                    <tr>
                        <td>Invitation Sent:-</td>
                        <td><?php echo $companydetail["invitation_sent"]; ?></td>
                    </tr>
                    <tr>
                        <td>Invitation Accepted:</td>
                        <td><?php echo $companydetail["invitation_accepted"]; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-3">
<?php echo $this->Element("endorsementcounter", array("endorsementformonth" => $companydetail["endorsementformonth"])); ?>
            </div>
        </div>
<?php /* ?>


  <div class="row">
  <div class="col-lg-3 col-md-3 col-sm-12">
  <?php
  $org_image = $orgdata['Organization']['image'];
  if($org_image==""){
  echo $this->Html->image('img1.png',array('class'=>"img-responsive"));
  echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "50px", "heigh" => "50px"));

  }else{
  $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR  .$org_image;
  echo $this->Html->image($org_imagenew,array('width'=>'270','height'=>'180','id'=>'org_image'));
  echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "50px", "heigh" => "50px"));
  }
  ?>
  </div>
  <div class="col-lg-9 col-md-9 col-sm-12">
  <div class="row">
  <div class="col-md-6 col-sm-12 our-lady" align="left"> <a href="#"><?php echo $companydetail["name"];?></a> </div>
  <div class="col-md-6 col-sm-12" align="right"> <a href="#" rel="one" class="dots"><!--<img src="img/3dots.png" alt="img">--></a>
  <div class="arrow_box one" style="position:absolute; right:10px;z-index:2;">
  <div style="border:0px solid #f00; margin-top:-35px; margin-right:5px;" class="pull-right"><img src="img/popupArrow.png" alt=""/></div>
  </div>

  </div>
  </div>
  <div class="row content-color squareblock">
  <div class="col-md-3 col-sm-3">
  <h3><?php echo $companydetail["shortname"];?></h3>
  <p><?php echo $companydetail["street"]; if(!empty($companydetail["street"])){echo ",";}?> <?php echo $companydetail["city"];?></p>
  <p><?php echo $companydetail["state"];if(!empty($companydetail["street"])){echo ",";}?> <?php echo $companydetail["zip"];?></p>
  </div>
  <div class="col-md-5">
  <h3>Organization Status: <?php echo ($orgdata["Organization"]["status"] == 1) ? "Active": "Inactive";?></h3>
  <dl class="dl-horizontal">
  <dt class="text-left">Total Users:</dt>
  <dd><?php echo $companydetail["totalusers"];?></dd>
  <dt>Invitation Sent:</dt>
  <dd><?php echo $companydetail["invitation_sent"];?></dd>
  <dt>Invitation Accepted:</dt>
  <dd><?php echo $companydetail["invitation_accepted"];?></dd>

  </dl>
  </div>
  <div class="col-md-4">
  <h3>&nbsp;</h3>
  <div class="endrosd-month">
  <p>Endorsements for the month</p>
  <h3><?php echo $companydetail["endorsementformonth"];?></h3>
  </div>
  </div>
  </div>
  </div>
  </div>

  <?php */ ?>
        <section>
            <input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">
<?php if (!empty($orgdata["Endorsement"])) { ?>
                <div class="search-icn ">
                    <input type="text" class="form-control" id="searchliveendorsements"  placeholder="Filter Items..." >
                    <div id="livesearch"></div>

                </div>

<?php
} else {
    pr("No Data Available");
}
?>
            <input type="hidden" id="endorsementorgid" value="<?php echo $orgdata['Organization']['id']; ?>">
        </section>

        <div id="searchendorsement">
            <?php
            //==============binding element to show data
            echo $this->Element("livesearchdata", $orgdata);
            ?>
        </div>
        <div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
        <input id="pagename" value="liveendorsements" type="hidden">
    </div>
