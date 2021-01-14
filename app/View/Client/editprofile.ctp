<script>var countrydata = <?php echo json_encode($statearray) ?>;
</script>
<?php
/* print_r($this->request->data["User"]);
  exit; */
$dobdate = "";
if (isset($this->request->data["User"]["dob"]) && $this->request->data["User"]["dob"] != "") {

    $dobdate = str_replace("-", "/", $this->request->data["User"]["dob"]);
    $dobdate = explode("/", $dobdate);
    $dobdate = mktime(0, 0, 0, $dobdate[0], $dobdate[1], $dobdate[2]);
    $dobdate = date("m-d-Y", $dobdate);
}
$fname = "";
if (isset($this->request->data["User"]["fname"])) {
    $fname = $this->request->data["User"]["fname"];
}
$lname = "";
if (isset($this->request->data["User"]["lname"])) {
    $lname = $this->request->data["User"]["lname"];
}
$street = "";
if (isset($this->request->data["User"]["street"])) {
    $street = $this->request->data["User"]["street"];
}
$city = "";
if (isset($this->request->data["User"]["city"])) {
    $city = $this->request->data["User"]["city"];
}
$zip = "";
if (isset($this->request->data["User"]["zip"])) {
    $zip = $this->request->data["User"]["zip"];
}
$about = "";
if (isset($this->request->data["User"]["about"])) {
    $about = $this->request->data["User"]["about"];
}
$mobile = "";
if (isset($this->request->data["User"]["mobile"])) {
    $mobile = $this->request->data["User"]["mobile"];
}
?>

