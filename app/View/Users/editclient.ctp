<?php
$data = array(
    "textcenter" => "Organization Info",
    "righttabs" => "1",
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';

if ($authUser["role"] == 2) {
    $data['auth_users'] = $authUser;
}

echo $this->Element($headerpage, array('data' => $data));
$checkarray = array("state");
foreach ($checkarray as $fieldvalue) {
    $$fieldvalue = "";
    if (!empty($userdata['User'][$fieldvalue])) {
        $$fieldvalue = $userdata['User'][$fieldvalue];
    }
}
?>

<div class="container">
    <section>
        <div id="img_msg" ></div>
        <?php echo $this->Form->create('Userphoto', array('url' => array('controller' => 'users', 'action' => 'setimage'))); ?>
        <input type="hidden" name="clientid"  value="<?php echo $userdata['User']['id']; ?>" />
        <div class="row signup">
            <div class="col-md-5 col-sm-5 col-lg-5">
                <div class="pull-right">
                    <button type="button" id="client_upload_photo" class="btn btn-blue">Upload Picture</button>
                </div>
            </div>
            <div class="col-md-2 col-sm-2 col-lg-2 text-center"><?php
                if ($client_image == "") {
                    echo $this->Html->image('p_pic.png', array('width' => '115', 'id' => 'client_image'));
                } else {
                    $client_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $client_image;
                    echo $this->Html->image($client_imagenew, array('width' => '115', 'id' => 'client_image'));
                }
                ?></div>
            <div class="col-md-5 col-sm-5 col-lg-5">
                <div class="pull-left">
                    <button type="button" class="btn btn-blue" id="client_remove_photo">Remove Picture</button>
                </div>
            </div>
        </div>
        <?php
        echo $this->Form->input('Userphoto', array(
            'type' => 'file',
            'id' => 'photo',
            'label' => false,
            'class' => 'btn_uplaod_file hidden'
        ));
        ?>
        <?php echo $this->Form->end(); ?>
    </section>
    <!--Used to display validation errors-->
    <?php if ($errormsg != '') { ?>
        <div class="error-createclient"><?php //echo $errormsg;      ?></div>
        <?php
    }
    echo $this->Form->create('User');
    ?>
    <section>
        <?php echo $this->Form->input('image', array('class' => 'form-control', 'label' => false, 'type' => 'hidden', 'id' => 'client_image_name', 'value' => $client_image)); ?>
        <div class="row">
            <form class="form-horizontal">
                <section>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-1">
                                <div class="labelCus">ID</div>
                            </div>
                            <div class="col-md-11">
                                <input type="text" id="inputEmail" value="<?php
                                echo $userdata['User']['id'];
                                ;
                                ?>" class="form-control" readonly="readonly" >
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">Email</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('email', array('placeholder' => 'Email', 'class' => 'form-control', 'label' => false, "readonly" => "readonly")); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Username</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('username', array('placeholder' => 'Username', 'class' => 'form-control', 'readonly' => 'readonly', 'label' => false)); ?>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">First Name</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('fname', array('placeholder' => 'First Name', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">Last Name</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('lname', array('placeholder' => 'Last Name', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">DOB</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('dob', array('placeholder' => 'MM-DD-YYYY / Click Icon', 'type' => 'text', 'id' => 'datepicker_dob', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Mobile</div>
                            </div>
                            <div class="col-md-5">
                                <?php echo $this->Form->input('mobile', array('placeholder' => 'Mobile No.', 'class' => 'form-control txt-decimal', 'label' => false)); ?>
                            </div>
                            <div class="col-md-5"> 
                                <div class="checkbox">

                                    <?php echo $this->Form->checkbox('mobile_visible', array('hiddenField' => false, 'class' => 'css-checkbox')); ?>
                                    <label for="UserMobileVisible" name="" class="css-label" style="color:#fff; font-size:12px">Visible to other seeing profile</label>

                                </div>
                            </div>

                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">Role</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('role', array('type' => 'textbox', 'label' => false, 'class' => 'form-control', 'readonly')); ?>
                            </div>
                        </div>
                        <?php /* ?>
                          <div class="col-md-6">
                          <div class="col-md-2">
                          <div class="labelCus">Deactivated</div>
                          </div>

                          <!--new-->
                          <div class="col-md-10"> <span class="radio">
                          <?php
                          $options = array(
                          '0' => 'Yes',
                          '1' => 'No'
                          );
                          echo $this->Form->input('status', array('type' => 'radio',
                          'separator'=> '</div><div>',
                          'before' => '<div class="col-md-3">',
                          'after' => '</div>',
                          'options' =>  $options,
                          'label' => true,
                          'legend' => false
                          )
                          );
                          ?>
                          </span>
                          </div>
                          </div>
                          <?php */ ?>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <span style="color: white; padding-left: 7px">Note:- Ctrl + Select to multiselect Skills</span>
                            <div class="col-md-2">
                                <div class="labelCus">Skills</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('skills', array('empty' => 'Select Skills', 'multiple' => 'multiple', 'label' => false, 'options' => $skill, 'selected' => $selectedskills, 'class' => 'form-control')); ?>
                                <div id="other_UserSkills" style="display:none">
                                    <div style="width:93%; float:left;"><?php echo $this->Form->input('other_skills', array('class' => 'form-control', 'label' => false)); ?></div>
                                    <div style="width:7%; float:left;" class="Add"  onclick="add('Skills', 'Skills')" style="background-color: white">
                                        <?php echo $this->Html->image('addCoreValue.png', array('class' => 'img-responsive')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span style="color: white; padding-left: 7px">Note:- Ctrl + Select to multiselect Hobbies</span>
                            <div class="col-md-2">
                                <div class="labelCus">Hobbies</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('hobbies', array('empty' => 'Select Hobbies', 'multiple' => 'multiple', 'label' => false, 'options' => $hobbies, 'selected' => $selectedhobbies, 'class' => 'form-control')); ?>
                                <div id="other_UserHobbies" style="display:none;">
                                    <div style="width:93%; float:left;"><?php echo $this->Form->input('other_hobbies', array('class' => 'form-control', 'label' => false)); ?></div>
                                    <div style="width:7%; float:left;" class="Add"  onclick="add('Hobbies', 'Hobbies')" style="background-color: white">
                                        <?php echo $this->Html->image('addCoreValue.png', array('class' => 'img-responsive')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">About</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('about', array('placeholder' => 'About', 'class' => 'form-control', 'label' => false, 'type' => 'textarea')); ?>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Country</div>
                            </div>
                            <div class="col-md-10">
                                <div class = "select-style">
                                    <?php echo $this->Form->input('country', array('empty' => 'Select Country', 'class' => 'form-control textbox country', 'label' => false, 'options' => $listCountries, 'selected' => $this->request->data['User']['country'], 'data-url' => Router::url(array('controller' => 'ajax', 'action' => 'states')))); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">State</div>
                            </div>
                            <div class="col-md-10">
                                <?php
                                $stylestatelist = "style=display:block;";
                                $stylestatetextlist = "style=display:none;";
                                if (empty($listState)) {
                                    $stylestatelist = "style=display:none;";
                                    $stylestatetextlist = "style=display:block;";
                                }
                                ?>
                                <div id="selectstate" class="select-style" <?php echo $stylestatelist; ?> >
                                    <?php echo $this->Form->input('state', array('empty' => 'Select State', 'label' => false, 'options' => $listState, 'selected' => $this->request->data['User']['state'], 'class' => 'form-control textbox states')); ?>
                                </div>
                                <div id="selectstatetext" <?php echo $stylestatetextlist; ?> >
                                    <?php echo $this->Form->input('state_name', array('type' => 'text', 'value' => $state, 'class' => 'textbox', 'id' => 'state_name', 'label' => false)); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--ends here-->

                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">City</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('city', array('placeholder' => 'City', 'class' => 'form-control', 'label' => false, 'type' => 'text')); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Street</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('street', array('placeholder' => 'Street', 'class' => 'form-control', 'label' => false, 'type' => 'text')); ?>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Zip code</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('zip', array('placeholder' => 'Zip', 'class' => 'form-control txt-decimal', 'label' => false, 'type' => 'text')); ?>
                            </div>
                        </div>
                    </div>    
                </section>
                <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus" style="padding-top:23px;">Password</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('changepassword', array('placeholder' => 'Password', 'class' => 'form-control', 'label' => false, 'type' => 'password', 'value' => '')); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus" style="padding-top:15px;">Confirm Password</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('changepassword_confirm_password', array('placeholder' => 'Confirm Password', 'class' => 'form-control', 'label' => false, 'type' => 'password', 'value' => '')); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>
<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="clientformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="editclientformsubmit">Save</button>

                <?php //echo $this->Form->reset(__('Reset'), array('class' => 'btn_reset btn btn-default','id'=>'clientformcencel'));   ?>  
                <?php //echo $this->Form->submit(__('Submit'), array('class' => 'btn_submit btn btn-default','id'=>'clientformsubmit'));  ?>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</section>