<?php
$loggedinUser = AuthComponent::user();

$subscriptionConfig = Configure::read("subscription");

echo $this->Html->script("jquercsv");
?>
<?php
$data = array(
    "textcenter" => "Organization Info",
    "righttabs" => "3",
    "orgid" => $orgdata['Organization']['id'],
    "video_feature" => $orgdata['Organization']['featured_video_enabled'],
    "customer_portal" => $orgdata['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgdata['Organization']['enable_daisy_portal']
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2 || $authUser["role"] == 6) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));
$orgdetails = array(
    "id" => $orgdata['Organization']['id'],
    "image" => $orgdata['Organization']['image'],
    "name" => $orgdata['Organization']['name'],
    "sname" => $orgdata['Organization']['short_name'],
    "secret_code" => $orgdata['Organization']['secret_code'],
    "street" => $orgdata['Organization']['street'],
    "city" => $orgdata['Organization']['city'],
    "state" => $orgdata['Organization']['state'],
    "zip" => $orgdata['Organization']['zip'],
    "country" => $orgdata['Organization']['country'],
    "status" => $orgdata['Organization']['status'],
    "theme" => $orgdata['Organization']['theme_type'],
    "short_code" => $orgdata['Organization']['short_code'],
);
$org_image = $orgdata['Organization']['image'];
$orgname = $orgdata['Organization']['name'];
$orgid = $orgdata['Organization']['id'];
?>