<div class="my-profile col-md-12">
    <?php if ($successmsg != "") { ?>
        <div id="flashmessage" class="msg text-center col-md-12" style="margin:10px 0"><?php echo $successmsg; ?></div>
    <?php } elseif ($errormsg != "") { ?>
        <div id="flashmessage" class="error text-danger text-center col-md-12"><?php echo $errormsg; ?></div>
    <?php } ?>
    <?php echo $this->Form->create('User', array("method" => "post", "enctype" => "multipart/form-data")); ?>
    <section class="">
        <?php // echo $this->Form->create('Userphoto', array('url' => array('controller' => 'users', 'action' => 'setimage')));  ?>
        <div class="row f-center">
            <div class="col-md-5 col-sm-5 col-lg-5">
                <div class="pull-right">
                    <button class="btn btn-blue" type="button" id="user_upload_photo">Upload Picture</button>
                </div>
            </div>

            <div class="col-md-2 col-sm-2 col-lg-2 text-center">
                <?php
                $client_image = "";
                // print_r($this->request->data);
                if ($this->request->data["User"]["Userphoto"] == "") {
                    echo $this->Html->image('p_pic.png', array('width' => '115', 'height' => '115', 'id' => 'client_image', 'class' => 'img-circle'));
                } else {
                    //pr($this->request->data["User"]["Userphoto"]);
                    $user_image = explode("/", $this->request->data["User"]["Userphoto"]);
                    // print_r($user_image);
                    // echo WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1];
                    //echo "<hr>";
                    //echo file_exists(WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1]);

                    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                        $client_image = $user_image[count($user_image) - 1];
                        $user_image = Router::url('/', true) . PROFILE_IMAGE_DIR . $client_image;
                        $client_image = $user_image;
                    } elseif (isset($_SESSION["tp_profile"]["image"])) {
                        $user_image = $_SESSION["tp_profile"]["image"];
                        $client_image = $user_image;
                    } else {
                        $user_image = 'p_pic.png';
                    }

                    echo $this->Html->image($user_image, array('width' => '115', 'height' => '115', 'id' => 'client_image', 'class' => 'img-circle'));
                }
                ?>
            </div>
            <div class="col-md-5 col-sm-5 col-lg-5">
                <div class="pull-left">
                    <button class="btn btn-blue" type="button" id="user_remove_photo">Remove Picture</button>
                </div>
            </div>

        </div>

        <?php
        echo $this->Form->input('Userphoto', array(
            'type' => 'file',
            'id' => 'photo',
            'label' => false,
            'class' => 'btn_uplaod_file hidden',
            'accept' => ".jpg,.png,.gif,.jpeg",
        ));
        ?>
        <?php //echo $this->Form->end();?>
        <center><label class="error" id="validImageError"></label></center>
    </section>

    <?php echo $this->Form->input('image', array('class' => 'form-control', 'label' => false, 'type' => 'hidden', 'id' => 'client_image_name', 'value' => $client_image)); ?>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label require">First Name</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('fname', array('placeholder' => 'First Name', 'value' => $fname, 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label require">Last Name</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('lname', array('placeholder' => 'Last Name', 'value' => $lname, 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Date of Birth</div>
            </div>
            <div class="col-md-10">
                <div class="input"> 
                  <!-- <input type="date" maxlength="250" class="form-control" placeholder="DOB">--> 
                    <?php echo $this->Form->input('dob', array('placeholder' => 'MM-DD-YYYY / Click Icon', 'value' => $dobdate, 'type' => 'text', 'id' => 'datepicker_dob', 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Hobbies</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <span style="color: white; padding-left: 7px">Note:- Ctrl + Select to multiselect Skills</span> <?php echo $this->Form->input('hobbies', array('empty' => 'Select Hobbies', 'multiple' => 'multiple', 'label' => false, 'options' => $hobbies, 'selected' => $selectedhobbies, 'class' => 'form-control')); ?>
                    <div id="other_UserHobbies" style="display:none;margin-top:5px;">
                        <div style="width:93%; float:left;"><?php echo $this->Form->input('other_hobbies', array('class' => 'form-control', 'label' => false)); ?></div>
                        <div style="width:7%; float:left;" class="Add"  onclick="add('Hobbies', 'Hobbies')" style="background-color: white">
                            <?php echo $this->Html->image('addCoreValue.png', array('class' => 'img-responsive')); ?> </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Skills</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <span style="color: white; padding-left: 7px">Note:- Ctrl + Select to multiselect Hobbies</span> <?php echo $this->Form->input('skills', array('empty' => 'Select Skills', 'multiple' => 'multiple', 'label' => false, 'options' => $skill, 'selected' => $selectedskills, 'class' => 'form-control')); ?> </div>
                <div id="other_UserSkills" style="display:none; margin-top:5px;">
                    <div style="width:93%; float:left; "><?php echo $this->Form->input('other_skills', array('class' => 'form-control', 'label' => false)); ?></div>
                    <div style="width:7%; float:left;" class="Add"  onclick="add('Skills', 'Skills')" style="background-color: white">
                        <?php echo $this->Html->image('addCoreValue.png', array('class' => 'img-responsive')); ?> </div>
                </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Street</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('street', array('placeholder' => 'Street Address', 'value' => $street, 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label ">City</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('city', array('placeholder' => 'City', 'value' => $city, 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label ">Zip</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('zip', array('placeholder' => 'Zip', 'value' => $zip, 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <?php
    $stylestatelist = "style=display:block;";
    $stylestatetextlist = "style=display:none;";
    if (empty($statearray[$default_country])) {
        $stylestatelist = "style=display:block;";
        $stylestatetextlist = "style=display:none;";
    }
    if (isset($this->request->data["User"]["country"]) && $this->request->data["User"]["country"] != "") {
        $default_country = $this->request->data["User"]["country"];
    }
    ?>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label ">Country</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('country', array('empty' => 'Select Country', 'label' => false, 'options' => $countryarray, 'selected' => $default_country, 'class' => 'form-control')); ?> </div>
            </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label ">State</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('state', array('empty' => 'Select State', 'label' => false, 'options' => $statearray[$default_country], 'class' => 'form-control')); ?> </div>
            </div>
            <div id="selectstatetext" <?php echo $stylestatetextlist; ?> > <?php echo $this->Form->input('state_name', array('type' => 'text', 'class' => 'textbox', 'id' => 'state_name', 'label' => false)); ?> </div>
    </section>
    <?php //echo $this->Form->input('country', array('empty' => 'Select Country','multiple' => 'multiple', 'label' => false, 'options' => $countryarray, 'selected' => $default_country, 'class' => 'form-control'));  ?>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label require">Phone</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('mobile', array('placeholder' => 'Enter Your Phone Here...', 'value' => $mobile, 'class' => 'form-control', 'label' => false,)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">About</div>
            </div>
            <div class="col-md-10">
                <div class="input "> <?php echo $this->Form->input('about', array('placeholder' => 'About', 'value' => $about, 'class' => 'my-pro-textarea', 'label' => false, 'type' => 'textarea')); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">&nbsp;</div>
            </div>
            <div class="col-md-10">
                <div class="input ">
                    <button type="button" class="btn btn-orange" id="userprofilesubmit" >Submit</button>
                </div>
            </div>
        </div>
    </section>
    <?php echo $this->Form->end(); ?>
</div>