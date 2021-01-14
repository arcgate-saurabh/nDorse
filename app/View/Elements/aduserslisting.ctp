<?php

$loggedinUser = AuthComponent::user();
//pr($loggedinUser); exit;
foreach ($org_user_data as $data) {

    $post_id[] = $data['UserOrganization']['id'];
    ?>
<tr id="row_<?php echo $data['UserOrganization']['id']; ?>">
    <td style="width: 3%;" >
            <?php
            $enabledClass = ($data['User']['daisy_enabled'] == 1) ? "show" : "hide";
            echo $this->Html->image('daisy-logo.png', array('id' => "daisy_label_" . $data['User']['id'], 'height' => '30', 'width' => '30', 'class' => $enabledClass));
            ?>
    </td>
    <td>
            <?php
            $user_image = $data['User']['image'];
            if ($user_image == "") {
                echo $this->Html->image('user.png', array('class' => "img-circle", 'width' => '61', 'height' => '61'));
            } else {
                if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image)) {
                    $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $user_image;
                    echo $this->Html->image($user_imagenew, array('width' => '61', 'height' => '61', 'id' => 'org_image', 'class' => "img-circle"));
                } else {
                    echo $this->Html->image('user.png', array('class' => 'img-circle', 'width' => '61', 'height' => '61'));
                }
            }
            ?>
    </td>
    <td>
        <h6 style="color:#ffffff; font-size:18px; margin:2px; color:#337ab7">
                <?php
                
                $name = ucfirst($data['User']['fname']) . ' ' . ucfirst($data['User']['lname']);
                

//                $decodedFname = $this->App->decodeData($data['User']['fname']);
//                $decodedLname = $this->App->decodeData($data['User']['lname']);
//                
//                $name = ucfirst($decodedFname) . ' ' . ucfirst($decodedLname);

                echo '<div style="cursor:pointer" class="usersprofile" data-userorgid="' . $data['UserOrganization']['id'] . '">' . $name . '</div>';
                ?>
        </h6>
            <?php
            if($data['User']['source'] == 'activedirectory'){
                    echo "(AD User)";
                } ?>
        <p style="color:#c2c1c1; font-size:14px;"><?php echo $data['User']['email']; ?><br>
            Last updated on: <?php
                //echo $this->App->dateConvertDisplay($data['User']['updated']);
                echo $this->Time->Format($data['User']['updated'], DATEFORMAT);
                //echo $data['User']['updated']; 
                ?><br>
            Created on: <?php echo $this->Time->Format($data['User']['created'], DATEFORMAT); ?>
        </p>
    </td>
    <td class="text-active"><?php echo ($data['UserOrganization']['status'] == 1) ? "Active" : (($data['UserOrganization']['status'] == 0) ? "Inactive" : "Evaluation Mode"); ?>
    <td id="roleendorser_<?php echo $data['UserOrganization']['user_id']; ?>">
            <?php
