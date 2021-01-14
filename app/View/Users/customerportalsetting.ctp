<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" 
      integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<?php
$data = array(
    "textcenter" => "Customer Portal Setting",
    "righttabs" => "3",
    "orgid" => $orgDetail['Organization']['id'],
    "video_feature" => $orgDetail['Organization']['featured_video_enabled'],
    "customer_portal" => $orgDetail['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgDetail['Organization']['enable_daisy_portal']
);
//pr($data); exit;
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
echo $this->Element($headerpage, array('data' => $data));
//pr($orgDetail['Organization']['image']); exit;
?>
<p><?php echo $this->Session->Flash(); ?></p>

<div class="stats">
    <div class="row bor-bot">
        <h2><?php echo $orgDetail['Organization']['name']; ?></h2>
    </div>
</div>
<section>
    <div class="">
        <section>
            <div class="customerPortal" id="fnamelname">
                <div class="createEditOrg">
                    <div class="col-lg-12 row">
                        <div class="labelCus require">Customize Company Logo</div>
                        <div class="labelCus" id="endorse_visible_alert" style="color: salmon;">
                            <div class="pull-left"><?php echo $this->Html->image("Alert_Symbol.png", array('height' => "20px", 'width' => "20px")); ?></div>
                            <div class="note">
                                *This logo will be show on Customer Feedback Portal. It will not reflect original company logo.
                            </div>
                        </div>
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
                            <button type="button"  id="org_upload_photo" class="btn btn-blue">Upload Picture</button>
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
        $portalLink = Router::url('/', true) . "guest/index/" . base64_encode($orgDetail['Organization']['id']);
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
                            echo $this->Form->input('cp_show_core_values', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $orgDetail['Organization']['cp_show_core_values'],
                                'class' => 'allowIt',
                                    )
                            );
                            ?>
                        </span> 
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
                            echo $this->Form->input('cp_show_comment', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $orgDetail['Organization']['cp_show_comment'],
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                    <?php if (isset($loggedinUser['role']) && $loggedinUser['role'] == 1) { ?>
                        <div class="col-md-3">
                            <div class="labelCus" > Minimum characters for Message Box</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="text">
                                <?php
                                echo $this->Form->input('cp_message_limit', array('type' => 'text', 'label' => false, 'value' => $orgDetail['Organization']['cp_message_limit'], 'max' => '3', 'class' => 'onlyNumber'));
                                ?>
                            </span> 
                        </div>
                    <?php } ?>

                </div>
                <div class="col-md-12">
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
                </div>
                <div class="col-md-12">
                    <div class="col-md-2">
                        <div class="labelCus">Enabled Default User</div>
                    </div>
                    <div class="col-md-10 dis-flex-top"> 
                        <div class="checkbox">
                            <?php $checked = $orgDetail['Organization']['cp_default_user_enabled'] ? "checked" : ""; ?>
                            <input type="checkbox" name="Organization[enabled_default_user]" class="css-checkbox" value ="1" <?php echo $checked; ?>  id="enabled_default_user">
                            <label class="css-label" for="enabled_default_user" >
                                &nbsp;
                            </label>
                        </div>
                        <?php $displayClass = $orgDetail['Organization']['cp_default_user_enabled'] ? "" : "hide"; ?>
                        <div class="userlistdropdown <?php echo $displayClass; ?>" style="margin-top: 7px;">
                            <select class="select2 enabled_default_user_select " id="enabled_default_user_select" name="Organization[enabled_default_user_id]" style="width:125%;">
                                <option value="0">Select Default User</option>
                                <?php
                                if (!empty($userList)) {
                                    foreach ($userList as $UserID => $userName) {
                                        $selected = "";
                                        if ($orgDetail['Organization']['cp_default_user_id'] == $UserID) {
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


                <div class="col-md-12" id="">
                    <div class="col-md-6">

                        <div class="col-md-3">
                            <div class="labelCus">Enable Pending Notifications</div>
                        </div>
                        <div class="col-md-9"> 

                            <span class="radio">
                                <?php
                                $options = array(
                                    '0' => 'No',
                                    '1' => 'Yes'
                                );
                                echo $this->Form->input('enabled_feedback_notification', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['enabled_feedback_notification'],
                                    'class' => 'enabled_feedback_notification',
                                        )
                                );
                                ?>
                            </span> 
                        </div>

                    </div>
                    <div class="col-md-6 feedback_notification_setting" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="labelCus">Nomination Notifications</div>
                                <div>
                                    <a href="#" data-toggle="modal" data-target="#myModal_nomination" class="pending-invites btn btn-default addNotificationUser"><strong>+</strong> Add User</a>
                                </div>
                            </div>
                            <div class="col-md-9 dis-flex-top"> 

                                <div class="flxBlist feedbackNotifyUsers">
                                    <div class="vScoll" style="width: 70%;">

                                        <?php
                                        if (!empty($FeedbackNotifyUsersList)) {
                                            foreach ($FeedbackNotifyUsersList as $index => $userData) {
//                                            pr($userData['FeedbackNotifyUser']);
                                                $emailEnabled = $userData['FeedbackNotifyUser']['email_enabled'];
                                                $smsEnabled = $userData['FeedbackNotifyUser']['sms_enabled'];
                                                $emailId = $userData['FeedbackNotifyUser']['email'];
                                                ?>
                                                <div class="flexNotic notifyusersdiv" id="daisy_notifier_<?php echo $userData['FeedbackNotifyUser']['id']; ?>">
                                                    <p class="" style="width: 95%;">
                                                        <span><?php echo $userData['FeedbackNotifyUser']['name']; ?></span>
                                                        <span>
                                                            <?php if ($emailEnabled == 1) { ?>
                                                                <i class="fas fa-at"></i>    
                                                            <?php } ?>
                                                            <?php if ($smsEnabled == 1) { ?>
                                                                <!--<i class="fas fa-sms"></i>-->
                                                            <?php } ?>
                                                            <a href="javascript:void(0);" class="edit_notifyuser"  data-id="<?php echo $userData['FeedbackNotifyUser']['id']; ?>" data-email="<?php echo ($emailEnabled == 1) ? $emailEnabled : 0; ?>" data-emailid="<?php echo $emailId; ?>" data-name="<?php echo $userData['FeedbackNotifyUser']['name']; ?>"><i class="fa fa-edit"></i></a>
                                                            <a href="javascript:void(0);" class="delete_notifyuser" data-id="<?php echo $userData['FeedbackNotifyUser']['id']; ?>" data-name="<?php echo $userData['FeedbackNotifyUser']['name']; ?>"><i class="fa fa-trash"></i></a>
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
                                    </div>
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
                <button type="button" class="btn btn-default" id="CustomerportalsettingFormSubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?>
<div class="stats mb15">
    <div class="row bor-bot">
        <h3 ><span class="heading_status_type">Pending</span> Guest nDorsements
            <a class="btn statusbttn btn-warning btn-sm ml15 Pending active" data-name="Pending" disabled="disabled" data-value="0">Pending</a>
            <a class="btn statusbttn btn-danger btn-sm ml15 Rejected" data-name="Rejected" data-value="2">Rejected</a>
            <a class="btn statusbttn btn-info btn-sm ml15  Drafted" data-name="Drafted" data-value="3">Drafted</a>
        </h3>
        <div class="row col-md-12 ">
            <div class="note">
                *All <span id="disclaimerText">Pending</span> nDorsements will be deleted after 30 days from date received.
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">
<input type="hidden" id="org_id" value="<?php echo $orgDetail['Organization']['id']; ?>">
<input id="pagename" value="guestendorsements" type="hidden">

<div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>

<div id="searchendorsement">

    <div class="row col-md-12 ">

    </div>

    <?php
    //==============binding element to show data
    //pr($orgDetail);
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
                <button type="button" id="add_feedback_notifications" class="btn btn-primary">ADD</button>
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
                <button type="button" id="update_feedback_notifications" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {


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
            url = siteurl + 'ajax/deleteFeedbackNotifier';

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

        $("#update_feedback_notifications").click(function () {

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
                    url: siteurl + 'ajax/updateFeedbackNotificationUser',
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




        var feedbackEnabled = '<?php echo $orgDetail['Organization']['enabled_feedback_notification']; ?>';
        if (feedbackEnabled == 1) {
            $('.feedback_notification_setting').fadeIn('medium');
        }

        $('.enabled_feedback_notification').on('click', function () {

            if ($('input[class=enabled_feedback_notification]:checked').val() == 1) {
                $('.feedback_notification_setting').fadeIn('medium');
            } else {
                $('.feedback_notification_setting').fadeOut('medium');
            }
        });


    });


    $("#enabled_daisy_email_notification").on('click', function () {
        var emailIsChecked = $('#enabled_daisy_email_notification:checkbox:checked').length > 0;
        if (emailIsChecked) {
            $("#user_email").fadeIn('medium');
        } else {
            $("#user_email").fadeOut('medium');
            $(".user_email_error").addClass('hide');
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


    $("#add_feedback_notifications").click(function () {
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
                url: siteurl + 'ajax/saveFeedbackNotificationUser',
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



    $("#CustomerportalsettingFormSubmit").click(function () {
        var errorFlag = false;
        $(".err").hide();
        if ($("#enabled_default_user").is(":checked")) {
            var enabledUserId = $("#enabled_default_user_select").val();
            if (enabledUserId < 1) {
                $(".enabled_default_user_selectErr").show();
                errorFlag = true;
            }
        }
        if (!errorFlag) {
            $("#OrganizationCustomerportalsettingForm").submit();
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

        var notifyUser = $(".notifyusersdiv").length;
        if (notifyUser > 2) {
            $(".addNotificationUser").hide();
        } else {
            $(".addNotificationUser").show();
        }

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