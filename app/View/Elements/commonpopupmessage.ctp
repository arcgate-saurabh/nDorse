<div class="modal fade" id="myModal2_commonpopupmessage" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close"" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4 class="modal-title"></h4>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal2_purchasesubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("adminsubscription", array('onSubmit'=>'return false;',"id"=>"adminsubscriptionIndexForm"));?>
        <input type="hidden" name="sale_org_id" id="sale_org_id" value="" />
        <div > 
          <!--<div>Type</div> -->
          
          <?php
                                    $subscriptiontype = array("trial" => "Trial", "paid" => "Paid (Annual subscription)");
                                    echo $this->Form->input('mode', array('empty' => 'Select Mode', 'options' => $subscriptiontype, 'class' => 'form-control')); ?>
          
          <!--<div>Users</div> --> 
          <?php echo $this->Form->Input("users", array("type" => "text", 'label'=>'Number of users','class' => 'form-control', "id" => "users"));?>
          <div id="suadmin_subscription_trial" style="display:none;">
            <?php
                                    $duration = array(1 => "1 Month", 2 => "2 Month",3=>"3 Month",4=>"4 month",5=>"5 month",6=>"6 Month");
                                    echo $this->Form->input('trial_duration', array('empty' => 'Select Type', 'options' => $duration, 'class' => 'form-control', "selected" => "paid")); ?>
          </div>
         <!-- <div id="suadmin_subscription_paid" style="display:none;">
            
          </div>-->
          <div id="suadmin_subscription_amt" style="display:none;" >
            <?php
                                     echo $this->Form->Input("amount", array("type" => "text", 'label'=>'Amount to pay ($)','class' => 'form-control', "id" => "amt"));?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="adminsubscriptionsubmit" class="btn btn-blue pull-left" type="button">Done</button>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>
<div class="modal fade" id="myModal2_upgradesubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("upgradeadminsubscription", array('onSubmit'=>'return false;',"id"=>"upgradeadminsubscriptionIndexForm"));?>
        <input type="hidden" name="up_org_id" id="up_org_id" value="" />
        
        <!--<div>Users</div> --> 
        <?php echo $this->Form->Input("users", array("type" => "text","label"=>"Number of users to upgrade", 'class' => 'form-control', "id" => "upgrade_users"));?>
        <div id="suadmin_subscription_amt" > 
          
          
          <?php
                                     echo $this->Form->Input("amt", array("type" => "text", 'class' => 'form-control',"label"=>"Amount To Pay ($)", "id" => "upgrade_amt"));?>
        </div>
      </div>
      <div class="modal-footer">
        <button id="updateadminsubscriptionsubmit" class="btn btn-blue pull-left" type="button">Done</button>
        <?php echo $this->Form->end();?> </div>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="myModal2_downgradesubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("downgradeadminsubscription", array('onSubmit'=>'return false;',"id"=>"downgradeadminsubscriptionIndexForm"));?>
        <input type="hidden" name="down_org_id" id="down_org_id" value="" />
        <input type="hidden" name="pool_qty" id="pool_qty" value="" readonly="readonly" />
        <input type="hidden" name="select_user" id="select_user" value="" readonly="readonly" />
        <input type="hidden" name="user_quota" id="user_quota" value="" readonly="readonly" />
        <?php echo $this->Form->Input("users", array("type" => "text", "label"=>"Number of users to downgrade",'class' => 'form-control', "id" => "downgrade_users"));?>
        <div id="suadmin_downgrade_amt" >
          <?php
                                     echo $this->Form->Input("amt", array("type" => "text","label"=>"Amount ($)", 'class' => 'form-control', "id" => "downgrade_amt"));?>
        </div>
        <div id="downgrade_active_users1" style="display:none;">
          <input type="text" id="searchuser" name="searchuser" value="" />
          <div id="downgrade_active_users" ></div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="downgradeadminsubscriptionsubmit" class="btn btn-blue pull-left" type="button">Done</button>
        <?php echo $this->Form->end();?></div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal2_terminatesubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Terminate Subscription</h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("adminterminatesubscription", array('onSubmit'=>'return false;',"id"=>"adminterminatesubscriptionIndexForm"));?>
        <input type="hidden" name="terminate_org_id" id="terminate_org_id" value="" />
        <!--<span class="radio">
        <div class="input radio">

            <input type="radio" checked="checked" value="1" id="OrgAllowAttachment1" name="data[Org][allow_attachment]">
            <label for="OrgAllowAttachment1">Yes</label>

            <input type="radio" value="0" id="OrgAllowAttachment0" name="data[Org][allow_attachment]">
            <label for="OrgAllowAttachment0">No</label>

        </div>
        </span> -->
        
        <span class="radio">
        <?php
                                    $options = array(
                                            '1' => 'Right Away',
                                            '2' => 'Next Billing'
                                    );
                                    echo $this->Form->input('option', array('type' => 'radio', 
                                        
                                        'options' =>  $options,
                                        'label' => array("style" => "background-color:transperent"),
                                        'legend' => false,
                                        'value' => 1
                                        )
                                    );
                                ?>
        </span> </div>
      <div class="modal-footer">
        <button id="adminterminatesubscriptionsubmit" type="mannual" class="btn btn-blue pull-left" type="button">
        Done
        </button>
        <?php echo $this->Form->end();?></div>
    </div>
  </div>
