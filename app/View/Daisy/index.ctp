<?php
$path = Router::url('/', true);
$path = str_replace("http", "https", $path);
$this->assign('title', 'DAISY');
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" />
<div class="bg">
    <?php echo $this->Html->Image("/images/daisy-bg.JPG", array("class" => "img-responsive", "alt" => "")); ?>
</div>
<?php
$cpLogo = isset($orgDetail['Organization']['cp_logo']) ? $orgDetail['Organization']['cp_logo'] : $orgDetail['Organization']['image'];
?>
<div class="daisy-guest">
    <section class="guest-bg">
        <div class="guest-header">
            <div class="main-width-removed">
                <h3>Welcome To 
                    <br> 
                    <span>
                        The DAISY Award<span style="font-size:145%">®️️</span> For Extraordinary Nurses Nomination Portal
                    </span>
                    <!-- <span class="comp-name">
                    <?php //echo $orgDetail['Organization']['name'];  ?>
                    </span>  -->
                    <br> 
                </h3>
                <h2>
                    <?php echo $orgDetail['Organization']['name']; ?>
                </h2>
            </div>
        </div>
        <div class="daisy-txt">
            <div class="container">
                <div class="text-center mb20" >
                    <?php echo $this->Html->Image("/images/daisy-shdow.png", array("alt" => "", "width" => "180")); ?>
                </div>
                <div class="col-md-12">
<!--                    <p>The DAISY Award© for Extraordinary Nurses is a national program that honors the compassionate care and clinical excellence our nurses bring to their patients every day. Please tell us about yourself, so that we may include you in the celebration of this award should the nurse you nominated is chosen, then click “NEXT” to proceed with the nomination. Help us celebrate the compassion and skill of our extraordinary nurses. Nominate a nurse today!</p>-->
                        <!--<p>The DAISY Award® for Extraordinary Nurses is an international program that honors the compassionate care and clinical excellence our nurses bring to their patients every day. Please tell us about yourself, so that we may include you in the celebration of this award should the nurse you nominated is chosen, then click "NEXT" to proceed with the nomination. Help us celebrate the compassion and skill of our extraordinary nurses. Say Thank You to your nurse today!</p>-->
                    <p>The DAISY Award® for Extraordinary Nurses is an international program that honors the compassionate care and clinical excellence our nurses bring to their patients every day. Please tell us about yourself so we may invite you to the celebration of your nurse should she or he be selected for The DAISY Award. Then click “NEXT” to proceed. Help us celebrate the compassion and skill of our extraordinary nurses. Say Thank You to your nurse today!</p>
                </div>
            </div>
        </div>
        <?php
        echo $this->Form->create('endorse', array('url' => array('controller' => 'daisy', 'action' => 'endorse', 'id' => $encryptID), 'type' => 'post', 'id' => 'guestfeedback'));
        ?>
        <div class="guest-content text-center">
            <div class="main-width">
                <div class="comp-logo">
                    <?php // echo $this->Html->Image("/images/comp-logo.png", array("class" => "img-responsive", "alt" => "")); ?>
                    <?php
