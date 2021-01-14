<?php
$data = array(
    "textcenter" => "Create User",
    "righttabs" => "1"
);
echo $this->Element('header', array('data' => $data));
?>


<section>
    <div id="img_msg" ></div>
    <?php echo $this->Form->create('Userphoto', array('url' => array('controller' => 'users', 'action' => 'setimage'))); ?>
    <input type="hidden" name="endorserid"  value="<?php echo $nextendorserid; ?>" />
    <div class="row signup">
        <div class="col-md-5 col-sm-5 col-lg-5">
            <div class="pull-right">
                <button type="button" id="client_upload_photo" class="btn btn-blue">Upload Picture</button>
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-lg-2 text-center">
            <?php
            if ($client_image == "") {
                echo $this->Html->image('p_pic.png', array('width' => '115', 'id' => 'client_image'));
            } else {
                $client_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $client_image;
                echo $this->Html->image($client_imagenew, array('width' => '115', 'id' => 'client_image'));
            }
            ?>
        </div>
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
<?php //if ($errormsg!= ''){ ?>
<!--        <div class="error-createclient"><?php //echo $errormsg;     ?></div>-->
<?php
//} 
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
                            <input type="text" id="inputEmail" value="<?php echo $nextendorserid; ?>" class="form-control" readonly="readonly" >
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus require">Email</div>
                            <input type="hidden" id="orgidendorser" value="<?php echo $organization_id; ?>">
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->input('email', array('placeholder' => 'Email', 'class' => 'form-control', 'label' => false)); ?>
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
                <div class="row" id="fnamelname">
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
                <div class="row" id="dobmobile">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">DOB</div>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->input('dob', array('placeholder' => 'MM-DD-YYYY', 'readonly', 'type' => 'text', 'id' => 'datepicker_dob', 'class' => 'form-control', 'label' => false)); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Mobile</div>
                        </div>
                        <div class="col-md-5">
                            <?php echo $this->Form->input('mobile', array('placeholder' => 'Mobile', 'class' => 'form-control txt-decimal', 'label' => false)); ?>
                        </div>
                        <div class="col-md-5"> 
                            <div class="checkbox">

                                <?php echo $this->Form->checkbox('mobile_visible', array('hiddenField' => false, 'class' => 'css-checkbox')); ?>
                                <label for="UserMobileVisible" name="" class="css-label" style="color:#fff; font-size:12px">Visible to other seeing profile</label>

                            </div>
                        </div>
                        <!--<div class="col-md-5"> 
                            <span class="checkbox">
                                <label for="" name="">
                        <?php //echo $this->Form->checkbox('mobile_visible', array('hiddenField' => false));   ?>
                                    Visible to other seeing profile</label>
                            </span>
                        </div> -->
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6" id="role">
                        <div class="col-md-2">
                            <div class="labelCus">Role</div>
                        </div>
                        <div class="col-md-10">
                            <div class = "select-style">
                                <?php
                                $role = array("3" => "nDorser", "2" => DESIGNATEDADMIN);
                                echo $this->Form->input('role', array('empty' => 'Select Role', 'label' => false, 'options' => $role, 'class' => 'form-control', "selected" => 3));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Job Title</div>
                        </div>
                        <div class="col-md-10">
                            <div class = "select-style">
                                <?php echo $this->Form->input('job_title', array('empty' => 'Select Job Title', 'label' => false, 'options' => $jobtitles, 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Department</div>
                        </div>
                        <div class="col-md-10">
                            <div class = "select-style">
                                <?php echo $this->Form->input('department', array('empty' => 'Select Department', 'label' => false, 'options' => $departments, 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Sub Organization</div>
                        </div>
                        <div class="col-md-10">
                            <div class = "select-style">
                                <?php echo $this->Form->input('entity', array('empty' => 'Select Sub Organization', 'label' => false, 'options' => $entities, 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row" id="skillshobbies">

                    <div class="col-md-6">
                        <span style="color: white; padding-left: 7px">Note:- Ctrl + Select to multiselect Skills</span>
                        <div class="col-md-2">
                            <div class="labelCus">Skills</div>
                        </div>
                        <div class="col-md-10">
                            <?php
                            echo $this->Form->input('skills', array('empty' => 'Select Skills', 'multiple' => 'multiple', 'label' => false, 'options' => $skills, 'class' => 'form-control'));
                            $otherskillsdisplay = "none";
                            ?>
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
                            <?php echo $this->Form->input('hobbies', array('empty' => 'Select Hobbies', 'multiple' => 'multiple', 'label' => false, 'options' => $hobbies, 'class' => 'form-control')); ?>
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


                </div>
            </section>
            <section>
                <div class="row" >
                    <div id="about" class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">About</div>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->input('about', array('placeholder' => 'About', 'class' => 'form-control', 'label' => false, 'type' => 'textarea')); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus" >Status</div>
                        </div>
                        <div class="col-md-10"> <span class="radio">
                                <?php
                                $options = array(
                                    '0' => 'Inactive',
                                    '1' => 'Active',
                                    '3' => 'Evaluation',
                                );
                                echo $this->Form->input('status', array('type' => 'radio',
                                    'separator' => '</div><div class="col-md-3">',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => 1,
                                    'class' => 'js_userStatusRadio'
                                        )
                                );
                                ?>
                            </span> </div>

                    </div>
                    <div class="col-md-6" id="sendinvitations">
                        <div class="col-md-2">
                            <div class="labelCus" >Send Invitation</div>
                        </div>
                        <div class="col-md-10"> <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('invitation', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => 1,
                                    'class' => 'js_inviteRadio'
                                        )
                                );
                                ?>
                            </span> </div>

                    </div>
                </div>
            </section>
            <section>
                <div class="row" id="countrystate">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Country</div>
                        </div>
                        <div class="col-md-10">
                            <div class = "select-style">
                                <?php echo $this->Form->input('country', array('empty' => 'Select Country', 'selected' => $country_code, 'class' => 'form-control textbox country', 'label' => false, 'options' => $listCountries, 'data-url' => Router::url(array('controller' => 'ajax', 'action' => 'states')))); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus ">State</div>
                        </div>
                        <div class="col-md-10">
                            <?php
                            $stylestatelist = "style=display:block;";
                            $stylestatetextlist = "style=display:none;";
                            if (empty($listState)) {
                                $stylestatelist = "style=display:block;";
                                $stylestatetextlist = "style=display:none;";
                            }
                            ?>
                            <div class="select-style" id="selectstate"<?php echo $stylestatelist; ?> >
                                <?php echo $this->Form->input('state', array('empty' => 'Select State', 'label' => false, 'options' => $listState, 'class' => 'form-control textbox states')); ?>
                            </div>
                            <div id="selectstatetext" <?php echo $stylestatetextlist; ?> >
                                <?php echo $this->Form->input('state_name', array('type' => 'text', 'class' => 'textbox', 'id' => 'state_name', 'label' => false)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--ends here-->
            <section>
                <div class="row" id="citystreet">
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
                <div class="row" id="zip">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Zip code</div>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->input('zip', array('placeholder' => 'Zip', 'class' => 'form-control txt-decimal', 'label' => false, 'type' => 'text')); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus">Phone</div>
                        </div>
                        <div class="col-md-10">
                            <div class="input"> <?php echo $this->Form->input('mobile', array('placeholder' => 'Enter Your Phone Here...', 'class' => 'form-control', 'label' => false,)); ?> </div>
                        </div>
                    </div>
                </div>    
            </section>
            <!--
                            <section>
                                <div class="row" id="passwordcpassword">
                                    <div class="col-md-6">
                                        <div class="col-md-2">
                                            <div class="labelCus">Password</div>
                                        </div>
                                        <div class="col-md-10">
            <?php //echo $this->Form->input('password', array('placeholder' => 'Password', 'class' => 'form-control', 'label' => false,'type'=>'password'));   ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-2">
                                            <div class="labelCus">Confirm Password</div>
                                        </div>
                                        <div class="col-md-10">
            <?php //echo $this->Form->input('confirm_password', array('placeholder' => 'Confirm Password', 'class' => 'form-control', 'label' => false,'type'=>'password'));   ?>
                                        </div>
                                    </div>
                                </div>
                            </section>-->
        </form>
    </div>
</section>

<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="clientformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="endorserformsubmit">Save</button>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</section>