</div>

<!-- bulk active user active -->
<div class="modal fade" id="myModal2_bulkactiveuser" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("bulkactiveuser", array('onSubmit'=>'return false;',"id"=>"bulkactiveuser"));?>
        <input type="hidden" name="bulk_org_id" id="bulk_org_id" value="" />
        <div class="">
          <h6>Number of users to activate</h6>
          <?php echo $this->Form->Input("active_users_no", array("type" => "text", 'class' => 'form-control', "id" => "active_users_no", "div" => false, "label" => false));?>
          <div style="margin: 5px 0;">
            <h6 style="margin:10px 0;" class="pull-right">Note: Oldest inactive users accounts will be activated</h6>
            <button  class="activeuserbulksubmit btn btn-blue pull-left" recentuser="1"  type="button">Activate</button>
          </div>
        </div>
        <div> <span> <?php echo $this->Html->image('or.png',array( 'width' => '100%'));  ?></span>
          <div class="clearfix"></div>
          <div >
            <h5>Select Inactive Users</h5>
          </div>
          <div class="search-icn"><input type="text" id="searchactiveuser" name="searchactiveuser" value="" class="form-control" placeholder="Search User..." /></div>
        </div>
        <div id="bulk_active_users1" style="height:350px; overflow-x:hidden; overflow:auto; margin-top:10px;" >
          <div id="bulk_active_users" ></div>
        </div>
      </div>
      <div class="modal-footer">
        <button  class="activeuserbulksubmit btn btn btn-blue pull-left" recentuser="0" type="button">Activate</button>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>

<!-- bulk inactive user active -->
<div class="modal fade" id="myModal2_bulkinactiveuser" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("bulkinactiveuser", array('onSubmit'=>'return false;',"id"=>"bulkinactiveuser"));?>
        <input type="hidden" name="inbulk_org_id" id="inbulk_org_id" value="" />
        <div> Number of users to inactivate<?php echo $this->Form->Input("inactive_users_no", array("type" => "text", 'class' => 'form-control', "id" => "inactive_users_no", "div" => false, "label" => false));?> </div>
        <div style="margin: 5px 0;">
          <h6 style="margin:10px 0;" class="pull-right">Note: Recent active users accounts will be deactivated</h6>
          <button class="inactiveuserbulksubmit btn btn-blue" recentuser="1"  type="button">Inactivate</button>
        </div>
        <div>
          <div><span> <?php echo $this->Html->image('or.png',array( 'width' => '100%'));  ?></span></div>
          <div>Selected Active users</div>
          <div class="search-icn"><input type="text" id="searchinactiveuser" name="searchinactiveuser" value="" class="form-control" placeholder="Search User..." /></div>
        </div>
        <div id="bulk_inactive_users1" style="height:350px; overflow-x:hidden; overflow:auto; margin-top:10px;" >
          <div id="bulk_inactive_users" ></div>
        </div>
        <div class="modal-footer">
          <button  class="inactiveuserbulksubmit btn btn btn-blue pull-left" recentuser="0" type="button">Inactivate</button>
        </div>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>
<div class="modal fade" id="myModal2_confirm" role="dialog">
  <div class="modal-dialog"> 
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="text-align: center">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-footer">
        <div class="text-center" id="orgadmin_account" style="display:none;">
          <button id="confirmpurchase" purchase=""  onclick="" class="btn btn-primary btn-blue" type="button">Confirm</button>
          <button class="btn btn-primary btn-blue cancelbulk" type="button">Cancel</button>
        </div>
        <div class="text-center" id="suadmin_account" style="display:none;">
          <button class="btn btn-primary btn-blue subscriptionok" type="button">ok</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="bt_updateSubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title subcription"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("updateadminsubscription", array("id"=>"updateadminsubscriptionIndexForm"));?>
        <div>
          <div>Number of users to <span id="updateType">upgrade</span></div>
          <div>
            <?php
              echo $this->Form->Input("userCount", array("type" => "text", 'class' => 'form-control', "id" => "userCount"));
              echo $this->Form->Input("action", array("type" => "hidden", 'class' => 'form-control', "id" => "action"));
              echo $this->Form->Input("organizationId", array("type" => "hidden", 'class' => 'form-control', "id" => "organizationId"));
              echo $this->Form->Input("reload", array("type" => "hidden", 'class' => 'form-control', "id" => "reload"));
            ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-blue pull-left" type="submit">Done</button>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>
