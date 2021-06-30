<?php
$path = Router::url('/', true);
$path = str_replace("http", "https", $path);
$this->assign('title', 'DAISY');
?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" />
<!-- <link rel="stylesheet" type="text/css" href="https://ndorse.net/css/style.css"/> -->

<div class="bg">
    <?php echo $this->Html->Image("/images/daisy-bg.JPG", array("class" => "img-responsive", "alt" => "")); ?>
</div>
<style type="text/css"> 
    #about {
        height: 100px;
        width: 300px;
        border: 1px solid gray;
    }

    #console {
        margin-top: 20px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
//        $(".nano").nanoScroller();
    });
</script> 
<div class="daisy-guest">
    <section class="guest-bg">
        <div class="guest-header">
            <div class="main-width">
                <h3><span class="comp-name">
                        <?php
                        //  exit;
//                        pr($orgDetail);
                        echo $orgDetail['Organization']['name'];
                        ?>
                    </span>
                </h3>
                <!--<h2>DAISY Award Portal</h2>-->
                <h2>Say Thank You to Your Nurse:<br> DAISY Award Nomination Form</h2>
            </div>
        </div>
        <div class="guest-content text-center">
            <div class="temp content-tab">
                <?php
                echo $this->Form->create('endorse', array('type' => 'post', 'url' => array('controller' => 'daisy', 'action' => 'thanks', 'id' => $encryptID), 'id' => 'guestfeedback2'));

                echo $this->Form->input('org_id', array('name' => 'org_id', 'value' => $orgId, 'id' => 'org_id', 'type' => 'hidden'));
                echo $this->Form->input('source', array('type' => 'hidden', 'value' => 'daisy_portal', 'name' => 'source'));
                echo $this->Form->input('selected_endorse_id', array('name' => 'selected_endorse_id', 'value' => "", 'id' => 'selected_endorse_id', 'type' => 'hidden'));
                echo $this->Form->input('selected_endorse_name', array('name' => 'selected_endorse_name', 'value' => "", 'id' => 'selected_endorse_name', 'type' => 'hidden'));
                echo $this->Form->input('selected_endorse_type', array('name' => 'selected_endorse_type', 'value' => "", 'id' => 'selected_endorse_type', 'type' => 'hidden'));
                echo $this->Form->input('selected_dept_id', array('name' => 'selected_dept_id', 'value' => "", 'id' => 'selected_dept_id', 'type' => 'hidden'));
                ?>
                <div class="form-group main-width" id="nDorse-search-data" style="z-index: 3;">
                    <div class="col-md-12">
                        <label>Name of nurse Whom you'd like to give thanks:</label>
                        <div class="autoSerchInput">
                            <div class="" style="margin-top:10px;" id="fistname-div">
                                <?php
                                echo $this->Form->input('firstname', array('id' => 'daisySearchKeyGuest', 'placeholder' => "First Name*",
                                    'class' => "form-control", 'label' => false, "onkeyup" => "setDefultBg(this);", "errDiv" => "firstname_err", 'value' => ""));
                                ?>
                                <p class="firstname_err err" id="firstname_err" style="color: red; display: none;">Please enter first name.</p>
                            </div>
                        </div>

                        <div class="selected-values hidden">
                            <div class="col-md-11" id="selectedValues"></div>

                            <div class="col-md-1 pull-right">
                                <button class="btn btn-clear-all js_clearAll" type="button">Clear All</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="" style="margin-top:10px;">
                            <?php
                            echo $this->Form->input('lastName', array('id' => 'endorseLastName', 'placeholder' => "Last name",
                                'class' => "form-control", 'label' => false, "onkeyup" => "setDefultBg(this);", 'value' => ""));
                            ?>
                        </div>
                        <div class="autoSerchInput">
                            <div class="" style="margin-top:10px;" id="dept-div">
                                <?php
                                echo $this->Form->input('department', array('id' => 'endorseDepartmentName', 'placeholder' => "Department or Unit",
                                    'class' => "form-control", 'label' => false, "onkeyup" => "setDefultBg(this);", 'value' => ""));
                                ?>
                            </div>
                        </div>
                        <?php
                        if ($orgDetail['Organization']['daisy_subcenters'] == 1 && !empty($DaisySubcenters)) {
                            ?>
                            <div class=""> 
                                <label style="margin-top: 0px;">Facility</label>
                                <select class="form-control" name="nominee_subcenter">
                                    <option value="">--Select Facility--</option>
                                    <option value="">Facility Unknown</option>
                                    <?php
                                    foreach ($DaisySubcenters as $index => $dSubcenters) {
                                        // pr($dSubcenters['DaisySubcenter']);
                                        ?>
                                        <option value="<?php echo $dSubcenters['DaisySubcenter']['id']; ?>"><?php echo $dSubcenters['DaisySubcenter']['name']; ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <p class="mobile_noErr err" style="color: red;display: none;"><i>Please enter Mobile Number.</i></p>
                            </div>
                        <?php } ?>
                    </div>

                    <!--                <div class="col-md-7">
                                        <label>Name of the nurse you are nominating:</label>
                                        <label>Name of the nurse whom you'd like to give thanks:</label>
                                        <label>Name of nurse you are nominating:</label>
                                        <div class="search-icn" style="margin-top:10px;">
                    <?php
// echo $this->Form->input('searchKey', array('id' => 'daisySearchKeyGuest', 'placeholder' => "Search for Nurse",
//  'class' => "form-control", 'label' => false, "onkeyup" => "setDefultBg(this);", "errDiv" => "endorse_user_err", 'value' => ""));
                    ?>
                                            <p class="endorse_user_err err" id="endorse_user_err" style="color: red; display: none;">Please select member/department.</p>
                                            <div class="selected-values hidden">
                                                <div class="col-md-11" id="selectedValues"></div>
                    
                                                <div class="col-md-1 pull-right">
                                                    <button class="btn btn-clear-all js_clearAll" type="button">Clear All</button>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                    
                                        <div class="selected-user">
                                            <h6>Name of user/dept </h6>
                                            <span class="">
                    <?php //echo $this->Html->Image("/images/close.png", array("width" => "30px", "alt" => "", "name" => 'select_user_image', "id" => "selectedUserImage"));     ?>
                                            </span>
                                        </div>
                    
                                    </div>-->

                    <!--                <div class="col-md-5 selected-pic">
                                        <div class="text-center">
                    <?php // echo $this->Html->Image("/images/user.png", array("id" => "selected_endorse_image", "name" => 'selected_endorse_image', "alt" => ""));     ?>
                                            <h3 class="selected_endorse_designation"> Designatiion </h3>
                                            <h4 class="selected_endorse_dept"> Department </h4>
                                        </div>
                                    </div>-->
                    <div class="clearfix"></div>
                </div>
                <div class="form-group main-width">
                    <div class="checkbox noname">
                        <input type="hidden" name="default_user_id" class="css-checkbox" id="default_user_id" value="<?php echo $orgDetail['Organization']['daisy_default_user_id']; ?>">
                        <input type="checkbox" name="default_user_checked" class="css-checkbox" id="default_user_checked">
                        <label class="css-label" for="default_user_checked">
                            If you are not sure of the name of the person you are trying to recognize or nominate, please check this box
                        </label>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="form-group main-width">
                    <?php
//                pr($orgDetail['Organization']['daisy_active_awards']);
                    if (isset($orgDetail['Organization']['daisy_show_awards']) && $orgDetail['Organization']['daisy_active_awards'] != '0' && $orgDetail['Organization']['daisy_show_awards'] == 1) {
                        ?>
                        <div>
                            <label>Type of Award: </label>

                            <select class="form-control" name="award_type">
                                <?php
                                foreach ($DAISYAwards as $id => $awardName) {
                                    if (!empty($orgDetail['Organization']['daisy_active_awards']) || $orgDetail['Organization']['daisy_active_awards'] != '') {
                                        $orgDaisy_active_awards = json_decode($orgDetail['Organization']['daisy_active_awards']);
                                        if (in_array($id, $orgDaisy_active_awards)) {
                                            ?>
                                            <option value="<?php echo $id; ?>"><?php echo $awardName; ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="clearfix"></div>

                </div>

                <?php if (isset($orgDetail['Organization']['daisy_show_comment_box']) && $orgDetail['Organization']['daisy_show_comment_box'] == 1) { ?>
                    <div class="form-group main-width">
                        <div class="d-flexCenter">
                            <label class="pull-left">Share Your Story!</label>
                            <span class="character_counts pull-right">0 Character</span>
                        </div>
                        <div class="input">

                            <textarea onkeyup="setDefultBg(this);" name="message" errDiv="user_msg_err" placeholder="Please tell us specifically how this nurse made a special difference in your experience as a patient or family member." id="user_msg" class="user-msg" data-min="<?php echo $orgDetail['Organization']['daisy_message_limit']; ?>"></textarea>
                            <p class="user_msg_err err" id="user_msg_err" style="color: red; display: none;">Please enter your comment.</p>

                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($orgDetail['Organization']['cp_default_user_enabled']) && $orgDetail['Organization']['cp_default_user_enabled'] == 1) { ?>
                    <!-- <div class="form-group main-width">
                        <div class="checkbox noname">
                            <input type="hidden" name="default_user_id" class="css-checkbox" id="default_user_id" value="<?php echo $orgDetail['Organization']['cp_default_user_id']; ?>">
                            <input type="checkbox" name="default_user_checked" class="css-checkbox" id="default_user_checked">
                            <label class="css-label" for="default_user_checked">
                                If you are not sure of the name of the person you are trying to recognize or nominate, please check this box
                            </label>
                            <div class="clearfix"></div>
                        </div>
                    </div>  -->
                    <?php
                }
                if (isset($orgDetail['Organization']['daisy_show_core_values']) && $orgDetail['Organization']['daisy_show_core_values'] == 1) {
                    if (!empty($coreValues)) {
                        ?>
                        <!-- Core Values part -->
                        <div class="form-group main-width ">
                            <label>Select one or more core values</label>
                            <div class="cor-vs nano">
                                <div class="nano-content">
                                    <?php
                                    if (!empty($coreValues)) {
                                        foreach ($coreValues as $index => $coreDATA) {
                                            ?>
                                            <div class="checkbox col-md-6">
                                                <input type="checkbox" name="core_value[]" class="css-checkbox" value ="<?php echo $index; ?>"  id="core_value_<?php echo $index; ?>" autocomplete="on" >
                                                <label class="css-label" for="core_value_<?php echo $index; ?>" >
                                                    <?php echo $coreDATA['name']; ?>
                                                </label>
                                                <div class="clearfix"></div>
                                            </div>

                                            <!-- Old Code -->
                                            <!-- <div class="form-group">
                                                <input type="checkbox" name="core_value[]"  value ="<?php echo $index; ?>"  id="core_value_<?php echo $index; ?>" autocomplete="on">
                                                <div class="btn-group">
                                                    <label for="core_value_<?php echo $index; ?>" class="btn btn-default">
                                                        <span class="glyphicon glyphicon-ok"></span>
                                                        <span><?php echo $coreDATA['name']; ?></span>
                                                    </label>
                                                </div>
                                            </div> -->
                                            <!-- Old Code -->


                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- Core Values part End-->
                        <?php
                    }
                }
                ?>

                <!-- <div class="form-group main-width">
                    <label>Share Your Story!</label> <span class="character_counts">0 Character</span>
                    <div class="input">
    
                        <textarea onkeyup="setDefultBg(this);" name="message" errDiv="user_msg_err" placeholder="Enter Your Story Here..." id="user_msg" class="user-msg" data-min="<?php echo $orgDetail['Organization']['cp_message_limit']; ?>"></textarea>
                        <p class="user_msg_err err" id="user_msg_err" style="color: red; display: none;">Please enter your comment.</p>
    
                    </div>
                </div>
                -->
                <div class="form-group main-width">
                    <button class="btn guest-btn btn-block validateForm" type="button" id="">Submit</button>
                </div>
                <?php echo $this->Form->end(); ?>
            </div>
        </div>

        <div class="guest-footer text-center">
            <div class="powered-by">
                <?php echo $this->Html->Image("/images/powered-by.png", array("class" => "img-responsive", "alt" => "")); ?>
            </div>
        </div>

        <!-- <label class="css-label dont-trig">Temp </label> -->

        <!--            <div class="modal fade in" id="dont" >
                      <div class="modal-dialog" >
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Core Value </h4>
                          </div>
    
    <div class="modal-body">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-orange close-btn">Close</button>
                            <button type="button" class="btn btn-orange close-btn"> Do not remind me again</button>
                          </div>
                        </div>
                      </div>
                    </div>-->

        <div class="modal fade in" id="cpMsg" >
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><?php echo $orgDetail['Organization']['cp_disclaimer_message'] ?></h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-orange close-btn closeDisclaimerbttn">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade"></div>
    </section>
</div>
<div class="MT30"></div>
<script>
    $(document).ready(function () {

        $(".dont-trig").on("click", function () {
            $("#dont").toggleClass("show");
            $(".modal-backdrop").toggleClass("in");
        });

        $(".close-btn").on("click", function () {
            $(".modal").toggleClass("show");
            $(".modal-backdrop").toggleClass("in");
        });

        $(".closeDisclaimerbttn").on("click", function () {
//            $("#guestfeedback2").submit();
        });

        $(".character_counts").html($.trim($("#user_msg").val()).length + " Characters");

        $('#user_msg').bind('keyup', function (e) {
            $(".character_counts").html($.trim($(this).val()).length + " Characters");
        });


        $(".validateForm").on("click", function () {
            $(".user-msg").removeClass("guest-portal-error");
            $("#selected_endorse_id").removeClass("guest-portal-error");

            var userMsgVal = $.trim($(".user-msg").val());
            var userMsgMinChar = $(".user-msg").attr('data-min');

            var selected_endorse_id = $.trim($("#selected_endorse_id").val());

            var error = false;
            $(".err").hide();
            if (userMsgVal.length < 1) {
                $(".user-msg").addClass("guest-portal-error");
                $(".user-msg").css({"border": "1px solid red"});
                $(".user_msg_err").slideDown('slow');
                $(".user_msg_err").html('Please enter your comment.');
                error = true;
            } else if (userMsgVal.length < userMsgMinChar) {
                $(".user-msg").addClass("guest-portal-error");
                $(".user-msg").css({"border": "1px solid red"});
                $(".user_msg_err").slideDown('slow');
                $(".user_msg_err").html('Please enter minimum ' + userMsgMinChar + ' characters in your comment.');
                error = true;
            }

            var default_user_checked = $('#default_user_checked').is(':checked');
            var daisyFirstName = $('#daisySearchKeyGuest').val();

            if (!default_user_checked) {

                if (daisyFirstName.length < 1) {
                    $("#daisySearchKeyGuest").addClass("guest-portal-error");
                    $(".firstname_err").slideDown('slow');
                    error = true;
                }
//                if (selected_endorse_id.length < 1) {
//                    $("#endorsementSearchKeyGuest").addClass("guest-portal-error");
//                    $(".endorse_user_err").slideDown('slow');
//                    error = true;
//                }
            } else {
                $("#daisySearchKeyGuest").removeClass("guest-portal-error");
//                $("#endorsementSearchKeyGuest").removeClass("guest-portal-error");
            }

            if (!error) {
//                var cpDisclaimerEnabled = '<?php // echo $orgDetail['Organization']['cp_disclaimer_enabled']                                    ?>';
//                if (cpDisclaimerEnabled == 1) {
//                    $("#cpMsg").toggleClass("show");
//                    $(".modal-backdrop").toggleClass("in");
//
//                } else {
//                    $("#guestfeedback2").submit();
//                }
//                
                $("#guestfeedback2").submit();
            } else {
                return false;
            }
        });

        $('#endorsementSearchKeyGuest').bind('keypress keydown keyup', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });

        $("#endorseSearchKey").on("keyup", function () {
            console.log($(this).val());
        });

        $("#default_user_checked").on("change", function () {
//            console.log($(this).is(':checked'));
            var selectDefault = $(this).is(':checked');
            if (selectDefault) {
                $("#selectedUserImage").click();
            }
        });


    });
    function setDefultBg(obj) {

        $(obj).removeClass('guest-portal-error');
        $(obj).css('border', '');
        var errDiv = $(obj).attr('errDiv');
        var DivID = $(obj).attr('id');

//        console.log("DivID : " + DivID);
        if (DivID == 'daisySearchKeyGuest' || DivID == 'endorseLastName') {
            $("#selected_endorse_id").val('');
        }
        if (DivID == 'endorseDepartmentName') {
            $("#selected_dept_id").val('');
        }
        $("." + errDiv).css('display', 'none');


    }

    $("#guestfeedback2").on('click', function () {
        $(".suggestion").remove();
    });
</script>