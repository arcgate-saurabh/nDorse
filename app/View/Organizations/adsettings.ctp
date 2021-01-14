<?php

//foreach($disabledUsers as $i => $uData){ 
//    pr($uData);
//}
//exit;
//pr($orgADSettings[0]['OrgAdSetting']); exit;
$data = array(
    "textcenter" => "Active Directory Setting",
    "righttabs" => "3",
    "orgid" => $orgDetail['Organization']['id'],
    "video_feature" => $orgDetail['Organization']['featured_video_enabled'],
    "customer_portal" => $orgDetail['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgDetail['Organization']['enable_daisy_portal']
);

$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
echo $this->Element($headerpage, array('data' => $data));
$orgId = $orgDetail['Organization']['id'];
?>
<p><?php echo $this->Session->Flash(); ?></p>

<div class="stats">
    <div class="row">
        <div class="bor-bot d-flexCenter">
            <h2 class="pull-left">
                <?php echo $this->Html->link($orgDetail['Organization']['name'],array('controller'=>'organizations','action'=>'info',$orgDetail['Organization']['id'])); ?>
            </h2>
            <?php if(isset($orgADSettings) && !empty($orgADSettings)){ ?>
            <?php echo $this->Html->link("Add New Users", 'javascript:void(0);', array('data-target'=>'#myModal_adusers','data-toggle' => 'modal', 'class' => 'btn btn-success pull-right')); ?>
            &nbsp; &nbsp; 
            <?php echo $this->Html->link("Disable Users", 'javascript:void(0);', array('data-target'=>'#myModal_deactiveusers','data-toggle' => 'modal', 'class' => 'btn btn-danger pull-right')); ?>
            &nbsp; &nbsp; 
            <?php echo $this->Html->link("Enable Users", 'javascript:void(0);', array('data-target'=>'#myModal_reactiveusers','data-toggle' => 'modal', 'class' => 'btn btn-primary pull-right')); ?>
            <?php } ?>
        </div>
    </div>
</div>
<section>
    <div class="">
        <section>
            <div class="customerPortal" id="fnamelname">
                <div class="createEditOrg">

                </div>
            </div>
        </section>
        <?php
        echo $this->Form->create('Organization');
        echo $this->Form->input('id', array('id'=>'id', 'type' => 'hidden', 'value' => isset($orgADSettings[0]['OrgAdSetting']['id'])?$orgADSettings[0]['OrgAdSetting']['id']:''));
        echo $this->Form->input('org_id', array('id'=>'org_id', 'type' => 'hidden', 'value' =>  $orgDetail['Organization']['id']));
        ?>
        <section>
            <div class="clearfix"></div>
            <div class="row customerPortalLabel">
                <div class="col-md-6"> 

                    <div class="col-md-3">
                        <div class="labelCus">AD Domain</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio input ">
                            <?php
                                $adDomain = (isset($orgADSettings[0]['OrgAdSetting']['ad_domain'])? $orgADSettings[0]['OrgAdSetting']['ad_domain'] :'');
                                echo $this->Form->input('ad_domain', array('id'=>'ad_domain', 'type' => 'text', 'label' => false, 'value' => $adDomain));
                            ?>
                            <span class="error" style="display: none;">Please enter AD domain.</span>
                        </span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-3">
                        <div class="labelCus">Admin Username</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio input">
                            <?php
                                $adDomain = (isset($orgADSettings[0]['OrgAdSetting']['admin_username'])?$orgADSettings[0]['OrgAdSetting']['admin_username'] :'');
                                echo $this->Form->input('admin_username', array('id'=>'admin_username','type' => 'text', 'label' => false, 'value' => $adDomain));
                            ?>
                            <span class="error" style="display: none;">Please enter AD Admin.</span>
                        </span> 
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="col-md-6"> 

                    <div class="col-md-3">
                        <div class="labelCus">Base DN</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio input">
                            <?php
                                $adDomain = (isset($orgADSettings[0]['OrgAdSetting']['base_dn'])?$orgADSettings[0]['OrgAdSetting']['base_dn'] :'');
                                echo $this->Form->input('base_dn', array('id'=>'base_dn','type' => 'text', 'label' => false, 'value' => $adDomain));
                            ?>
                            <span class="error" style="display: none;">Please enter AD BASE DN.</span>
                        </span> 
                    </div>
                    <div class="clearfix"></div>

                    <div class="col-md-3">
                        <div class="labelCus">Admin Password</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio input">
                            <?php
                                $adDomain = (isset($orgADSettings[0]['OrgAdSetting']['admin_password'])?$orgADSettings[0]['OrgAdSetting']['admin_password'] :'');
                                echo $this->Form->input('admin_password', array('id'=>'admin_password','type' => 'password', 'label' => false, 'value' => $adDomain));
                            ?>
                            <span class="error" style="display: none;">Please enter AD BASE DN.</span>
                        </span> 
                    </div>
                    <div class="clearfix"></div>
                </div>

            </div>
            <button id="submitAdDetails">Save</button> 
        </section>
    </div>
</section>
<?php echo $this->Form->end(); ?>

<?php if(isset($orgADSettings) && !empty($orgADSettings)){ ?>
<div class="stats mb15">
    <div class="row bor-bot">
        <h3 >Active Directory Users</h3>
        <div class="row col-md-12 ">
        </div>
    </div>
</div>
    <?php }else{ ?>
<div class="stats mb15">
    <div class="row bor-bot">
        <h3 >Please Provide Active Directory details.</h3>
        <div class="row col-md-12 ">
        </div>
    </div>
</div>
<?php }?>
<?php if(isset($orgADSettings) && !empty($orgADSettings)){ ?>
<input type="hidden" id="totalrecords" value="<?php echo $totalOrgrecords; ?>">
<input type="hidden" id="org_id" value="<?php echo $orgDetail['Organization']['id']; ?>">
<input id="pagename" value="adsettings" type="hidden">

<div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
<div id="searchendorsement">
    <div class="row UserList">
        <input type="hidden" name="totalrecords" id="totalrecords" value="<?php echo $totalOrgrecords; ?>">
        <?php 
        
        if (is_array($org_user_data) && !empty($org_user_data)) {  ?>
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
                    <?php echo $this->Element('aduserslisting', array("orgstatus" => $orgDetail['Organization']['status'], "admin_id" => $orgDetail['Organization']['admin_id'], "orguser_id" => $authUser["id"],'roleList'=>$roleList)); ?>
            </tbody>
        </table>
        <div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
        <?php }else{ ?>
        <div><p style="color: wheat;"><?php echo $serverRespone;?></p></div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<!-- Model box for Active Directory users -->
<div class="modal fade" id="myModal_adusers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content adUserModal" align="left">
            <div class="modal-header">
                <h4>Add Users</h4>
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label> Search Users :</label>
                    <select id="unAddedSelect" class="select2 form-control" multiple style="width: 100%">
                    <?php 
                    
                    if (is_array($unAddedUsersList) && !empty($unAddedUsersList)) { 
                        foreach($unAddedUsersList as $i => $uData){ 
                            $unAddedUserJson = json_encode($unAddedUsersList);
                            ?>
                        <option value="<?php echo $uData['ad_accountname'];?>"><?php echo ucfirst($uData['fname']) . ' ' . ucfirst($uData['lname']);?></option>
                        <?php }
                        ?>
                    <?php }else{
                        $unAddedUserJson = json_encode(array());
                    } ?>
                    </select>
                    <span class="error addUserError" style="display: none;">Please select users.</span>
                </div>
                <div class="form-group">
                    <input id="check" type="radio" name="userrole" value="3" checked="checked">
                    <label class="css-label" for="check">Endorser</label>

                    <input id="check1" type="radio" name="userrole" value="2">
                    <label class="css-label" for="check1">Admin</label>

                    <input id="check2" type="radio" name="userrole" value="5">
                    <label class="css-label" for="check2">Admin Elite</label>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input id="daisy_check" type="checkbox" name="is_daisy" value="3" class="css-checkbox">
                        <label class="css-label" for="daisy_check">Is DAISY?</label>
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" id="saveAddedUser" class="btn btn-success">Add User</button>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal_deactiveusers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog adUserModal" role="document">
        <div class="modal-content" align="left">
            <div class="modal-header">
                <h4>Disable AD Users</h4>
                <?php // pr($addedUsers); exit;?>
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label> Search Users :</label>
                    <select id="addedSelect" class="select2" multiple style="width: 100%">
                    <?php if (is_array($addedUsers) && !empty($addedUsers)) { 
                        foreach($addedUsers as $i => $uData){ 
                            $addedUserJson = json_encode($addedUsers);
                            ?>
                        <option value="<?php echo $uData['user']['id'];?>"><?php echo ucfirst($uData['user']['fname']) . ' ' . ucfirst($uData['user']['lname']);?></option>
                        <?php }
                        ?>
                    <?php }else{
                        $addedUserJson = json_encode(array());
                    } ?>
                    </select>
                    <span class="error removeUserError" style="display: none;">Please select users.</span>
                </div>
                <div class="form-group">
                    <button type="button" id="removeAddedUser" class="btn btn-danger">Disable User</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal_reactiveusers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog adUserModal" role="document">
        <div class="modal-content" align="left">
            <div class="modal-header">
                <h4>Enable AD Users</h4>
                <?php // pr($addedUsers); exit;?>
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label> Search Users :</label>
                    <select id="disabledSelect" class="select2" multiple style="width: 100%">
                    <?php if (is_array($disabledUsers) && !empty($disabledUsers)) { 
                        foreach($disabledUsers as $i => $uData){ 
                            
                            ?>
                        <option value="<?php echo $uData['user']['id'];?>"><?php echo ucfirst($uData['user']['fname']) . ' ' . ucfirst($uData['user']['lname']);?></option>
                        <?php }
                        ?>
                    <?php }else{
                        
                    } ?>
                    </select>
                    <span class="error disabledUserError" style="display: none;">Please select users.</span>
                </div>
                <div class="form-group">
                    <button type="button" id="enableAddedUser" class="btn btn-primary">Enable User</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // -->



<script>
    $(document).ready(function () {

        var unAddedUsersJson = '<?php echo $unAddedUserJson; ?>';
        var unAddedObj = $.parseJSON(unAddedUsersJson);
        var orgId = '<?php echo $orgId;?>';

        $('#submitAdDetails').on('click', function (e) {
            $('.error').hide();
            var error = false;
            e.preventDefault();
            $("form input").each(function () {
                if ($(this).attr('id') != 'id') {
                    var value = $.trim($(this).val());
                    if (value == '') {
                        $(this).parents('.input').find('.error').show();
                        error = true;
                    }
                }
            });

            if (!error) {
                $("#OrganizationAdsettingsForm").submit();
            }

        });

        $(".select2").select2();

        $(".select2").on('change', function () {
            $(".error").hide();
        });

        $('#enableAddedUser').on('click', function () {
            $(".error").hide();
            var oldUserData = $('#disabledSelect').val();

            if (oldUserData == null) {
                $(".disabledUserError").show();
                return false;
            }

            $.ajax({
                type: "POST",
                url: siteurl + 'organizations/enableAdUser',
                data: {userData: oldUserData, 'org_id': orgId},
                success: function (data, textStatus, xhr) {
//                                    console.log(data);
//                                    return false;
                    var jsonparser = $.parseJSON(data);

                    var status = jsonparser["success"];
                    //console.log(status); return false;
                    if (status) {
                        location.reload(true);
                    }
                },
            });


        });

        $('#removeAddedUser').on('click', function () {
            $(".error").hide();
            var removedUserData = {};
            var oldUserData = $('#addedSelect').val();

            if (oldUserData == null) {
                $(".removeUserError").show();
                return false;
            }

//            return false;
            $.confirm({
                title: false,
                content: 'Deleted User will no longer access to nDorse.',
                type: 'red',
                columnClass: 'medium',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function () {
                            $.ajax({
                                type: "POST",
                                url: siteurl + 'organizations/disableAdUser',
                                data: {userData: oldUserData, 'org_id': orgId},
                                success: function (data, textStatus, xhr) {
//                                    console.log(data);
//                                    return false;
                                    var jsonparser = $.parseJSON(data);

                                    var status = jsonparser["success"];
                                    //console.log(status); return false;
                                    if (status) {
                                        location.reload(true);
                                    }
                                },
                            });
                        }
                    },
                    cancel: function () {
                    }
                }
            });
        });

        $('#saveAddedUser').on('click', function () {
            $(".error").hide();
            var newAddedUserData = {};
            var newUserData = $('#unAddedSelect').val();
            if (newUserData == null) {
                $(".addUserError").show();
                return false;
            }
            $.each(newUserData, function (index, userValue) {
                var userData = unAddedObj[userValue];
                newAddedUserData[userValue] = userData;
            });
            console.log(newAddedUserData);

            $.ajax({
                type: "POST",
                url: siteurl + 'organizations/addNewADUser',
                data: {userData: newAddedUserData, 'org_id': orgId},
                success: function (data, textStatus, xhr) {
                    console.log(data);
//                    return false;
                    var jsonparser = $.parseJSON(data);
                    //console.log(jsonparser);
                    var status = jsonparser["success"];
                    //console.log(status); return false;
                    if (status) {
                        location.reload(true);
                    }
                },
            });

        });

    });

</script>