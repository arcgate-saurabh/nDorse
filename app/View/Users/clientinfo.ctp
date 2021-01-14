<?php ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid header-bg">
    <div class="row headerinfo">
      <div class="title-org">
        <p>Organization Owner Info</p>
      </div>
      <div id="refresh"><a href="#" class="pull-right"> <?php echo $this->Html->image('refresh.png',array('class'=>'img-responsive', 'width' => '36')); ?></a> </div>
    </div>
  </div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-md-2 sidebar">
  <div id="wrapper">
    <ul class="sidebar-nav">
      <li class="sidebar-brand"> <?php echo $this->Html->link('Organizations',array('controller'=>'organizations','action'=>'index')); ?> </li>
      <li class="sidebar-brand"> <?php echo $this->Html->link('Set Up New Organization',array('controller'=>'users','action'=>'createorg', 'client_id' => $userdata['User']['id'])); ?> </li>
      <?php if($authUser["role"] == "1"){
                          echo '<li class="sidebar-brand">'.$this->Html->link("Global Settings", array("controller" => "users",  "action" => "setting")).'</li>';
                    }
                    if($authUser["role"] == "2"){?>
      <li class="sidebar-brand"><?php echo $this->Html->Link("FAQS", array('controller' => 'users', 'action' => 'usersfaq'));?></li>
      <?php }?>
    </ul>
    <div class="logout" style=""> <span class="pull-left userName"> <?php echo $authUser['fname'].' '.$authUser['lname']; ?>
      <div class="clearfix"></div>
      <?php echo $this->Html->link('LOGOUT',array('controller'=>'users','action'=>'logout'),array('class'=>'logoutText')); ?> </span> <span class="pull-right"> <?php echo $this->Html->image("user.png", array('class' => 'img-circle', 'widht' => '61', 'height' => '61'));?> </span> </div>
  </div>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <div align="left" id="page-content-wrapper2">
    <div class="lady-lake">
      <div class="row bor-bot" >
        <div class="col-md-5">
          <div class="media">
            <div class="media-left col-md-3">
              <?php $user_image = $userdata['User']['image'];
                    if($user_image==""){
                        echo $this->Html->image('user.png',array('class'=>"img-circle", "width" => "61", "height" => "61")); 
                    }else{
                        $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR  .$user_image;
                        if(file_exists(WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image)){
                            echo $this->Html->image($user_imagenew,array('width'=>'61','height'=>'61','id'=>'org_image','class'=>"img-circle"));
                        }else {
                            echo $this->Html->image('user.png',array('class'=>"img-circle", "width" => 61, "height" => 61)); 
                        }
                    }
                ?>
            </div>
            <div class="media-body">
              <?php
                    $username = ucfirst($userdata['User']['fname']).' '.ucfirst($userdata['User']['lname']);
                    $links = array("controller" => "users", "action" => "editclient", $userdata['User']['id']);
                ?>
                <h4 class="media-heading"><?php echo $this->Html->link($username, $links)." ".$this->Html->Image("edit_icon.png", array("url" => $links, "data-toggle" => "tooltip", "title" => "Edit Client")); ?></h4>
                <div><?php echo $userdata['User']['email']; ?><br />
                  Last updated on: <?php echo $this->Time->Format($userdata['User']['updated'], DATEFORMAT); ?><br />
                  Created on: <?php echo $this->Time->Format($userdata['User']['created'], DATEFORMAT);?></div>
            </div>
          </div>
        </div>
        <div class="col-md-4 user-subcribed org-status">
          <table class="">
            <!--<tr>
              <td>Type</td>
              <td>Free</td>
            </tr>
            <tr>
              <td>Payment Method</td>
              <td>Both</td>
            </tr>
            <tr>
              <td>Status</td>
              <td><?php //echo ($userdata['User']['status']) ? " Active": " Inactive";?></td>
            </tr>
        -->
            <tr>
              <td>Number Of Orgs</td>
              <td id="u_totalorgs"><?php echo count($user_org_data); ?></td>
            </tr>
            <tr>
              <td> Total Users </td>
              <td id="u_totalusers"><?php echo $noofclientusers;?></td>
            </tr>
          </table>
        </div>
      </div>
      <!-- /row -->
      
      <?php if($user_org_data){?>
      <h2 class="org">Organizations</h2>
      <?php } 
                    $ajaxurl = Router::url(array('controller' => 'ajax','action' => 'changeorgstatus'));
                    foreach($user_org_data as $orgdata) { ?>
      <div id="row_<?php echo $orgdata["Organization"]["id"]; ?>" class="bor-bot <?php echo ($orgdata['Organization']['status']) ? "" : "inactive"?>">
        <?php 
            $orgdetails = array(
               "id"        => $orgdata['Organization']['id'],
               "image"     => $orgdata['Organization']['image'],
               "name"      => $orgdata['Organization']['name'],
               "sname"     => $orgdata['Organization']['short_name'],
               "street"    => $orgdata['Organization']['street'],
               "city"      => $orgdata['Organization']['city'],
               "state"     => $orgdata['Organization']['state'],
               "zip"       => $orgdata['Organization']['zip'],
               "country"       => $orgdata['Organization']['country'],
           );
        echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'other')); ?>
        <?php /*
                        <div class="col-lg-2 col-md-2 col-sm-12">
                        <?php
                            $org_image = $orgdata['Organization']['image'];
                            if($org_image==""){
                                  echo $this->Html->image('img1.png',array('class'=>'img-responsive' , 'width' => '175'));
                                  //echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "50px", "height" => "50px"));
                            }else{
                             $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR  .$org_image;
                             if(file_exists(WWW_ROOT. ORG_IMAGE_DIR  .$org_image)){
                                    echo $this->Html->image($org_imagenew,array('id'=>'org_image','class'=>"img-responsive"));
                             }else {
                                    //echo $this->Html->image($orgdata['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "50px", "height" => "50px"));
                             }
                        }?>
                        </div>
                        <div class="col-md-3 comp-name">
                        <?php
                            $orgname = $orgdata['Organization']['name']; 
                            $orgid =  $orgdata['Organization']['id'];
                            echo '<h2>'.$this->Html->link($orgname,array('controller'=>'organizations','action'=>'info',$orgid)). '</h2>'; 
							
							 echo '<h3>'.$orgdata['Organization']['short_name'].'</h3>';
							 $address1 = array("street" => $orgdata['Organization']['street'], "city" => $orgdata['Organization']['city']);
                                if(!empty($address1["street"])){
                                    echo '<p>'.implode(", ", $address1).'</p>';
                                }
                                $address2 = array("state" => $orgdata['Organization']['state'], "country" => $orgdata['Organization']['country'], "zip" => $orgdata['Organization']['zip']);
                                if(!empty($address2["state"])){
                                    echo '<p>'.implode(", ", $address2).'</p>';
                                }
                        ?>
                        </div>
                        <?php */ ?>
        <div class="col-md-4 org-status">
          <div id="orgstatus_<?php echo $orgdata['Organization']['id'];?>">
            <?php $target_id = $orgdata['Organization']['id'];?>
            <h3>Organization Status:<?php echo ($orgdata['Organization']['status']) ? " Active" : " Inactive"; ?></h3>
          </div>
          <table>
            <tr>
              <td>Total Users:</td>
              <td id="totalusers"><?php echo $nooforgusers[$target_id];?></td>
            </tr>
            <tr>
              <td>Invitation Sent:-</td>
              <td></td>
            </tr>
            <tr>
              <td>Via Mobile App:</td>
              <td><?php echo $invitation_pending[$target_id]["app"];?></td>
            </tr>
            <tr>
              <td>Via website:</td>
              <td><?php echo $invitation_pending[$target_id]["web"];?></td>
            </tr>
            <tr>
              <td>Invitation Accepted: </td>
              <td><?php echo $invitation_accepted[$target_id];?></td>
            </tr>
          </table>
        </div>
        <div class="col-md-3">
          <div class="pull-right"> <a href="javascript:void(0);" rel="<?php echo $orgdata['Organization']['id']; ?>_one" class="dots"> <?php echo $this->Html->image('3dots.png',array('class'=>'img-responsive')); ?> </a>
            <div class="arrow_box <?php echo $orgdata['Organization']['id']; ?>_one" style="position:absolute; right:10px;z-index:2;">
              <div style="border:0px solid #f00; margin-top:-35px; margin-right:5px;" class="pull-right"><?php echo $this->Html->Image("popupArrow.png");?></div>
              <ul>
                <li id="statuschanges_<?php echo $orgdata['Organization']['id'];?>"><a href="#" data-toggle="modal" data-target="#myModal" onclick="changestatus(<?php echo $orgdata['Organization']['id']; ?>, '<?php echo $ajaxurl; ?>', '<?php echo $orgdata['Organization']['status']; ?>,', 'Organization')"><?php echo ($orgdata['Organization']['status']) ? "Inactive ": "Activate "?></a></li>
                <li><a href="#"  data-toggle="modal" data-target="#myModa2_<?php echo $orgdata['Organization']['id'];?>">Delete</a></li>
                <li><?php echo $this->Html->link("Reports and Charts", array("controller" => "organizations", "action" => "reportsandcharts", $orgdata['Organization']['id'])); ?></li>
                <li><?php echo $this->Html->link("Live Endorsements", array("controller" => "organizations", "action" => "liveendorsement", $orgdata['Organization']['id'])); ?></li>
              </ul>
            </div>
          </div>
          <div class="clearfix"></div>
          <?php echo $this->Element("endorsementcounter", array("endorsementformonth" => $endorsementformonth[$target_id]));?> </div>
        
        <!-- /row -->
        
        <div class="col-md-3 MT30"> 
          <!--<button type="submit" class="btn btn-lg btn-primary btn-block2">Renew Subscription</button>
                            <button type="submit" class="btn btn-lg btn-primary btn-block2">Terminate Subscription</button> --> 
        </div>
        <div class="col-md-12">
          <div class="red-line"></div>
          <div class="content-color">
            <h3 class="info">Subscription Info</h3>
            <?php if($orgdata['Subscription']["organization_id"] == $orgdata['Organization']['id'] && $orgdata["Subscription"]['payment_method'] == "ndorse"&& strtotime($orgdata["Subscription"]['end_date']) > time()){ ?>
            <?php echo $this->Element('subscriptiondetails', array("subscription" => $orgdata['Subscription'])); ?>
            <?php }elseif($orgdata['Subscription']["organization_id"] == $orgdata['Organization']['id'] && $orgdata["Subscription"]['payment_method'] == "web"  && strtotime($orgdata["Subscription"]['end_date']) > time() ){ ?>
            <?php echo $this->Element('subscriptiondetails', array("subscription" => $orgdata['Subscription'])); ?>
            <?php }elseif($orgdata['Subscription']["organization_id"] == $orgdata['Organization']['id'] && $orgdata["Subscription"]['payment_method'] == "web" && strtotime($orgdata["Subscription"]['end_date']) > time() ){ ?>
            <div class="col-md-5 user-subcribed"> Subscription is under process. </div>
            <?php }else{?>
            <h4 class="no-subcrip">No Subscription available.</h4>
            <?php } ?>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <!--<div class="row-end">&nbsp;</div> --> 
      
      <!-- /row --> 
      
      <!-- <div class="clearfix"></div> --> 
      <?php echo $this->Element('deleteitem', array('data' => $orgdata));?>
      <?php } ?>
    </div>
  </div>
  <!-- /container fluid --> 
</div>
<div class="modal fade" id="myModal2_deletepopup" role="dialog">
  <div class="modal-dialog"> 
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4 class="modal-title">Test Code</h4>
      </div>
    </div>
  </div>
</div>