<div class="modal fade" id="myModal2_overwritesubscription" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("overwriteadminsubscription", array('onSubmit'=>'return false;',"id"=>"overwriteadminsubscriptionIndexForm"));?>
        <input type="hidden" name="overwrite_org_id" id="overwrite_org_id" value="" />
        
          
          <?php echo $this->Form->Input("users", array("type" => "text", 'label'=>'Paid Users','class' => 'form-control', "id" => "overwrite_users"));?> 
          <div id="suadmin_subscription_amt" >
          
            
              <?php
                                     echo $this->Form->Input("amt", array("type" => "text",'label'=>'Amount to pay ($)', 'class' => 'form-control', "id" => "overwrite_amt"));?>
            
          </div>
        
      </div>

      <div class="modal-footer"><button id="overwriteadminsubscriptionsubmit" class="btn btn-blue pull-left"  type="button">Done</button>
      <?php echo $this->Form->end();?> 
      </div>
      </div>
  </div>
</div>

<div class="modal fade" id="myModal2_bulkReinvite" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("BulkReinvite", array('onSubmit'=>'return false;',"id"=>"bulkReinviteForm"));?>
        <input type="hidden" name="reinvite_org_id" id="reinvite_org_id" value="" />
        <div>
          <!--<div><span> <?php echo $this->Html->image('or.png',array( 'width' => '100%'));  ?></span></div>-->
          <div>Select users to re-invite</div>
          <div class="search-icn"><input type="text" id="searchReinviteUser" name="searchReinviteUser" value="" class="form-control" placeholder="Search User..." /></div>
        </div>
        <div id="bulkReinvite1" style="height:350px; overflow-x:hidden; overflow:auto; margin-top:10px;" >
          <div id="bulkReinvite" ></div>
        </div>
        <div class="modal-footer">
          <button  class="reinviteuserbulksubmit btn btn btn-blue pull-left" recentuser="0" type="button">Re-Invite</button>
        </div>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>

<div class="modal fade" id="myModal2_inviteToOtherOrg" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Select Organization</h4>
      </div>
      <div class="modal-body" style="">
        <input type="hidden" name="inviteUserId" id="inviteUserId" value="" />
          <div class="clearfix"></div>
          <div >
            <h5>Note: You can select only one organization.</h5>
          </div>
          <div class="search-icn"><input type="text" id="searchAllOrgs" name="searchAllOrgs" value="" class="form-control" placeholder="Search Organization..." /></div>
        <!--</div>-->
        <div id="bulk_active_users1" style="height:350px; overflow-x:hidden; overflow:auto; margin-top:10px;" >
          <div id="inviteToOtherOrgsDiv" ></div>
        </div>
      </div>
      <div class="modal-footer">
        <button  class="inviteToOtherOrgSubmit btn btn btn-blue pull-left" type="button" id="inviteToOtherOrgSubmit">Invite</button>
      </div>
  </div>
</div>
</div>

<div class="modal fade" id="myModal2_upgradesubscriptiontrial" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style=""> <?php echo $this->Form->create("upgradeTrialSubscription", array('onSubmit'=>'return false;',"id"=>"upgradeTrialSubscriptionForm"));?>
        <input type="hidden" name="trialUpgradeOrgId" id="trialUpgradeOrgId" value="" />
        <div > 
          <!--<div>Users</div> --> 
          <?php echo $this->Form->Input("users", array("type" => "text", 'label'=>'Number of users','class' => 'form-control', "id" => "trialUsers"));?>
          <div >
            <?php
                    $duration = array(1 => "1 Month", 2 => "2 Month",3=>"3 Month",4=>"4 month",5=>"5 month",6=>"6 Month");
                    echo $this->Form->input('trial_duration', array('empty' => 'Select Duration', 'options' => $duration, 'class' => 'form-control', "selected" => "paid", "id" => "trialDuration")); ?>
          </div>
         <!-- <div id="suadmin_subscription_paid" style="display:none;">
            
          </div>-->
        </div>
      </div>
      <div class="modal-footer">
        <button id="upgradeTrialSubscriptionSubmit" class="btn btn-blue pull-left" type="button">Done</button>
      </div>
      <?php echo $this->Form->end();?> </div>
  </div>
</div>

<div class="modal fade" id="myModal2_convertToPaid" role="dialog">
  <div class="modal-dialog"> 
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Convert to paid annual subscription</h4>
      </div>
      <div class="modal-body" style=""> 
          <?php echo $this->Form->create("convertSubscription", array('onSubmit'=>'return false;',"id"=>"convertSubscriptionForm"));?>
        <input type="hidden" name="convertToPaidOrgId" id="convertToPaidOrgId" value="" />
        <div> 
          
          <!--<div>Users</div> --> 
          <?php echo $this->Form->Input("users", array("type" => "text", 'label'=>'Number of users','class' => 'form-control', "id" => "convertUsers"));?>
            <?php
            echo $this->Form->Input("amount", array("type" => "text", 'label'=>'Amount to pay ($)','class' => 'form-control', "id" => "convertAmt"));?>
        </div>
        <?php echo $this->Form->end();?>
      </div>
      <div class="modal-footer">
        <button id="convertSubscriptionSubmit" class="btn btn-blue pull-left" type="button">Done</button>
      </div>
       </div>
  </div>
</div>