<div class="row" style="border-bottom:1px solid #fff; padding:20px 0" >
    <input type="hidden" id="orgid" value="<?php echo $orgdata['Organization']['id']; ?>">
    <?php echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'info')); ?>
    <?php /* ?>
      <div class="col-md-2">
      <?php

      if($org_image==""){
      echo $this->Html->image('img1.png',array('class'=>"img-responsive", 'width' => '175', ));
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
      //print_r($authUser);

      echo '<h2>'.$this->Html->link($orgname,array('controller'=>'users','action'=>'editorg',$orgid));
      echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid))).'</h2>';
      echo '<h3>'.$orgdata['Organization']['short_name'].'</h3>';?>



      </div>
      <?php */ ?>
    <div class="col-md-4 org-status">
        <?php
        if ($orgdata['Organization']['status'] == 1) {
            $subscription_dispaly = "block";
        } else {
            $subscription_dispaly = "none";
        }
        ?>

        <div id="purchase_<?php echo $orgdata['Organization']['id']; ?>" style="display:<?php echo $subscription_dispaly; ?>"  >
            <?php
            $encodedId = urlencode($ViewContFunctions->encodeString($orgdata['Organization']['id']));
            $available_quota = 10;
            if ($authUser["role"] == 1) {


                if ($orgdata["Subscription"]["organization_id"] != $orgdata['Organization']['id']) {
                    ?>
                    <button class="btn btn-xs btn-info" onclick="purchasesubscription('<?php echo $orgdata['Organization']['id']; ?>')">Sell Subscription</button>
                    <?php
                } else {
                    $available_quota += $orgdata["Subscription"]["pool_purchased"];
                    if ($orgdata["Subscription"]["payment_method"] == "ndorse" && $orgdata["Subscription"]["type"] == "paid" && $orgdata["Subscription"]["is_deleted"] == 0) {
                        ?>
                        <button class="btn btn-xs btn-info" onclick="upgradesubscription('<?php echo $orgdata['Organization']['id']; ?>')">Upgrade</button>
                        <button class="btn btn-xs btn-info"  onclick="downgradesubscription('<?php echo $orgdata['Organization']['id']; ?>', '<?php echo ($orgdata["Subscription"]["pool_purchased"]); ?>')">Downgrade</button>
                        <button class="btn btn-xs btn-danger" onclick="terminatesubscription('<?php echo $orgdata['Organization']['id']; ?>')" >Terminate Subscription</button>
                    <?php } elseif ($orgdata["Subscription"]["payment_method"] == "ndorse" & $orgdata["Subscription"]["type"] == "paid" && $orgdata["Subscription"]["is_deleted"] == 1) { ?>
                        <button class="btn btn-xs btn-danger" onclick="revertsubscription('<?php echo $orgdata['Organization']['id']; ?>', '<?php echo $orgdata["Subscription"]["pool_purchased"]; ?>')" >Revert Subscription</button>
                    <?php } elseif ($orgdata["Subscription"]["payment_method"] == "ndorse" && $orgdata["Subscription"]["type"] == "trial") { ?>
                        <button class="btn btn-xs btn-info" onclick="upgradeSubscriptionTrial('<?php echo $orgdata['Organization']['id']; ?>')" >Upgrade</button>
                        <button class="btn btn-xs btn-info" onclick="convertToPaidManual('<?php echo $orgdata['Organization']['id']; ?>', '<?php echo $orgdata["Subscription"]["pool_purchased"]; ?>', '<?php echo ($orgdata["Subscription"]["pool_purchased"] * $subscriptionConfig['annual_price_per_user']); ?>')" >Convert to paid</button>
                        <button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial('<?php echo $orgdata['Organization']['id']; ?>')" >Terminate Subscription</button>
                    <?php } elseif ($orgdata["Subscription"]["payment_method"] == "web" && $orgdata["Subscription"]["is_deleted"] == 0) { ?>
                        <button class="btn btn-xs btn-info" onclick="overwritesubscription('<?php echo $encodedId; ?>')">Override Subscription</button>
                        <button class="btn btn-xs btn-danger" onclick="terminatesubscriptionbybraintree('<?php echo $encodedId; ?>')" >Terminate Subscription</button>
                        <?php
                    } elseif ($orgdata["Subscription"]["payment_method"] == "web" && $orgdata["Subscription"]["is_deleted"] == 1) {
                        // $available_quota+=$orgdata["Subscription"]["pool_purchased"];
                        $deletedby = "admin";
                        // print_r($orgdata);exit;
                        if (!empty($orgdata["Transactions"]) && $orgdata["Transactions"][0]["user_id"] == 1) {
                            $deletedby = "nDorse";
                        } elseif (!empty($orgdata["Transactions"])) {

                            // $deletedby ="admin (".$adminusrarray[$orgdata["Transactions"][0]["user_id"]].")";
                            if ($orgdata["Transactions"][0]["user_id"] == 0) {
                                $deletedby = "admin (payment failed) ";
                            } else {
                                $deletedby = "admin (" . $adminusrarray[$orgdata["Transactions"][0]["user_id"]] . ")";
                            }
                        }
                        ?>
                        <div class="msg">Subscription canceled by <?php echo $deletedby; ?></div>    
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <?php if ($loggedinUser['role'] != 6) { ?>
                    <div id="js_orgAction_<?php echo $orgdata['Organization']['id']; ?>">

                        <?php
                        if ($orgdata["Subscription"]['is_deleted'] == 1 && $orgdata["Subscription"]["type"] == "paid") {
                            $available_quota += $orgdata["Subscription"]["pool_purchased"];
                            $deletedby = "admin";
                            // print_r($orgdata);exit;
                            if (!empty($orgdata["Transactions"]) && $orgdata["Transactions"][0]["user_id"] == 1) {
                                $deletedby = "nDorse";
                            } elseif (!empty($orgdata["Transactions"])) {


                                //$deletedby ="admin (".$adminusrarray[$orgdata["Transactions"][0]["user_id"]].")";
                                if ($orgdata["Transactions"][0]["user_id"] == 0) {
                                    $deletedby = "admin (payment failed) ";
                                } else {
                                    $deletedby = "admin (" . $adminusrarray[$orgdata["Transactions"][0]["user_id"]] . ")";
                                }
                            }
                            echo '<div class="msg">Subscription canceled by ' . $deletedby . '</div>';
                        } else {
                            if ($orgdata["Subscription"]['payment_method'] != "ndorse") {
                                if ($orgdata["Subscription"]["organization_id"] == "" || $orgdata["Subscription"]['is_deleted'] == 1) {
                                    ?>
                                    <?php echo $this->Html->link("Purchase Subscription", array('controller' => 'subscription', 'action' => 'btpurchase', $encodedId), array('class' => 'btn btn-info')); ?>
                                    <?php
                                } else {
                                    $available_quota += $orgdata["Subscription"]["pool_purchased"];
                                    ?>
                                    <button class="btn btn-xs btn-info js_updateSubscription" act="upgrade" og="<?php echo $encodedId; ?>">Upgrade</button>
                                    <button class="btn btn-xs btn-info js_updateSubscription" act="downgrade" og="<?php echo $encodedId; ?>" pp="<?php echo ($orgdata["Subscription"]['pool_purchased'] + FREE_POOL_USER_COUNT) - $activeusercount; ?>">Downgrade</button>
                                    <button class="btn btn-xs btn-danger js_cancelSubscription" og="<?php echo $encodedId; ?>">Cancel Subscription</button>
                                    <?php
                                }
//        } 
//        else {
                                // print_r($orgdata);
//                                            if($orgdata["Subscription"]['is_deleted'] == 1) {
//                                        $available_quota+=$orgdata["Subscription"]["pool_purchased"];
//                             $deletedby ="admin";
//                            // print_r($orgdata);exit;
//                                   if(!empty($orgdata["Transactions"]) && $orgdata["Transactions"][0]["user_id"]==1 )
//                                   {
//                                    $deletedby ="nDorse";
//                                   }elseif(!empty($orgdata["Transactions"])){
//                                    
//                                    
//                                     //$deletedby ="admin (".$adminusrarray[$orgdata["Transactions"][0]["user_id"]].")";
//                                        if($orgdata["Transactions"][0]["user_id"]==0){
//                                      $deletedby ="admin (payment failed) ";               
//                                    }else{
//                                       $deletedby ="admin (".$adminusrarray[$orgdata["Transactions"][0]["user_id"]].")";         
//                                    }
//                                     
//                                   }
//                                                echo '<div class="msg">Subscription canceled by '.$deletedby.'</div>';
//                                            }
//        }
                            } else {
                                $available_quota += $orgdata["Subscription"]["pool_purchased"];
                                echo '<div class="msg">' . 'Subscription purchased through nDorce LLC. Please contact <a href="mailto:support@nDorse.net" _target="blank">nDorse support</a> if you want to update/cancel your subscription.' . '</div>';
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>
            <?php //if($orgdata["Subscription"]["organization_id"]==$orgdata['Organization']['id']){ ?>
            <?php // }      ?>
        </div>
        <div id="orgstatus_<?php echo $orgdata['Organization']['id']; ?>">
            <h3>Organization Status: <?php echo ($orgdata['Organization']['status'] == 1) ? "Active" : (($orgdata['Organization']['status'] == 2) ? "Deleted" : "Inactive"); ?></h3>
        </div>
        <table>
            <tr>
                <td>Available Limit:</td>
                <td><div id="available_quota_<?php echo $orgdata['Organization']['id']; ?>"> <?php echo $available_quota; ?></div></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; -Paid Users:</td>
                <td class="js_poolPurchased"><?php echo ($available_quota - 10); ?></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; -Free Users: </td>
                <td>10</td>
            </tr>
            <tr>
                <td>Total Users:</td>
                <td id="totalusers"><?php echo $totalusers; ?></td>
            </tr>
            <tr>
                <td colspan="2">Invitation Sent:-</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; -Via Mobile App:</td>
                <td><?php echo $inviationStats['total_invitations']["app"]; ?></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp; -Via Website: </td>
                <td><?php echo $inviationStats['total_invitations']["web"]; ?></td>
            </tr>
            <tr>
                <td>Invitation Accepted:</td>
                <td><?php echo $inviationStats['invitations_accepted']; ?></td>
            </tr>
            <?php
            $invitelink = ($inviationStats["pending_count"] > 0 && $orgdata['Organization']['status'] == 1) ? '<a href="#" data-toggle="modal" data-target="#myModal_invitations" class="pending-invites">Invitations waiting for Acceptance</a>' : 'Invitations waiting for Acceptance';
            ?>

            <tr>
                <td>
                    <?php
                    if ($loggedinUser['role'] == 6) {
                        echo "Invitations waiting for <br>Acceptance";
                    } else
                        echo $invitelink;
                    ?>
                    :</td>
                <td><?php echo $inviationStats["pending_count"]; ?></td>
            </tr>
            <?php $requestlink = ($pendingrequescounter > 0 && $orgdata['Organization']['status'] == 1) ? 'onclick="pendingbutton(' . $orgid . ')"' : ""; ?>
            <tr>
                <td id="innerhtmlpr">
                    <?php if ($requestlink != "") { ?>
                        <a href="javascript:void(0)" <?php echo $requestlink; ?> class="pending-invites">Pending Request:</a>
                    <?php } else { ?>
                        Pending Request:
                    <?php } ?>
                </td>
                <td id="counterpr"><?php echo $pendingrequescounter; ?></td>
            </tr>
            <?php if ($orgid == 148 || $orgid == 415 || $orgid == 425) { ?>
                <tr>
                    <td>
                        ADFS Users(Active/Uploaded):
                    </td>
                    <td id=""><?php echo $activeAdfsUsers; ?>/<?php echo $totalADFSUsers; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-md-3">
        <div class="nDorse-of-month">
            <h4>Total nDorsements</h4>
            <h3>
                <?php echo $totalendorsements; ?>
            </h3>
            <div class="sep"></div>
            <h4>Total Core Values</h4>
            <h3><?php echo array_sum($corevalueendorsedcounter); ?></h3>
        </div>
        <?php echo $this->Element("endorsementcounter"); ?> </div>
</div>
<?php /* ?>

  <section>
  <div class="row borBot" >
  <div class="col-lg-2 col-md-2 col-sm-12">
  <?php

  $org_image = $orgdata['Organization']['image'];
  if($org_image==""){
  echo $this->Html->image('img1.png',array('class'=>"img-responsive", 'width' => '175'));
  echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));

  }else{
  $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR  .$org_image;
  echo $this->Html->image($org_imagenew,array('width'=>'175','id'=>'org_image'));
  echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));
  }
  ?>
  </div>
  <div class="col-lg-9 col-md-9 col-sm-12">
  <div class="row">
  <div class="col-md-6 col-sm-12 our-lady" align="left">
  <?php
  $orgname = $orgdata['Organization']['name'];
  $orgid = $orgdata['Organization']['id'];
  echo $this->Html->link($orgname,array('controller'=>'users','action'=>'editorg',$orgid));
  echo $this->Html->Image("edit_icon.png", array("class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid)));
  ?>

  </div>
  </div>
  <div class="row content-color">
  <div class="col-md-3 col-sm-3">
  <h3><?php echo $orgdata['Organization']['short_name']; ?>
  <input type="hidden" id="orgid" value="<?php echo $orgdata['Organization']['id'];?>">

  </h3>
  <p><?php echo $orgdata['Organization']['about']; ?></p>

  </div>
  <div class="col-md-5">
  <h3>Organization Status:<?php echo ($orgdata['Organization']['status']) ? " Active": " Inactive";?></h3>
  <dl class="dl-horizontal" id="#freelance">
  <dt class="text-left">Total Users:</dt>
  <dd id="totalusers"><?php echo $totalusers;?></dd>
  <dt>Invitation Sent:-</dt>
  <dd></dd>
  <dt>Via Mobile App:</dt>
  <dd><?php echo $invitation_pending["app"];?></dd>
  <dt>Via website:</dt>
  <dd><?php echo $invitation_pending["web"];?></dd>
  <dt>Invitation Accepted:</dt>
  <dd><?php echo $invitation_accepted;?></dd>

  <?php
  //=======to check if link should be shown or not
  $invitelink = ($invitations_array["invitations_pending"]["web"] + $invitations_array["invitations_pending"]["app"] > 0 && $orgdata['Organization']['status'] == 1)? '<a href="#" data-toggle="modal" data-target="#myModal_invitations" class="pending-invites">': '<a href="javascript:void(0)">';
  //$pendinglink = $pendingrequests > 0? '<a href="#" data-toggle="modal" data-target="#myModal_pendingrequest" class="pending-invites">': '<a href="javascript:void(0)">';
  ?>
  <dt><?php echo $invitelink;?>Invitations waiting for acceptance:</a></dt>
  <dd><?php echo $invitation_pending["web"] + $invitation_pending["app"] - $invitation_accepted;?></dd>


  <?php $requestlink = ($pendingrequescounter > 0 && $orgdata['Organization']['status'] == 1) ?  'onclick="pendingbutton('.$orgid.')"' : "" ; ?>
  <dt id="innerhtmlpr"><a href="javascript:void(0)" <?php echo $requestlink; ?> class="pending-invites">Pending Request:</a></dt>
  <dd id="counterpr"><?php echo $pendingrequescounter;?></dd>
  </dl>
  </div>
  <div class="col-md-4">
  <h3>&nbsp;</h3>
  <div class="endrosd-month">
  <p>Endorsements for the month</p>
  <h3><?php echo $endorsementformonth;?></h3>
  </div>
  </div>
  </div>
  </div>
  </div>
  </section>
 * 


  <section>

  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  Total CoreValues <?php  echo array_sum($corevalueendorsedcounter);?>
  </button>
  <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="" aria-expanded="false" aria-controls="collapseExample">
  Total Endorsements <?php  echo $totalendorsements;?>
  </button>

  <div class="collapse" id="collapseExample">
  <?php if($coredata){?>
  <div class="row AddCoreValue">
  <div class="col-md-12">
  <div class="pull-left">
  <h4></h4>
  </div>
  </div>
  </div>
  <?php } ?>
  <div class="row OrgInfo fs18">
  <table class="table">
  <?php foreach($coredata as $key => $data) {?>
  <tr>
  <td><?php echo $data; ?></td>
  <td><?php echo $corevalueendorsedcounter[$key];?></td>
  </tr>
  <?php } ?>
  </table>

  </div>
  </div>
  </section>
 * <?php */ ?>
<div class="row bor-bot">
    <section >
        <div class="content-color">
            <h3 class="info">Subscription Info</h3>
            <?php if ($orgdata['Subscription']["organization_id"] == $orgdata['Organization']['id'] && $orgdata["Subscription"]['payment_method'] == "ndorse" && strtotime($orgdata["Subscription"]['end_date']) > time()) { ?>
                <?php echo $this->Element('subscriptiondetails', array("subscription" => $orgdata['Subscription'])); ?>
            <?php } elseif ($orgdata['Subscription']["organization_id"] == $orgdata['Organization']['id'] && $orgdata["Subscription"]['payment_method'] == "web" && in_array($orgdata["Subscription"]['is_deleted'], array(0, 1)) && strtotime($orgdata["Subscription"]['end_date']) > time()) { ?>
                <?php echo $this->Element('subscriptiondetails', array("subscription" => $orgdata['Subscription'])); ?>
            <?php } else { ?>
                <!--<h4 class="no-subcrip">No Subscription available.</h4>-->
                <h4 class="no-subcrip">For further subscriptions, please contact NDORSE, LLC via support@ndorse.net.</h4>
            <?php } ?>
        </div>
    </section>
</div>
<?php if (isset($orgSubcenterDetail) && count($orgSubcenterDetail) > 0) { ?>
    <div class="row bor-bot">
        <section >
            <div class="content-color"> 
                <h3 class="info" style="color: #f3711f;"><strong>Sub-Centers</strong>
                    <?php echo $this->Html->Image("edit_icon.png", array("class" => "editorgimage", "url" => array('controller' => 'users', 'action' => 'subcentersetting', $orgid))); ?>
                </h3>
                <div class="infoFlexBox">
                    <?php
                    foreach ($orgSubcenterDetail as $index => $orgSubCenter) {
                        $data = $orgSubCenter['OrgSubcenter'];
                        ?>
                        <div class="infoFlexBoxCol">
                            <h4 class="" style="color: #fff;margin: 0;"><strong><?php echo $data['short_name']; ?></strong></h4>
                            <h5 class="no-subcrip" style="color: #fff;"><strong>Total User: <?php echo $subcenterUserCountArray[$data['id']];?></strong></h5>
                            <h5 class="no-subcrip" style="color: #fff;"><strong>Current Month nDorsements: <?php echo $subcentersCurrentMonthEndorsements[$data['id']];?></strong></h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>
<?php } ?>
<?php if (!empty($coredata) && !empty($countermonthlyendorsements)) { ?>
    <div class="row bor-bot">
        <section >
            <div class="row AddCoreValue">
                <div class="col-md-12">
                    <div class="pull-left">
                        <h4>Core Values Fulfilled</h4>
                    </div>
                </div>
            </div>
            <div class="row OrgInfo fs18">
                <table class="table">
                    <?php
                    foreach ($coredata as $key => $data) {
                        if (!isset($countermonthlyendorsements[$key])) {
                            continue;
                        }
                        ?>
                        <tr>
                            <td><?php echo $data; ?></td>
                            <td><?php echo $countermonthlyendorsements[$key]; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="clearfix"></div>
        </section>
    </div>
<?php } ?>
<section>
    <div class="row AddCoreValue">
        <?php if (!empty($org_user_data)) { ?>
            <div class="col-md-6">
                <div class="pull-left user-list">
                    <h4 style="display:inline">User List</h4>
                    <span class="act-inact">Active & Eval Users: (<?php echo $activeusercount; ?>), 
                        Inactive Users: (<?php echo $inactiveusercount; ?>)</span> </div>
            </div>
            <div class="row col-md-6">
                <?php
                if ($orgdata['Organization']['status'] == 1) {
                    if ($authUser["role"] != 6) {
                        ?>
                        <div class="pull-right">
                            <button class="btn btn-xs btn-blue" onclick="bulkactive(<?php echo $orgdata['Organization']['id']; ?>);">Bulk Activate</button>
                            <button class="btn btn-xs btn-blue" onclick="bulkinactive(<?php echo $orgdata['Organization']['id']; ?>);" >Bulk Inactivate</button>
                            <?php if ($loggedinUser['role'] == 1) { ?>
                                <button class="btn btn-xs btn-blue" onclick="bulkReinvite(<?php echo $orgdata['Organization']['id']; ?>);" >Bulk Reinvite</button>
                            <?php } ?>
                            <button class="btn btn-xs btn-blue" onclick="dowloadUserList(<?php echo $orgdata['Organization']['id']; ?>);" >Download Users List</button>
                            <?php echo $this->Html->Image("addClient.png", array("url" => array("controller" => "users", "action" => "createendorser", $orgid), "height" => "25px", "width" => "25px")); ?> <span class="addClient" style="vertical-align:middle;">
                            <?php
                            echo $this->Html->link("Add New User", array("controller" => "users", "action" => "createendorser", $orgid));
                            ?>
                            </span><br />
                            <?php //echo $this->Html->Image("addClient.png", array("url" => array("controller" => "users", "action" => "createendorser", $orgid), "height" => "25px", "width" => "25px"));    ?> 
            <!--                <span class="addClient" style="vertical-align:middle;">
                                <a href="#" data-toggle="modal" data-target="#myModal_adusers" class="pending-invites">Add New AD User</a>
                            </span>-->
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <br>
            <br>
            <div class="clearfix"></div>

        <?php } else { ?>
            <div class="col-md-9">
                <div class="pull-left">
                    <h4>No User Yet</h4>
                </div>
            </div>
            <?php if ($orgdata['Organization']['status'] == 1) { ?>
                <div class="row col-md-3">
                    <div class="pull-right"> <?php echo $this->Html->Image("addClient.png", array("url" => array("controller" => "users", "action" => "createendorser", $orgid), "height" => "25px", "width" => "25px")); ?> <span class="addClient" style="vertical-align:middle;"> <?php echo $this->Html->link("Add New User", array("controller" => "users", "action" => "createendorser", $orgid)); ?></span> </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="row" style="color:#fff; margin-bottom: 15px;">
        <div class="col-md-3">
            <button type="button" class="btn btn-info btn-xs" data-toggle="collapse" id="filterby" data-target="#filter-nDorsements" title="Click to filter the data">Filter By</button>
            <button type="button" class="btn btn-info btn-xs resetUserlistFilters" data-toggle="collapse" title="Click to Reset Filter">Reset Filters</button>
        </div>
        <div class="clearfix"></div>
        <div id="filter-nDorsements" class="collapse">
            <div class="col-md-2" style="overflow:hidden">
                <h4>Title </h4>
                <?php
                if (empty($jobtitles)) {
                    echo $this->Form->input('Job Title', array('empty' => 'No Data Available', 'id' => 'jobtitlefilterempty', 'multiple' => 'multiple', 'label' => false, 'options' => $jobtitles, 'class' => 'form-control'));
                } else {
                    echo $this->Form->input('Job Title', array('id' => 'jobtitlefilter', 'multiple' => 'multiple', 'label' => false, 'options' => $jobtitles, 'class' => 'form-control'));
                }
                ?>
            </div>
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
                <h4>Status </h4>
                <?php
                $status = array('1' => 'Active', '0' => 'Inactive');

                if (empty($status)) {
                    echo $this->Form->input('status', array('empty' => 'No Data Available', 'id' => 'statusFilter', 'multiple' => 'multiple', 'label' => false, 'options' => $status, 'class' => 'form-control'));
                } else {
                    echo $this->Form->input('status', array('id' => 'statusFilter', 'multiple' => 'multiple', 'label' => false, 'options' => $status, 'class' => 'form-control'));
                }
                ?>
            </div>
            <div class="col-md-2" style="overflow:hidden">
                <h4>Type </h4>
                <?php
                $type = array('2' => 'Admin', '6' => 'nDorse Elite');

                if (empty($type)) {
                    echo $this->Form->input('usertype', array('empty' => 'No Data Available', 'id' => 'usertypeFilter', 'multiple' => 'multiple', 'label' => false, 'options' => $entities, 'class' => 'form-control'));
                } else {
                    echo $this->Form->input('usertype', array('id' => 'usertypeFilter', 'multiple' => 'multiple', 'label' => false, 'options' => $type, 'class' => 'form-control'));
                }
                ?>
            </div>
            <div class="col-md-2">
                <div style="vertical-align:bottom; display:table-cell; height:170px;">
                    <h4></h4>
                    <button type="submit" id="submitfilterusers" class="btn btn-info">Apply</button>
                </div>
            </div>
        </div>
    </div>
    <div class="search-icn row" style="margin-bottom:10px;">
        <input type="text" class="form-control" id="searchkeyword" onkeyup="searchusers(this.value)" placeholder="Filter Items...">
    </div>
    <div class="row UserList">
        <input type="hidden" name="totalrecords" id="totalrecords" value="<?php echo $totalrecords; ?>">
        <?php if ($org_user_data) { ?>
            <table id="mytable" class="table table-condensed table-hover tablesorter">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th id="status">Status <?php
                            echo $this->Html->image("down-arrow.png", array("class" => "statusdown"));
                            echo $this->Html->image("up-arrow.png", array("class" => "statusup", "style" => "display:none"));
                            ?> </th>
                        <th id="role">Role <?php
                            echo $this->Html->image("down-arrow.png", array("class" => "statusdown"));
                            echo $this->Html->image("up-arrow.png", array("class" => "statusup", "style" => "display:none"));
                            ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="userslisting">
                    <?php echo $this->Element('userslisting', array("orgstatus" => $orgdata['Organization']['status'], "admin_id" => $orgdata['Organization']['admin_id'], "orguser_id" => $authUser["id"])); ?>
                </tbody>
            </table>
            <div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
        <?php } ?>
    </div>
</section>

<!------- --> 
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="text-align: center">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">File Import Status</h4>
            </div>
            <div class="modal-body" style="text-align: center">
                <p>
                    <?php
                    if (!empty($uploadedemssage)) {
                        echo $uploadedemssage;
                    }
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <button onclick="location.href = '<?php echo $this->Html->url(array("controller" => "organizations", "action" => "info", $orgdata["Organization"]["id"])) ?>'" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($uploadedemssage)) { ?>
    <script>$('#myModal').modal('show');</script>
<?php } ?>
<div aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModa2" class="modal fade in hidden" style="display: block;">
    <div role="document" class="modal-dialog">
        <div align="center" class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="btn btn-default pull-right close" type="button"><span aria-hidden="true">X</span></button>
                <!-- <h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>--> 
            </div>
        </div>
    </div>
</div>
<?php
//=======as this access is only for admin
if ($authUser["role"] == 1 && $orgdata['Organization']['status'] != 2) {
    ?>
    <section class="row footer-bg bulk-users">
        <div class="col-md-6 col-lg-6">
            <div class="BulkImport" >
                <h4>Bulk Import Of Users</h4>
                <div>
                    <div  class="pull-left DownloadLink"> <?php echo $this->Html->image('DownloadTemplate.png', array('align' => "left")); ?>
                        <h5><?php echo $this->Html->link('Download Template', array('controller' => 'organizations', 'action' => 'bulkusertemplate')); ?></h5>
                    </div>
                    <input type="file" id="bulkuserbutton" class="hidefileupload hidden">
                    <div class="pull-right BrowseButton">
                        <div class="choosefile">
                            <h6 id="choosefilebulkusers">Choose File </h6>
                            <span>
                                <input value="Upload" onclick="uploadcsvbulkuser(<?php echo $orgdata['Organization']['id']; ?>, '<?php echo $orgdata['Organization']['name']; ?>', '<?php echo $orgdata['Organization']['secret_code']; ?>')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button" >
                            </span> <a href="javascript:void(0)" data-toggle="collapse" data-target="#ToolTip02" aria-expanded="false" aria-controls="ToolTip02"><?php echo $this->Html->image('helpIcon.png'); ?></a> </div>
                        <div class="collapse Popover" id="ToolTip02" >
                            <div class="well">
                                <ul class="list-inline">
                                    <li>Step1: Download the empty template (CSV file) from website by clicking on Download template button.</li>
                                    <li></li>
                                    <li>Step2: Fill in the details in that CSV file. Save it in your computer’s hard drive.</li>
                                    <li></li>
                                    <li>Step3: Upload that CSV file to server by clicking on “Upload” button.</li>
                                </ul>
                                <span class="pull-right popOverArrow"><?php echo $this->Html->image('popOverArrow.png'); ?></span> </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="BulkImport" >
                <h4>Bulk Import Of Photo Of Existing Users</h4>
                <div class="">
                    <div  class="pull-left DownloadLink"> <?php echo $this->Html->image('DownloadTemplate.png', array('align' => "left")); ?>
                        <h5><?php echo $this->Html->link("Download Template", array("controller" => "organizations", "action" => "bulklinkimportstemp", $orgdata['Organization']['id'])); ?></h5>
                    </div>
                    <input type="file" id="bulkimagesbutton" class="hidefileuploadimages hidden">
                    <div class="pull-right BrowseButton">
                        <div class="choosefile">
                            <h6 id="choosefileexistingusers">Choose File </h6>
                            <span>
                                <input value="Upload" onclick="uploadbulkimages(<?php echo $orgdata['Organization']['id']; ?>)" class="btn btn-default pull-right" id="" type="button" >
                            </span> <a href="javascript:void(0)" data-toggle="collapse" data-target="#ToolTip01" aria-expanded="false" aria-controls="ToolTip01"><?php echo $this->Html->image('helpIcon.png'); ?></a> </div>
                        <div class="collapse Popover" id="ToolTip01" >
                            <div class="well">
                                <ul class="list-inline">
                                    <li>Step1: Download the CSV file of existing users from website by clicking on Download template button.</li>
                                    <li></li>
                                    <li>Step2: Fill in the links for each email id in that CSV file. Save it in your computer’s hard drive.</li>
                                    <li></li>
                                    <li>Step3: Upload that CSV file to server by clicking on “Upload” button.</li>
                                </ul>
                                <span class="pull-right popOverArrow"><?php echo $this->Html->image('popOverArrow.png'); ?></span> </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
<?php } ?>
<div class="modal fade" id="myModalbulkusersimports" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" >
                <button type="button" class="btn btn-default pull-right close closebulkimport" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Users Import Status</h4>
            </div>
            <div class="modal-body" >
                <div id="bulkuserstable"> </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="myModal_invitations" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body info-scroll" style="margin-left:30px;">
                <div class="checkbox">
                    <input id="check" type="checkbox" name="check" value="" class="css-checkbox">
                    <label class="css-label" for="check">Select All</label>
                </div>
                <?php
                if (!empty($inviationStats["pending_list"])) {
                    if (!empty($inviationStats["pending_list"]['active'])) {
                        echo "<strong>Active</strong><br>";
                        foreach ($inviationStats["pending_list"]['active'] as $key => $value) {
                            $key_value[$key] = $value["inviteflow"];
                            echo '<div class="checkbox">';
                            echo '<input type="checkbox" name="email_pendings[]" id="check' . $key . '" class="css-checkbox" data-value="' . $value["inviteflow"] . '" value="' . $value["email"] . '" rel="' . $key . '"/>';
                            //echo '<input id="check'.$i.'" type="checkbox" name="pendingemails[]" value="check1">';
                            echo '<label class="css-label" for="check' . $key . '">' . $value["email"] . '</label>';
                            if ($value["inviteflow"] == "web_invite") {
                                echo $this->Html->Image("webflow.png");
                            } else {
                                echo $this->Html->Image("mobileflow.png");
                            }
                            echo '</div>';
                        }
                    }

                    if (!empty($inviationStats["pending_list"]['inactive'])) {
                        echo "<strong>Inactive</strong><br>";
                        foreach ($inviationStats["pending_list"]['inactive'] as $key => $value) {
                            $key_value[$key] = $value["inviteflow"];
                            echo '<div class="checkbox">';
//                            echo '<input type="checkbox" name="email_pendings[]" id="check' . $key . '" class="css-checkbox" data-value="' . $value["inviteflow"] . '" value="' . $value["email"] . '" rel="' . $key . '"/>';
                            //echo '<input id="check'.$i.'" type="checkbox" name="pendingemails[]" value="check1">';
                            echo '<label class="css-label inactive-label" for="check' . $key . '">' . $value["email"] . '</label>';
                            if ($value["inviteflow"] == "web_invite") {
                                echo $this->Html->Image("webflow.png");
                            } else {
                                echo $this->Html->Image("mobileflow.png");
                            }
                            echo '</div>';
                        }
                    }


                    $arrayorgdetail = array("id" => $orgdata['Organization']['id'], "name" => $orgdata['Organization']['name'], "code" => $orgdata['Organization']['secret_code']);
                    echo "<input type = 'hidden' id = 'orgdetails' value = '" . json_encode($arrayorgdetail) . "'/>";
                }
                ?>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="reinviteemails" class="btn btn-primary btn-blue pull-left" style="margin-left:30px;">Re-Invite</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal_pendingrequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <input type="hidden" id="orgid" value="<?php echo $orgdata['Organization']['id']; ?>">
    <div class="modal-dialog" role="document" style="width: 1000px">
        <div class="modal-content" align="left">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body" style="margin-left:30px;">
                <div class="checkbox">
                    <input id="checkpr" type="checkbox" name="check" value="" class="css-checkbox">
                    <label class="css-label" for="checkpr">Select All</label>
                </div>
                <div class="pending-req">
                    <table class="table table-bordered table-striped table-hover" >
                        <thead>
                            <tr>
                                <td>&nbsp;</td>
                                <td>Name</td>
                                <td>Email</td>
                                <td>Mobile</td>
                                <td>Why want to join organization</td>
                                <td>Relation to organization</td>
                                <td>Other Reason</td>
                            </tr>
                        </thead>
                        <tbody  id="pendingdata">

                        </tbody>
                    </table>
<!--                    <table id = "pendingdata" border="1" style="width: 100%;">
                        <thead>
                        </thead>
                        <tbody >
                            
                        </tbody>
                    </table>-->
                </div>
                <?php /* if(!empty($pendingrequests)){
                  foreach($pendingrequests as $key => $value){
                  echo '<div class="checkbox">';
                  echo '<input type="checkbox" name="pendingrequests[]" id="checkpr'.$key.'" class="css-checkbox" value="'.$value["email"].'" rel="'.$key.'"/>';
                  echo '<label class="css-label" for="checkpr'.$key.'">'.$value["firstname"].'</label>';
                  echo '<label class="css-label" for="checkpr'.$key.'">'.$value["lastname"].'</label>';
                  echo '<label class="css-label" for="checkpr'.$key.'">'.$value["email"].'</label>';
                  echo '</div>';
                  }
                  } */ ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="pendingrequest" onclick="pendingrequest('accept', <?php echo $orgdata['Organization']['id']; ?>, '<?php echo $orgdata['Organization']['name']; ?>')" class="btn btn-primary btn-blue pull-left" style="margin-left:30px;">Accept</button>
                <button type="button" id="pendingrequest" onclick="pendingrequest('deny', <?php echo $orgdata['Organization']['id']; ?>, '<?php echo $orgdata['Organization']['name']; ?>')" class="btn btn-primary btn-blue pull-left" style="margin-left:30px;">Deny</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal3_activestatus" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center">
                <h4 class="modal-title">ARE YOU SURE YOU WANT TO INACTIVE?</h4>
                <div class="col-md-12 p30"> <span class="radio greybg">
                        <div class="input radio">
                            <div class="col-md-6">
                                <input type="radio" value="0" id="status_users" name="status_usersrt">
                                <label for="status_users">Complete Inactivate</label>
                            </div>
                            <div>
                                <input type="radio" checked="checked" value="3" id="status_userspi" name="status_usersrt">
                                <label for="status_userspi">Partially Inactivate</label>
                            </div>
                        </div>
                    </span> </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button onclick="" id="confirmbuttoninactiveusers" class="btn btn-primary btn-blue" type="button">Confirm</button>
                    <button class="btn btn-primary btn-blue canceldelete" type="button">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2_delete" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center">
                <h4 class="modal-title">ARE YOU SURE YOU WANT TO DELETE?</h4>
                <p>This will delete User</p>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button id="confirmbuttondelete" onclick="" class="btn btn-primary btn-blue" type="button">Yes</button>
                    <button class="btn btn-primary btn-blue canceldelete" type="button">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2_deletepopup" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center">
                <h4 class="modal-title"></h4>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="resetpassworduser" role="dialog">
    <div class="modal-dialog"> 
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
                <h3>Reset Password for <span id="username"></span></h3>
                <span style="display: none"> <span id="uid"></span> <span id="uemail"></span> <span id="orgname"><?php echo $orgdata['Organization']['name']; ?></span> </span> </div>
            <div class="modal-body" style=""> <?php echo $this->Form->create("userreset", array('onSubmit' => 'return false;')); ?> <?php echo $this->Form->Input("New Password", array("type" => "password", 'class' => 'form-control', "id" => "Usernewpassword", "placeholder" => "New Password")); ?> <?php echo $this->Form->Input("Confirm Password", array("type" => "password", 'class' => 'form-control', "id" => "Userconfirmpassword", "placeholder" => "Confirm Password")); ?> <br>
                <div class="checkbox"> <?php echo $this->Form->checkbox('show_password', array('class' => 'css-checkbox', "hiddenField" => FALSE, 'label' => false)); ?>
                    <label class="css-label show-pw" for="userresetShowPassword">Show Password</label>
                </div>
                <br/>
                <button id="passwordresetsubmit" class="btn btn-default" type="button">Reset</button>
                <?php echo $this->Form->end(); ?> </div>
        </div>
    </div>
</div>
<input type="hidden" id="pagename" value="userslisting">
<?php echo $this->Element("commonpopupmessage"); ?>
<?php
$flag_user = "";
if (isset($_SESSION["useralert"]) && $_SESSION["useralert"] != "") {
//    $flag_user = $_SESSION["useralert"] + 1;
//$this->Session->write('Auth.User.inactiveuser', "");
//$this->Session->delete('Auth.User.inactiveuser');
    $userAlert = $_SESSION["useralert"];
    unset($_SESSION["useralert"]);
}
?>

<!-- Modal -->
<div class="modal fade" id="myModalViewProfile" tabindex="-1" role="dialog" 
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <!-- Modal Header -->

            <div class="modal-header">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <h3>User Detail</h3>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="formusers" role="form">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fname">First Name : </label>
                            <input readonly="readonly" type="text" class="form-control"id="fname"/>
                        </div>

                        <div class="form-group">
                            <label for="email">Email : </label>
                            <input readonly="readonly" type="text" class="form-control" id="email"/>
                        </div>
                        <div class="form-group">
                            <label for="department">Department : </label>
                            <input readonly="readonly" type="text" class="form-control"id="department"/>
                        </div>
                        <div class="form-group">
                            <label for="subcenter">Sub center : </label>
                            <input readonly="readonly" type="text" class="form-control" id="subcenter"/>
                        </div>
                        <div class="form-group">
                            <label for="lname">Mobile : </label>
                            <input readonly="readonly" type="text" class="form-control" id="mobile"/>
                        </div>

                        <div class="form-group">
                            <div class="user-detail-label">Skills : </div>
                            <textarea readonly="readonly" class="form-control" id="skills"></textarea>
              <!--              <input readonly="readonly" type="text" class="form-control" id="skills"/>-->
                        </div>

                        <div class="form-group">
                            <label for="country">Country : </label>
                            <input readonly="readonly" type="text" class="form-control" id="country"/>
                        </div>
                        <div class="form-group">
                            <label for="lname">City : </label>
                            <input readonly="readonly" type="text" class="form-control"id="city"/>
                        </div>
                        <div class="form-group">
                            <label for="lname">Zip code : </label>
                            <input readonly="readonly" type="text" class="form-control"id="zip"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lname">Last Name : </label>
                            <input readonly="readonly" type="text" class="form-control" id="lname"/>
                        </div>
                        <div class="form-group">
                            <label for="lname">DOB : </label>
                            <input readonly="readonly" type="text" class="form-control" id="dob"/>
                        </div>
                        <div class="form-group">
                            <label for="jobtitle">Job Title : </label>
                            <input readonly="readonly" type="text" class="form-control"id="jobtitle"/>
                        </div>
                        <div class="form-group">
                            <label for="daisy_enabled">DAISY User : </label>
                            <input readonly="readonly" type="text" class="form-control" id="daisy_enabled"/>
                        </div>
                        <div class="form-group">
                            <div class="user-detail-label">Hobbies : </div>
                            <textarea readonly="readonly" class="form-control" id="hobbies"></textarea>
              <!--              <input readonly="readonly" type="text" class="form-control" id="hobbies"/>-->
                        </div>
                        <div class="form-group">
                            <label for="lname">State : </label>
                            <input readonly="readonly" type="text" class="form-control"id="state"/>
                        </div>

                        <div class="form-group">
                            <label for="lname">Street : </label>
                            <input readonly="readonly" type="text" class="form-control"id="street"/>
                        </div>

                        <div class="form-group">
                            <div class="user-detail-label">About : </div>
                            <textarea readonly="readonly" class="form-control" id="about"></textarea>
              <!--              <input readonly="readonly" type="text" class="form-control" id="about"/>-->
                        </div>



                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-blue pull-left" data-dismiss="modal"> Close </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {



<?php if (isset($userAlert)) { ?>
            alertbootbox("<?php echo $userAlert; ?>");
<?php } ?>
        var pendindnDorsementCount = '<?php echo $pendindnDorsementCount; ?>';
        if (pendindnDorsementCount > 0) {
            var MSG = "You have " + pendindnDorsementCount + " new pending guest nDorsements.";
            alertbootbox(MSG);
        }
    });


</script>