//            echo ($data['UserOrganization']['user_role'] == 3) ? ENDORSER : DESIGNATEDADMIN; 
            if ($data['UserOrganization']['user_role'] == 3) {
                echo ENDORSER;
            } else if ($data['UserOrganization']['user_role'] == 2) {
                echo DESIGNATEDADMIN;
            } else if ($data['UserOrganization']['user_role'] == 6) {
                echo NDORSEELITE;
            }
            ?>
    </td>
    <td>
            <?php
            // echo $orgstatus."---".$admin_id."---".$orguser_id;
            if ($orgstatus == 1 && $orguser_id != $data['User']['id']) {
                ?>
                <?php if ($loggedinUser["role"] != 6) { ?>
        <div class="ThreeDotsImg pull-right"><a href="javascript:void(0);" rel="<?php echo $data['User']['id']; ?>_one" class="dots"><?php echo $this->Html->image('3dots.png', array('class' => "img-responsive")); ?></a>

            <div class="arrow_box <?php echo $data['User']['id']; ?>_one">
                <div class="pull-right popupArrow" style=" margin-top:-25px;"><?php echo $this->Html->image('popupArrow.png', array('class' => "img-responsive")); ?></div>
                <ul style="margin-left:  -25px !important;">
                                <?php $changedrole = ($data['UserOrganization']['user_role'] == 3 || $data['UserOrganization']['user_role'] == 6) ? "2" : "3"; ?>
                                <?php $changedroleELite = ($data['UserOrganization']['user_role'] == 6) ? "3" : "6"; ?>
                    <li id="changestatus_<?php echo $data["UserOrganization"]["id"]; ?>">
                                    <?php if ($data['UserOrganization']['status'] == 3) { ?>
                        <a href="javascript:void(0)" onclick="changestatususers(<?php echo $data["UserOrganization"]["id"]; ?>, 'active')">Remove Evaluation Mode</a>
                                    <?php } elseif ($data['UserOrganization']['status'] == 1) { ?>
                        <!--<a onclick="showinactivepopup(<?php //echo $data['UserOrganization']['id'];                     ?>)" href="javascript:void(0)">Inactive</a>-->
                        <a onclick="changestatususers(<?php echo $data['UserOrganization']['id']; ?>, 'partially active')" href="javascript:void(0)">Evaluation Mode</a>
                                    <?php } ?>
                    </li>
                                <?php if (in_array($data['UserOrganization']['status'], array(1, 3))) { ?>
                    <li id="funcchangerole_<?php echo $data['UserOrganization']['user_id']; ?>"><a onclick="changeendorserrole(<?php echo $data['UserOrganization']['user_id']; ?>, <?php echo $data['UserOrganization']['organization_id']; ?>, <?php echo $changedrole; ?>)" href="javascript:void(0)" ><?php echo ($data['UserOrganization']['user_role'] == 3 || $data['UserOrganization']['user_role'] == 6) ? Configure::read("Give_Admin_Control") : Configure::read("Revoke_Admin_Control"); ?></a></li>
                                <?php } ?>
                                <?php if (in_array($data['UserOrganization']['status'], array(1, 3))) { ?>
                    <li id="funcchangerolenew_<?php echo $data['UserOrganization']['user_id']; ?>">
                        <a onclick="changeendorserrolenew(<?php echo $data['UserOrganization']['user_id']; ?>, <?php echo $data['UserOrganization']['organization_id']; ?>, <?php echo $changedroleELite; ?>)" href="javascript:void(0)" >
                                            <?php echo ($data['UserOrganization']['user_role'] == 3 || $data['UserOrganization']['user_role'] == 2) ? Configure::read("Give_Elite_Control") : Configure::read("Revoke_Elite_Control"); ?>
                        </a>
                    </li>
                                <?php } ?>

                    <li id="changedaisystatus_<?php echo $data['UserOrganization']['user_id']; ?>">
                        <a onclick="setdaisyusers(<?php echo $data['User']['id']; ?>, <?php echo ($data['User']['daisy_enabled'] == 0) ? 1 : 0; ?>)" href="javascript:void(0)" >
                                        <?php echo ($data['User']['daisy_enabled'] == 0) ? Configure::read("Give_DAISY_Control") : Configure::read("Revoke_DAISY_Control"); ?>
                        </a>
                    </li>

                    <!--<li><a onclick="showdeletepopup(<?php echo $data['UserOrganization']['id']; ?>)" href="javascript:void(0)">Delete</a></li>-->
                                <?php if (isset($data["UserOrganization"]) && $data["UserOrganization"]["joined"] == 0 && $data["UserOrganization"]['status'] == 1) { ?>
                    <li><a onclick="reinviteweb(<?php echo $data['User']['id']; ?>, '<?php echo $data['User']['email']; ?>', '<?php echo $data['User']['fname']; ?>', <?php echo $orgdata['Organization']['id']; ?>, '<?php echo $orgdata['Organization']['name']; ?>', '<?php echo $org_image = $orgdata['Organization']['secret_code']; ?>')" href="javascript:void(0)">Reinvite</a></li>
                                <?php } ?>

                                <?php
                                if ($loggedinUser["role"] == 1 || $loggedinUser["role"] == 2) {
                                    $userOrgStatusChangeTO = 'inactive';
                                    $userOrgStatusChangeLable = 'Inactive';

                                    if ($data['UserOrganization']['status'] == 0) { //0=inactive, 1 = active
                                        $userOrgStatusChangeTO = 'active';
                                        $userOrgStatusChangeLable = 'Active';
                                    }
                                    ?>
                    <li><a href="javascript:void(0)" class="changestatusofuser" onclick="changestatususersnew(<?php echo $data['UserOrganization']['user_id']; ?>,<?php echo $data["UserOrganization"]["organization_id"]; ?>, '<?php echo $userOrgStatusChangeTO; ?>')"><?php echo $userOrgStatusChangeLable; ?></a></li>
                                    <?php } ?>
                    <li>
                                    <?php echo $this->Html->link('Edit user information', array('controller' => 'users', 'action' => 'editcuser', $data['User']['id']), array('rel' => $data['User']['id'])); ?>
                    </li>
                </ul>
            </div>
        </div>
                <?php } ?>
            <?php } ?>

    </td>
</tr>
<?php } ?>
 