//                    $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $cpLogo;
//                    $org_imagenew = str_replace("http", "https", $org_imagenew);
//                    if (isset($cpLogo) && $cpLogo != '') {
//                        echo $this->Html->Image($org_imagenew, array("class" => "img-responsive", "alt" => ""));
//                    } else {
//                        echo $this->Html->image('comp_pic.png', array("class" => "img-responsive", "alt" => ""));
//                    }
                    ?>
                </div>

                <div class="form-group"> 
                    <div class="input">
                        <input placeholder="Your First Name*" errDiv="fnameErr" onkeyup="setDefultBg(this);" name="fname" class="form-control fname" maxlength="60" type="text">
                        <p class="fnameErr err" style="color: red; display: none;"><i>Please enter First Name.</i></p>
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="input">
                        <input  placeholder="Your Last Name*" errDiv="lnameErr" onkeyup="setDefultBg(this);" name="lname" class="form-control lname" maxlength="60" type="text">
                        <p class="lnameErr err" style="color: red; display: none;"><i>Please enter Last Number.</i></p>
                    </div>
                </div>

                <div class="form-group">
                    <p>We would like to contact you about your nomination and experience with The DAISY Award. If your nominee is chosen, we hope you will join us for the celebration of your very special nurse. If you agree, please enter your email and/or phone number below.</p>
                </div>


                <div class="form-group"> 
                    <div class="input">
                        <input  placeholder="Email Address" errDiv="emailErr" onkeyup="setDefultBg(this);" name="email" class="form-control email" maxlength="150" type="text">
                        <p class="emailErr err" style="color: red; display: none;"><i>Please enter Email.</i></p>
                    </div>
                </div>
                <div class="form-group"> 
                    <div class="input">
                        <input  placeholder="Contact/Mobile Number" errDiv="mobile_noErr" onkeyup="setDefultBg(this);" name="mobile" class="form-control mobile" maxlength="60" type="text">
                        <p class="mobile_noErr err" style="color: red;display: none;"><i>Please enter Mobile Number.</i></p>
                    </div>
                </div>
                <div class="form-group"> 

                    <label style="margin-top: 0px;">I am a...</label>
                    <select class="form-control" name="nominator_title">
                        <option value="Patient">Patient</option>
                        <option value="Family Member">Family Member</option>
                        <option value="Visitor">Visitor</option>
                        <option value="Nurse">Nurse</option>
                        <option value="Staff Member">Staff Member</option>
                        <option value="Physician">Physician</option>
                        <option value="Volunteer">Volunteer</option>
                    </select>
                    <p class="mobile_noErr err" style="color: red;display: none;"><i>Please enter Mobile Number.</i></p>
                </div>

                <div class="form-group ">
                    <button class="btn guest-btn btn-block" type="button" id="signupValidation">Next</button>
                </div>

            </div>
        </div>
        <?php echo $this->Form->end(); ?>
        <div class="guest-footer text-center">
            <div class="powered-by">
                <?php echo $this->Html->Image("/images/powered-by.png", array("class" => "img-responsive", "alt" => "")); ?>
            </div>
        </div>
    </section>
</div>
<div class="MT30"></div>
<script>
    $(document).ready(function () {
        $('#signupValidation').on("click", function () {
            var fname = $(".fname").val();
            var lname = $(".lname").val();
            var mobile_no = $(".mobile").val();
            var email = $(".email").val();
            $(".form-control").removeClass('guest-portal-error');
            var errorFlag = false;
            $(".err").hide();

            if (fname == '') {
                $(".fname").addClass('guest-portal-error');
                $(".fnameErr").slideDown('slow');
                $(".fnameErr").html('Please enter first name.');
                errorFlag = true;
            }
            if (lname == '') {
                $(".lname").addClass('guest-portal-error');
                $(".lnameErr").slideDown('slow');
                $(".lnameErr").html('Please enter last name.');
                errorFlag = true;
            }

//            if (mobile_no.length < 10) {
//                $(".mobile").addClass('guest-portal-error');
//                $(".mobile_noErr").slideDown('slow');
//                $(".mobile_noErr").html('Please enter valid mobile number.');
//                errorFlag = true;
//            } else {
//                var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
//                if (filter.test(mobile_no)) {
//                    $(".mobile").css('border-color', '');
//                } else {
//                    $(".mobile_noErr").slideDown('slow');
//                    $(".mobile_noErr").html('Please enter valid mobile number.');
//                    errorFlag = true;
//                }
//            }

            var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
            if (email != '') {
                if (!pattern.test(email)) {
                    $(".email").addClass('guest-portal-error');
                    $(".emailErr").html('Please enter valid email address');
                    $(".emailErr").slideDown('slow');
                    errorFlag = true;
                }
            }

            if (!errorFlag) {
                $("#guestfeedback").submit();
            }




        });
    });
    function setDefultBg(obj) {
        $(obj).removeClass('guest-portal-error');
        var errDiv = $(obj).attr('errDiv');
        $("." + errDiv).css('display', 'none');

    }
</script>