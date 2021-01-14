<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" 
      integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

<?php
$data = array(
    "textcenter" => "DAISY Portal Setting",
    "righttabs" => "3",
    "orgid" => $orgDetail['Organization']['id'],
    "video_feature" => $orgDetail['Organization']['featured_video_enabled'],
    "customer_portal" => $orgDetail['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgDetail['Organization']['enable_daisy_portal']
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
echo $this->Element($headerpage, array('data' => $data));
//pr($orgDetail['Organization']['image']); exit;
?>
<p><?php echo $this->Session->Flash(); ?></p>

<div class="stats">
    <div class="row">
        <div class="bor-bot d-flexCenter">
            <h2 class="pull-left"><?php echo $orgDetail['Organization']['name']; ?></h2>
            <?php echo $this->Html->link("All DAISY Nomination", array('controller' => 'organizations', 'action' => 'daisyendorsements', $orgDetail['Organization']['id']), array('class' => 'btn btn-success pull-right')); ?>
            <!--<a href="/nDorseV2/organizations/index" class="btn btn-success pull-right">All DAISY Nomination</a>-->

        </div>
    </div>
</div>
<section>
    <div class="">
        <section>
            <div class="customerPortal" id="fnamelname">
                <div class="createEditOrg">
                    <div class="col-lg-12 row">
                        <!--<div class="labelCus require">Customize Company Logo</div>-->
                        <!--                        <div class="labelCus" id="endorse_visible_alert" style="color: salmon;">
                                                    <div class="pull-left"><?php echo $this->Html->image("Alert_Symbol.png", array('height' => "20px", 'width' => "20px")); ?></div>
                                                    <div class="note">
                                                        *This logo will be show on Customer Feedback Portal. It will not reflect original company logo.
                                                    </div>
                                                </div>-->
                        <?php echo $this->Form->create('Orgphoto', array('url' => array('controller' => 'users', 'action' => 'setorgcpImage'))); ?>
                        <div class="">
                            <?php
                            //$orgDetail
                            //                            pr($orgDetail); exit;
                            if (isset($orgDetail)) {
                                if (isset($orgDetail['Organization']['cp_logo']) && $orgDetail['Organization']['cp_logo'] != '') {
                                    $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgDetail['Organization']['cp_logo'];
                                    echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
                                } else if (isset($orgDetail['Organization']['image']) && $orgDetail['Organization']['image'] != '') {
                                    $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgDetail['Organization']['image'];
                                    echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
                                } else {
                                    echo $this->Html->image('comp_pic.png', array('width' => '214', 'id' => 'org_image'));
                                }
                            }
                            ?>
                            <!--<button type="button"  id="org_upload_photo" class="btn btn-blue">Upload Picture</button>-->
                            <!--                                <button type="button" id="org_remove_photo" class="btn btn-blue">Remove Picture</button>-->
                            <?php
                            echo $this->Form->input('cp_logo', array(
                                'type' => 'file',
                                'id' => 'photo',
                                'label' => false,
                                'class' => 'btn_uplaod_file hidden'
                            ));
                            ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
        $portalLink = Router::url('/', true) . "daisy/index/" . base64_encode($orgDetail['Organization']['id']);
        $portalLink = str_replace("http", "https", $portalLink);
        echo $this->Form->create('Organization');
        echo $this->Form->input('id', array('type' => 'hidden', 'value' => $orgDetail['Organization']['id']));
//        echo $this->Form->input('portallink', array('type' => 'text', 'value' => $portalLink));
        echo $this->Form->input('image', array('type' => 'hidden', 'id' => 'org_image_name', 'value' => $orgDetail['Organization']['image']));
        ?>
        <section>
            <div class="clearfix"></div>
            <div class="row customerPortalLabel">
                <div class="col-md-6"> 
                    <div class="labelCus">Portallink : <span id="portal_link"><?php echo $portalLink; ?></span><div class="tooltip1">
                            <a onclick="myFunction()" onmouseout="outFunc()" class="btn btn-default btn-sm ml15"  style="word-wrap: break-word;">
                                <span class="tooltiptext" id="myTooltip" style="word-wrap: break-word;">Copy to clipboard</span>
                                Copy Link
                            </a>
                        </div></div>

                    <!--<a onclick="myFunction()">Copy text</a>-->
                    <div class="row">
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus">Show Core Values</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('daisy_show_core_values', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['daisy_show_core_values'],
                                    'class' => 'allowIt',
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                        <div class="col-md-3">
                            <div class="labelCus">Show Awards Selection</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('daisy_show_awards', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['daisy_show_awards'],
                                    'class' => 'daisy_show_awards',
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3" SelectDaisyAward>
                            <div class="labelCus SelectDaisyAward">Select DAISY Award</div>
                        </div>
                        <?php // pr($orgDetail); ?>
                        <div class="col-md-7 SelectDaisyAward">
                            <div class="selectAward">

                                <?php
//                            pr($orgDetail);
                                foreach ($DAISYAwards as $id => $awardName) {
                                    $checked = '';
                                    if (!empty($orgDetail['Organization']['daisy_active_awards']) || $orgDetail['Organization']['daisy_active_awards'] != '') {
                                        $orgDaisy_active_awards = json_decode($orgDetail['Organization']['daisy_active_awards']);
//                                   pr($orgDaisy_active_awards);//exit;
                                        if (!empty($orgDaisy_active_awards)) {
                                            if (in_array($id, $orgDaisy_active_awards)) {
                                                $checked = 'checked="checked"';
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="checkbox">
                                        <input type="checkbox" value="<?php echo $id; ?>" name="Organization[daisyawardslist][]" class="css-checkbox" <?php echo $checked; ?>  id="daisyaward_<?php echo $id; ?>">
                                        <label class="css-label" for="daisyaward_<?php echo $id; ?>"><?php echo $awardName; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <!--<select name="Organization[daisyawardslist][]" id="" multiple="multiple" class="form-control" style="display: none;">-->
                            <!--<option value="0">Select DAISY Award Type</option>-->
                            <?php /*
                              foreach ($DAISYAwards as $id => $awardName) {
                              $selected = '';
                              if (!empty($orgDetail['Organization']['daisy_active_awards']) || $orgDetail['Organization']['daisy_active_awards'] != '') {
                              $orgDaisy_active_awards = json_decode($orgDetail['Organization']['daisy_active_awards']);
                              //                                   pr($orgDaisy_active_awards);//exit;
                              if (in_array($id, $orgDaisy_active_awards)) {
                              echo $selected = 'selected="selected"';
                              }
                              }
                              ?>
                              <option value="<?php echo $id; ?>" <?php echo $selected; ?>><?php echo $awardName; ?></option>
                              <?php } */ ?>


                            <!--</select>-->
                        </div>
                    </div>


                </div>

                <!-- added by babulal prasad @28-02-2018 to show/hide leader board -->
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div>--> 

                    <div class="col-md-3">
                        <div class="labelCus" >Show Comment Box</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('daisy_show_comment_box', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $orgDetail['Organization']['daisy_show_comment_box'],
                                    )
                            );
                            ?>
                        </span> 
                    </div>

                    <?php if (isset($loggedinUser['role']) && ($loggedinUser['role'] == 1 || $loggedinUser['role'] == 2)) { ?>
                        <div class="col-md-3">
                            <div class="labelCus" > Minimum characters for Message Box </div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="text">
                                <?php
                                echo $this->Form->input('daisy_message_limit', array('type' => 'text', 'label' => false, 'value' => $orgDetail['Organization']['daisy_message_limit'], 'max' => '3', 'class' => 'onlyNumber form-control wdth50'));
                                ?>
                            </span> 
                        </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <div class="">
                        <div class="col-md-3">
                            <div class="labelCus">Enabled Default User</div>
                        </div>
                        <div class="col-md-9 dis-flex-top"> 
                            <div class="checkbox">
                                <?php $checked = $orgDetail['Organization']['daisy_default_user_enabled'] ? "checked" : ""; ?>
                                <input type="checkbox" name="Organization[daisy_default_user_enabled]" class="css-checkbox " value ="1" <?php echo $checked; ?>  id="enabled_default_user">
                                <label class="css-label" for="enabled_default_user" >
                                    &nbsp;
                                </label>
                            </div>
                            <?php $displayClass = $orgDetail['Organization']['daisy_default_user_enabled'] ? "" : "hide"; ?>
                            <div class="userlistdropdown wdth50 <?php echo $displayClass; ?>" style="margin-top: 7px;">
                                <select class="select2 enabled_default_user_select  " id="enabled_default_user_select" name="Organization[daisy_default_user_id]" style="width:125%;">
                                    <option value="0">Select Default User</option>
                                    <?php
                                    if (!empty($userList)) {
                                        foreach ($userList as $UserID => $userName) {
                                            $selected = "";
                                            if ($orgDetail['Organization']['daisy_default_user_id'] == $UserID) {
                                                $selected = "selected";
                                            }
                                            ?>
                                            <option <?php echo $selected; ?> value="<?php echo $UserID; ?>"><?php echo $userName['name']; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <p class="enabled_default_user_selectErr err" style="color: #fa8072; margin-bottom: 2%; margin-left: 0%;display: none;">
                                    <i>Please select user.</i>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="">
                        <div class="col-md-3">
                            <div class="labelCus">Enable Facility</div>
                            <div>
                                <a href="#" data-toggle="modal" data-target="#myModal_invitations" class="pending-invites btn btn-default addFacility"><strong>+</strong> Add Facility</a>
                            </div>
                        </div>
                        <div class="col-md-9 dis-flex-top"> 
                            <div class="checkbox">
                                <?php $checked = $orgDetail['Organization']['daisy_subcenters'] ? "checked" : ""; ?>
                                <input type="checkbox" name="Organization[daisy_subcenters]" class="css-checkbox" value ="1" <?php echo $checked; ?>  id="daisy_subcenters">
                                <label class="css-label" for="daisy_subcenters" >
                                    &nbsp;
                                </label>
                            </div>
                            <?php $displayClass1 = $orgDetail['Organization']['daisy_subcenters'] ? "" : "hide"; ?>
                            <div class="flxBlist subcenterlisting <?php echo $displayClass1; ?>">
                                <div class="vScoll">
                                    <?php
//                                    pr($DAISYAwards);
                                    if (!empty($DaisySubcenters)) {
                                        foreach ($DaisySubcenters as $index => $subcenterArray) {
                                            ?>
                                            <p class="" id="daisy_subcenter_<?php echo $subcenterArray['DaisySubcenter']['id']; ?>">
                                                <span><?php echo $subcenterArray['DaisySubcenter']['name']; ?></span>
                                                <span>
                                                    <a href="javascript:void(0);" class="edit_dsubcenter"  data-id="<?php echo $subcenterArray['DaisySubcenter']['id']; ?>" data-name="<?php echo $subcenterArray['DaisySubcenter']['name']; ?>"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:void(0);" class="delete_dsubcenter" data-id="<?php echo $subcenterArray['DaisySubcenter']['id']; ?>" data-name="<?php echo $subcenterArray['DaisySubcenter']['name']; ?>"><i class="fa fa-trash"></i></a>
                                                </span>
                                            </p>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <p class="">
                                            <span>Facility or Sub-center</span>
                                        </p>
                                    <?php }
                                    ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--#-->
                    <div class="clearfix"></div>

                </div>
                <!--                <div class="col-md-12">
                                    <div class="col-md-2">
                                        <div class="labelCus">Disclaimer Message</div>
                                    </div>
                                    <div class="col-md-10 dis-flex-top"> 
                                        <div class="checkbox">
                <?php $checked = $orgDetail['Organization']['cp_disclaimer_enabled'] ? "checked" : ""; ?>
                                            <input type="checkbox" name="Organization[enabled_disclaimer]" class="css-checkbox" value ="1" <?php echo $checked; ?>  id="enabled_disclaimer">
                                            <label class="css-label" for="enabled_disclaimer" >
                                                &nbsp;
                                            </label>
                                        </div>
                <?php $displayClass = $orgDetail['Organization']['cp_disclaimer_enabled'] ? "" : "hide"; ?>
                                        <div class="">
                                            <textarea placeholder="Disclaimer Message" id="enabled_disclaimer_text" class="<?php echo $displayClass; ?>" name="Organization[enabled_disclaimer_text]"><?php echo $orgDetail['Organization']['cp_disclaimer_message']; ?></textarea>
                                        </div>
                                    </div>
                                </div>-->

            </div>



            <div class="row" id="notificationNominationDiv">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="labelCus">Enable Pending Notifications</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'No',
                                    '0' => 'Yes'
                                );
                                echo $this->Form->input('enabled_daisy_notification', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['enabled_daisy_notification'],
                                    'class' => 'enabled_daisy_notification',
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                    </div>
                </div>
                <div class="col-md-6 daisy_notification_setting" style="display: none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="labelCus">Nomination Notifications</div>
                            <div>
                                <a href="#" data-toggle="modal" data-target="#myModal_nomination" class="pending-invites btn btn-default addNotificationUser"><strong>+</strong> Add User</a>
                            </div>
                        </div>
                        <div class="col-md-9 dis-flex-top"> 

                            <div class="flxBlist subcenterlisting1">
                                <div class="vScoll" style="width: 70%;">

                                    <?php
                                    if (!empty($DaisyNotifyUsersList)) {
                                        foreach ($DaisyNotifyUsersList as $index => $userData) {
//                                            pr($userData['DaisyNotifyUser']);
                                            $emailEnabled = $userData['DaisyNotifyUser']['email_enabled'];
                                            $smsEnabled = $userData['DaisyNotifyUser']['sms_enabled'];
                                            $emailId = $userData['DaisyNotifyUser']['email'];
                                            ?>
                                            <div class="flexNotic notifyusersdiv" id="daisy_notifier_<?php echo $userData['DaisyNotifyUser']['id']; ?>">
                                                <p class="" style="width: 95%;">
                                                    <span><?php echo $userData['DaisyNotifyUser']['name']; ?></span>
                                                    <span>
                                                        <?php if ($emailEnabled == 1) { ?>
                                                            <i class="fas fa-at" style="margin-top: 4px;margin-right: 5px;"></i>    
                                                        <?php } ?>
                                                        <?php if ($smsEnabled == 1) { ?>
                                                                <!--<i class="fas fa-sms"></i>-->
                                                        <?php } ?>
                                                        <a href="javascript:void(0);" class="edit_notifyuser"  data-id="<?php echo $userData['DaisyNotifyUser']['id']; ?>" data-email="<?php echo ($emailEnabled == 1) ? $emailEnabled : 0; ?>" data-emailid="<?php echo $emailId; ?>" data-name="<?php echo $userData['DaisyNotifyUser']['name']; ?>"><i class="fa fa-edit"></i></a>
                                                        <a href="javascript:void(0);" class="delete_notifyuser" data-id="<?php echo $userData['DaisyNotifyUser']['id']; ?>" data-name="<?php echo $userData['DaisyNotifyUser']['name']; ?>"><i class="fa fa-trash"></i></a>
                                                    </span>
                                                </p>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="flexNotic notifyusersdiv">
                                            <p class="" style="width: 95%;">
                                                <span>Please add user</span>

                                            </p>
                                        </div>    
                                    <?php }
                                    ?>

                                    <!--                                    <div class="flexNotic">
                                                                            <p class="">
                                                                                <span>Test</span>
                                                                                <span>
                                                                                    <i class="fas fa-at"></i> <i class="fas fa-sms"></i>
                                    
                                                                                    <a href="javascript:void(0);" class="edit_dsubcenter"  data-id="<?php echo $subcenterArray['DaisySubcenter']['id']; ?>" data-name="<?php echo $subcenterArray['DaisySubcenter']['name']; ?>"><i class="fa fa-edit"></i></a>
                                                                                    <a href="javascript:void(0);" class="delete_dsubcenter" data-id="<?php echo $subcenterArray['DaisySubcenter']['id']; ?>" data-name="<?php echo $subcenterArray['DaisySubcenter']['name']; ?>"><i class="fa fa-trash"></i></a>
                                                                                </span>
                                                                            </p>
                                                                            <div style="display: flex; align-items: center;">
                                                                                <input type="checkbox" id="successMob" name=""> <label for="successMob" class="btn btn-default"><i class="fa fa-check" aria-hidden="true"></i> Mobile</label>
                                                                                <input type="checkbox" id="successSms" name=""> <label for="successSms" class="btn btn-default"><i class="fa fa-check" aria-hidden="true"></i> SMS</label>
                                                                            </div>
                                                                        </div>-->
                                    <!--#-->



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </section>
        </form>
    </div>
</section>
<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="clientformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="DaisyportalsettingFormSubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?>
<div class="stats mb15">
    <div class="row bor-bot">
        <h3 ><span class="heading_status_type">Pending</span> DAISY Nominations
            <a class="btn statusbttn btn-warning btn-sm ml15 Pending active" data-name="Pending" disabled="disabled" data-value="0">Pending</a>
            <a class="btn statusbttn btn-danger btn-sm ml15 Rejected" data-name="Not Selected" data-value="2">Not Selected</a>
            <!--<a class="btn statusbttn btn-info btn-sm ml15  Drafted" data-name="Drafted" data-value="3">Drafted</a>-->
            <a class="btn statusbttn btn-success btn-sm ml15  Approved" data-name="Selected" data-value="1">Selected</a>
        </h3>
        <div class="row col-md-12 ">
            <!--            <div class="note">
                            *All <span id="disclaimerText">Pending</span> DAISY Nominations will be deleted after 30 days from date received.
                        </div>-->
        </div>
    </div>
</div>
<input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">
<input type="hidden" id="org_id" value="<?php echo $orgDetail['Organization']['id']; ?>">
<input id="pagename" value="daisyendorsements" type="hidden">

<div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>

<div id="searchendorsement">

    <div class="row col-md-12 ">

    </div>

    <?php
    //==============binding element to show data

    echo $this->Element("livesearchdataguest", array("orgdata" => $orgDetail));
    ?>


</div>

<!--#Nomination Notifications-->
<div class="modal fade" id="myModal_nomination" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-body">
                <div class="form-group">
                    <label>Add user for pending notification</label>
                    <input id="notification_user_name" type="input" name="notification_user_name" value="" placeholder="Enter name" class="form-control css-checkbox">
                    <span style="color: red;" class="notification_user_name_error error hide">Please enter name.</span>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="email_enabled" class="css-checkbox " value ="1"  id="enabled_daisy_email_notification">
                        <label class="css-label" for="enabled_daisy_email_notification" >
                            Email Notification
                        </label>
                        <input id="user_email" type="input" name="user_email" value="" placeholder="Enter email" class="form-control" style="display: none;">
                        <span style="color: red;" class="user_email_error error hide">Please enter email.</span>
                        <br>

                        <!-- Commented due to stop SMS notification functionality -->
                        <!--<input type="checkbox" name="sms_enabled" class="css-checkbox " value ="1"   id="enabled_daisy_sms_notification">-->
                        <!--                        <label class="css-label" for="enabled_daisy_sms_notification" >
                                                    SMS Notification
                                                </label>
                                                <input id="user_sms" type="input" name="user_sms" value="" placeholder="Enter mobile number" class="form-control onlyNumber" style="display: none;">
                                                <span style="color: red;" class="user_sms_error error hide">Please enter mobile no.</span>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="add_daisy_notifications" class="btn btn-primary">ADD</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal_nomination_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-body">
                <div class="form-group">
                    <label>Edit user for pending notification</label>
                    <input id="update_notify_id" type="hidden" name="update_notify_id" value="">
                    <input id="update_notification_user_name" type="input" name="update_notification_user_name" value="" placeholder="Enter name" class="form-control css-checkbox">
                    <span style="color: red;" class="update_notification_user_name_error error hide">Please enter name.</span>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="update_email_enabled" class="css-checkbox " value ="1"  id="update_enabled_daisy_email_notification">
                        <label class="css-label" for="update_enabled_daisy_email_notification" >
                            Email Notification
                        </label>
                        <input id="update_user_email" type="input" name="update_user_email" value="" placeholder="Enter email" class="form-control" style="display: none;">
                        <span style="color: red;" class="update_user_email_error error hide">Please enter email.</span>
                        <br>

                        <!-- Commented due to stop SMS notification functionality -->
                        <!--<input type="checkbox" name="sms_enabled" class="css-checkbox " value ="1"   id="enabled_daisy_sms_notification">-->
                        <!--                        <label class="css-label" for="enabled_daisy_sms_notification" >
                                                    SMS Notification
                                                </label>
                                                <input id="user_sms" type="input" name="user_sms" value="" placeholder="Enter mobile number" class="form-control onlyNumber" style="display: none;">
                                                <span style="color: red;" class="user_sms_error error hide">Please enter mobile no.</span>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="update_daisy_notifications" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>

<!--#Facility Form-->
<div class="modal fade" id="myModal_invitations" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-body">
                <div class="form-group">
                    <label>Please enter Sub center/Facility</label>
                    <input id="facility_name" type="input" name="facility_name" value="" placeholder="Enter Sub center/ Facility Name" class="form-control css-checkbox">
                    <span style="color: red;" class="facility_name_error hide">Please enter Sub center/Facility name.</span>
                </div>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="add_daisy_facility" class="btn btn-primary">ADD</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal_subcenterUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-body">
                <div class="form-group">
                    <label>Update Sub center/Facility</label>
                    <input id="update_facility_id" type="hidden" name="facility_id" value="">
                    <input id="update_facility_name" type="input" name="update_facility_name" value="" placeholder="Enter Sub center/ Facility Name" class="form-control css-checkbox">
                    <span style="color: red;" class="update_facility_name_error hide">Please enter Sub center/Facility name.</span>
                </div>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="update_daisy_facility" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>



<script>



    $("#facility_name").val('');

    $(".edit_notifyuser").on("click", function () {
        $(".update_notification_user_name").addClass('hide');
        var notifyID = $(this).attr('data-id');
        var subcenterName = $(this).attr('data-name');
        var emailEnabled = $(this).attr('data-email');
        var emailId = $(this).attr('data-emailid');
        console.log(notifyID);
        $(".update_user_email_error").addClass('hide');
        $(".update_notification_user_name_error").addClass('hide');

        if (emailEnabled == 1) {
            $("#update_enabled_daisy_email_notification").prop('checked', true);
            $("#update_user_email").val(emailId).show();
        } else {
            $("#update_enabled_daisy_email_notification").prop('checked', false);
            $("#update_user_email").val('').hide();
        }

        $("#update_notification_user_name").val(subcenterName);
        $("#update_notify_id").val(notifyID);
        $("#myModal_nomination_update").modal("show");
    });

    $(".delete_notifyuser").on("click", function () {
        var notify_id = $(this).attr('data-id');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";
        console.log(notify_id);
        url = siteurl + 'ajax/deleteDaisyNotifier';

        bootbox.confirm({
            title: "Are you sure you want to delete this user?",
            message: ' ',
            buttons: btnObj,
            closeButton: false,
            callback: function (result) {
                if (result == true) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {notify_id: notify_id, org_id: org_id},
                        success: function (response) {
                            if (response.status == false) {
                                alertbootboxcb(response.msg, function () {

                                });
                            } else {
                                $("#daisy_notifier_" + notify_id).remove();

                                var notifyUser = $(".notifyusersdiv").length;
                                if (notifyUser > 2) {
                                    $(".addNotificationUser").hide();
                                } else {
                                    $(".addNotificationUser").show();
                                }

                                alertbootboxcb("Notify user deleted!", function () {
                                });
                            }
                            $(".arrow_box").hide();
                        },
                        error: function (response) {
                        }
                    });
                }
            }
        });
    });


    $(".edit_dsubcenter").on("click", function () {
        $(".update_facility_name_error").addClass('hide');
        var subcenterId = $(this).attr('data-id');
        var subcenterName = $(this).attr('data-name');
        console.log(subcenterId);
        $("#update_facility_name").val(subcenterName);
        $("#update_facility_id").val(subcenterId);
        $("#myModal_subcenterUpdate").modal("show");
    });

    $(".delete_dsubcenter").on("click", function () {
        var subcenterId = $(this).attr('data-id');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";
        console.log(subcenterId);
        url = siteurl + 'ajax/deleteDaisyFacility';

        bootbox.confirm({
            title: "Are you sure you want to delete this subcenter/facility?",
            message: ' ',
            buttons: btnObj,
            closeButton: false,
            callback: function (result) {
                if (result == true) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {facility_id: subcenterId, org_id: org_id},
                        success: function (response) {
                            if (response.status == false) {
                                alertbootboxcb(response.msg, function () {

                                });
                            } else {
                                $("#daisy_subcenter_" + subcenterId).remove();
                                alertbootboxcb("Subcenter deleted!", function () {
                                });
                            }
                            $(".arrow_box").hide();
                        },
                        error: function (response) {
                        }
                    });
                }
            }
        });
    });

    $("#update_daisy_facility").click(function () {
        var facility_name = $.trim($("#update_facility_name").val());
        var facility_id = $.trim($("#update_facility_id").val());
        $(".update_facility_name_error").addClass('hide');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";
        var error = 0;
        if (facility_name == '') {
            $(".update_facility_name_error").removeClass('hide');
            error = 1;
        }

        if (error == 0) {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/updateDaisyFacility',
                dataType: 'json',
                data: {facility_name: facility_name, org_id: org_id, facility_id: facility_id},
                success: function (response) {
                    if (response.status == false) {
                        alertbootboxcb(response.msg, function () {
                        });
                    } else {
                        alertbootboxcb("Subcenter updated!", function () {
                        });
                        window.location.reload();
                        $("#myModal_invitations").modal("hide");
                    }
                },
                error: function (response) {
                }
            });
        }
    });

    $("#add_daisy_facility").click(function () {
        var facility_name = $.trim($("#facility_name").val());
        $(".facility_name_error").addClass('hide');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";
        var error = 0;
        if (facility_name == '') {
            $(".facility_name_error").removeClass('hide');
            error = 1;
        }
        if (error == 0) {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/saveDaisyFacility',
                dataType: 'json',
                data: {facility_name: facility_name, org_id: org_id},
                success: function (response) {
                    if (response.status == false) {
                        alertbootboxcb(response.msg, function () {
                        });
                    } else {
                        window.location.reload();
                        $("#myModal_invitations").modal("hide");
                    }
                },
                error: function (response) {
                }
            });
        }
    });

    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    $("#enabled_daisy_email_notification").on('click', function () {
        var emailIsChecked = $('#enabled_daisy_email_notification:checkbox:checked').length > 0;
        if (emailIsChecked) {
            $("#user_email").fadeIn('medium');
        } else {
            $("#user_email").fadeOut('medium');
            $(".user_email_error").addClass('hide');
        }

    });
    $("#enabled_daisy_sms_notification").on('click', function () {
        var smsIsChecked = $('#enabled_daisy_sms_notification:checkbox:checked').length > 0;
        if (smsIsChecked) {
            $("#user_sms").fadeIn('medium');
        } else {
            $("#user_sms").fadeOut('medium');
            $(".user_sms_error").addClass('hide');
        }

    });

    $("#update_enabled_daisy_email_notification").on('click', function () {
        var emailIsChecked = $('#update_enabled_daisy_email_notification:checkbox:checked').length > 0;
        if (emailIsChecked) {
            $("#update_user_email").fadeIn('medium');
        } else {
            $("#update_user_email").fadeOut('medium');
            $(".update_user_email_error").addClass('hide');
        }

    });
    $("#update_enabled_daisy_sms_notification").on('click', function () {
        var smsIsChecked = $('#update_enabled_daisy_sms_notification:checkbox:checked').length > 0;
        if (smsIsChecked) {
            $("#update_user_sms").fadeIn('medium');
        } else {
            $("#update_user_sms").fadeOut('medium');
            $(".update_user_sms_error").addClass('hide');
        }

    });

    $(".addNotificationUser").on('click', function () {
        $("#actionName").html('Add ');
        $("#notification_user_name").val('');
        $("#user_email").val('');
        $("#user_sms").val('');
        $(".user_email_error").addClass('hide');
        $(".notification_user_name_error").addClass('hide');
        $("#enabled_daisy_sms_notification").prop("checked", false);
        $("#enabled_daisy_email_notification").prop("checked", false);
        $("#user_email").val('').hide();
    });

    $("#add_daisy_notifications").click(function () {
        var notification_user_name = $.trim($("#notification_user_name").val());
        $('.error').addClass('hide');
        var email_enabled = 0;
        var sms_enabled = 0;
        var error = 0;
        var emailIsChecked = $('#enabled_daisy_email_notification:checkbox:checked').length > 0;
        var smsIsChecked = $('#enabled_daisy_sms_notification:checkbox:checked').length > 0;
        if (emailIsChecked) {
            var user_email = $.trim($("#user_email").val());
            if (user_email == '') {
                $(".user_email_error").html('Please enter email.');
                $(".user_email_error").removeClass('hide');
                error = 1;
            } else {
                if (IsEmail(user_email) == false) {
                    $(".user_email_error").html('Please enter valid email.');
                    $(".user_email_error").removeClass('hide');
                    error = 1;
                }
            }
            email_enabled = 1;
        } else {
            user_email = '';
        }

        if (smsIsChecked) {
            var user_sms = $.trim($("#user_sms").val());
            if (user_sms == '') {
                $(".user_sms_error").html('Please enter mobile number.');
                $(".user_sms_error").removeClass('hide');
                error = 1;
            } else {
                if (user_sms.length != 10) {
                    $(".user_sms_error").html('Please enter valid mobile number.');
                    $(".user_sms_error").removeClass('hide');
                    error = 1;
                }
            }
            sms_enabled = 1;
        } else {
            user_sms = '';
        }

        $(".notification_user_name_error").addClass('hide');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";

//        var error = 0;

        if (notification_user_name == '') {
            $(".notification_user_name_error").removeClass('hide');
            error = 1;
        }

        if (error == 0) {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/saveDaisyNotificationUser',
                dataType: 'json',
                data: {facility_name: notification_user_name, org_id: org_id, email_enabled: email_enabled, sms_enabled: sms_enabled, user_email: user_email, user_sms: user_sms},
                success: function (response) {
                    if (response.status == false) {
                        alertbootboxcb(response.msg, function () {
                        });
                    } else {
                        window.location.reload();
                        $("#myModal_nomination").modal("hide");
                    }
                },
                error: function (response) {
                }
            });
        }
    });

    $("#update_daisy_notifications").click(function () {

        var update_notification_user_name = $.trim($("#update_notification_user_name").val());
        var notifyID = $("#update_notify_id").val();
        $('.error').addClass('hide');
        var email_enabled = 0;
        var sms_enabled = 0;
        var error = 0;
        var emailIsChecked = $('#update_enabled_daisy_email_notification:checkbox:checked').length > 0;
        var smsIsChecked = $('#update_enabled_daisy_sms_notification:checkbox:checked').length > 0;
        if (emailIsChecked) {
            var user_email = $.trim($("#update_user_email").val());
            if (user_email == '') {
                $(".update_user_email_error").html('Please enter email.');
                $(".update_user_email_error").removeClass('hide');
                error = 1;
            } else {
                if (IsEmail(user_email) == false) {
                    $(".update_user_email_error").html('Please enter valid email.');
                    $(".update_user_email_error").removeClass('hide');
                    error = 1;
                }
            }
            email_enabled = 1;
        } else {
            user_email = '';
        }

        if (smsIsChecked) {
            var user_sms = $.trim($("#update_user_sms").val());
            if (user_sms == '') {
                $(".update_user_sms_error").html('Please enter mobile number.');
                $(".update_user_sms_error").removeClass('hide');
                error = 1;
            } else {
                if (user_sms.length != 10) {
                    $(".update_user_sms_error").html('Please enter valid mobile number.');
                    $(".update_user_sms_error").removeClass('hide');
                    error = 1;
                }
            }
            sms_enabled = 1;
        } else {
            user_sms = '';
        }

        $(".update_notification_user_name_error").addClass('hide');
        var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";

//        var error = 0;

        if (update_notification_user_name == '') {
            $(".update_notification_user_name_error").removeClass('hide');
            error = 1;
        }
//        console.log("tset");
//        return false;
        if (error == 0) {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/updateDaisyNotificationUser',
                dataType: 'json',
                data: {facility_name: update_notification_user_name, org_id: org_id, email_enabled: email_enabled, sms_enabled: sms_enabled, user_email: user_email, user_sms: user_sms, id: notifyID},
                success: function (response) {
                    if (response.status == false) {
                        alertbootboxcb(response.msg, function () {
                        });
                    } else {
                        window.location.reload();
                        $("#myModal_nomination_update").modal("hide");
                    }
                },
                error: function (response) {
                }
            });
        }
    });

    $("#DaisyportalsettingFormSubmit").click(function () {
        var errorFlag = false;
        $(".err").hide();
        if (!errorFlag) {
            $("#OrganizationDaisyportalsettingForm").submit();
        }
    });


    $("#daisy_subcenters").on('click', function () {
        if ($(this).is(":checked")) {
            $(".subcenterlisting").removeClass("hide");
        } else {
            $(".subcenterlisting").addClass("hide");
        }
    });


    function myFunction() {
        var $temp = $("<input>");
        $("body").append($temp);
        var myCode = $('#portal_link').html();
        $temp.val(myCode).select();
        document.execCommand("copy");
        $temp.remove();
        var tooltip = document.getElementById("myTooltip");
        //tooltip.innerHTML = "Link Copied: " + myCode;
        tooltip.innerHTML = "Link Copied";
    }

    function outFunc() {
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "Copy to clipboard";
    }

    $(document).ready(function () {


//                

        var notifyUser = $(".notifyusersdiv").length;
        if (notifyUser > 2) {
            $(".addNotificationUser").hide();
        } else {
            $(".addNotificationUser").show();
        }



        $('.daisy_show_awards').on('click', function () {
            if ($('input[class=daisy_show_awards]:checked').val() == 1) {
                $('.SelectDaisyAward').fadeIn('medium');
            } else {
                $('.SelectDaisyAward').fadeOut('medium');
            }
        });


        var daisyEnabled = '<?php echo $orgDetail['Organization']['enabled_daisy_notification']; ?>';
        if (daisyEnabled == 0) {
            $('.daisy_notification_setting').fadeIn('medium');
        }
        $('.enabled_daisy_notification').on('click', function () {
            if ($('input[class=enabled_daisy_notification]:checked').val() == 0) {
                $('.daisy_notification_setting').fadeIn('medium');
            } else {
                $('.daisy_notification_setting').fadeOut('medium');
            }
        });

        $("#enabled_disclaimer").on('click', function () {
            if ($(this).is(":checked")) {
                $("#enabled_disclaimer_text").removeClass("hide");
            } else {
                $("#enabled_disclaimer_text").addClass("hide");
            }
        });

        $("#enabled_default_user").on('click', function () {
            if ($(this).is(":checked")) {
                $(".userlistdropdown").removeClass("hide");
            } else {
                $(".userlistdropdown").addClass("hide");
            }
        });

        $(".select2").select2();

    });

</script>