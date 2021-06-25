<?php
//pr($orgdata["Endorsement"]);exit;
///===================element to search data for guest endorsement
if (!empty($orgdata["Endorsement"])) {
    ?>
    <div class="clearfix"></div>
    <?php
    foreach ($orgdata["Endorsement"] as $endorsement) {
//        pr($endorsement);
        ?>

        <section class="lady-lake farhan" id="feedback_section_<?php echo $endorsement["id"]; ?>">
            <?php // pr($endorsement['status']);  ?>
            <div class="row">
                <div class="col-md-3 pull-left" >
                    <div class="col-md-8 text-center">
                        <?php
                        $firstname = "";
                        $lastname = "";
//                        pr($endorsement);
                        //pr($endorsement['endorsement_for']);
                        if ($endorsement["endorsement_for"] == "user") {
//                            pr($userdetails);
                            $userdetail = $userdetails[$endorsement["endorsed_id"]];
                            $firstname = ucfirst($userdetail["User"]["fname"]);
                            $nomineeFName = $userdetail["User"]["fname"];
                            $lastname = ucfirst($userdetail["User"]["lname"]);
                            $nomineeLName = $userdetail["User"]["lname"];
                            if (isset($endorsement["daisy_custom_nominee"]) && $endorsement["daisy_custom_nominee"] == 1) {
                                $nomineeName = $endorsement["daisy_nominee_name"];
                            }

                            if ($userdetail["User"]["image"] != "" && file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $userdetail["User"]["image"])) {
                                $profile_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $userdetail["User"]["image"];
                                $image = $this->Html->image($profile_imagenew, array('width' => '64', 'height' => '64', 'id' => 'org_image', 'class' => 'img-circle no-hand'));
                            } else {
                                $image = $this->Html->Image("user.png", array("class" => "img-circle no-hand", "alt" => "64x64", "width" => "64px", "height" => "64px"));
                            }
                        } else if ($endorsement["endorsement_for"] == "department") {
                            $dept = $allvalues["department"];
                            if (isset($dept[$endorsement["endorsed_id"]])) {
                                $firstname = $dept[$endorsement["endorsed_id"]];
                            }
                            $image = $this->Html->Image("department.png", array("class" => "img-circle no-hand", "alt" => "64x64", "width" => "64px", "height" => "64px"));
                        } else if ($endorsement["endorsement_for"] == "entity") {

                            $entity = $allvalues["entities"];
                            if (isset($entity[$endorsement["endorsed_id"]])) {
                                $firstname = $entity[$endorsement["endorsed_id"]];
                            }
                            $image = $this->Html->Image("sub-org.png", array("class" => "img-circle no-hand", "alt" => "64x64", "width" => "64px", "height" => "64px"));
                        }
                        ?>
                        <?php echo $image; ?> <!--<img alt="" data-src="holder.js/64x64" class="media-object" style="width: 64px; height: 64px;" src="img/user.svg" data-holder-rendered="true"> -->

                        <div class="far-user " id="user_nominee_name_<?php echo $endorsement["id"]; ?>"><?php echo $firstname . " " . $lastname; ?></div>

                    </div>
                </div>
                <div class="col-md-6 text-center" style="margin:20px 0;">
                    <?php
                    if (!empty($endorsement["EndorseCoreValues"])) {
                        $orgcorevaluesarray = $allvalues["orgcorevaluesandcode"];

                        $checkcorevalues = array();
                        foreach ($endorsement["EndorseCoreValues"] as $endorsecorevalues) {
                            if (isset($orgcorevaluesarray[$endorsecorevalues["value_id"]])) {
                                $checkcorevalues[] = $orgcorevaluesarray[$endorsecorevalues["value_id"]];
                            }
                        }
                        asort($checkcorevalues);
                        //====after arranging it in asc order

                        if (!empty($checkcorevalues)) {
                            $counter = count($checkcorevalues);
                            foreach ($checkcorevalues as $allcorevalues) {
                                echo '<span class="treated-col" style = "color: ' . $allcorevalues["colorcode"] . '">' . $allcorevalues["name"] . '</span>';
                                if ($counter > 1) {
                                    echo "; ";
                                }
                                $counter--;
                            }
                        }
                    }
                    ?>
                    <div class="col-md-12 text-center daisy_message_<?php echo $endorsement["id"]; ?>" style="margin:5px 0;">
                        <?php echo $endorsement["message"]; ?>
                    </div>
                </div>

                <?php
                /**
                *Ticket:285 Issues to Clean Up 
                *Revert Pending DAISY Nomination Tool back to proper tool options.
                *25jun2021
                */
                ?>
                <div class="col-md-3">
                    <div class="pull-right ">
                        <div class="col-md-6 col-sm-12">
                            <?php if (isset($endorsement['status']) && $endorsement['type'] == 'daisy') { ?>
                                <a href="javascript:void(0);" rel="<?php echo $endorsement['id']; ?>_one" class="dots">
                                    <?php echo $this->Html->Image("3dots.png", array("align" => "pull-right")); ?>
                                </a>
                            <?php } ?>

                            <?php if (isset($endorsement['status']) && $endorsement['status'] != 1 && $endorsement['type'] == 'guest') { ?>
                                <a href="javascript:void(0);" rel="<?php echo $endorsement['id']; ?>_one" class="dots">
                                    <?php echo $this->Html->Image("3dots.png", array("align" => "pull-right")); ?>
                                </a>
                            <?php } ?>
                            <div class="arrow_box <?php echo $endorsement['id']; ?>_one" style="position: absolute; right: -18px; z-index: 2; display: none;">
                                <div style="border:0px solid #f00; margin-top:-35px; margin-right:5px;" class="pull-right">
                                    <?php echo $this->Html->Image("popupArrow.png"); ?>
                                </div>
                                <ul>
                                    <?php //pr($endorsement); ?>
                                    <?php if (isset($endorsement['status']) && $endorsement['status'] != 1 && $endorsement['type'] == 'guest' && $endorsement['status'] != 2 && $endorsement['status'] != 3) 
                                    { 
                                        ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 1)">Approve to Live</a>
                                        </li>
                                        <?php
                                    }
                                        ?>

                                    <?php if (isset($endorsement['status']) && $endorsement['status'] != 0 && $endorsement['type'] == 'daisy') 
                                    {   ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 0)">Move to Pending</a>
                                        </li>
                                        <?php 
                                    }   
                                        ?>

                                    <?php 
                                    if (isset($endorsement['status']) && ($endorsement['status'] != 1 && $endorsement['type'] == 'daisy')) {
                                        ?>
                                      <li>
                                      <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 1)">Move to Selected</a>
                                      </li>
                                        <?php 
                                    }
                                        ?>
                                    <?php
                                    //pr($endorsement['status']); 
                                    if (isset($endorsement['status']) && ($endorsement['type'] == 'daisy' && $endorsement['status'] != 2)) {
                                        ?>
                                      <li>
                                      <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 2)">Move to Not Selected</a>
                                      </li>
                                        <?php 
                                    }
                                        ?>
                                    <?php if (isset($endorsement['status']) && ($endorsement['status'] != 1 && $endorsement['status'] != 2 && $endorsement['status'] != 3 && $endorsement['type'] == 'guest')) { ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 3)">Hold</a>
                                        </li>
                                    <?php } ?>
                                    
                                    
                                    <?php if (isset($endorsement['status']) && $endorsement['type'] == 'daisy') { ?>
                                        <li>
                                            <a href="javascript:void(0)" id="edit_link_<?php echo $endorsement["id"]; ?>"
                                               data-deptid="<?php echo $endorsement["department_id"]; ?>"
                                               data-deptname="<?php echo $endorsement["department_name"]; ?>"
                                               data-fname="<?php echo $nomineeFName; ?>" 
                                               data-lname="<?php echo $nomineeLName; ?>"
                                               data-custom-user="<?php echo $endorsement["daisy_custom_nominee"]; ?>" 
                                               data-custom-dept="<?php echo $endorsement["daisy_custom_dept"]; ?>" 
                                               data-userid ="<?php echo $endorsement["endorsed_id"]; ?>" 
                                               data-id="<?php echo $endorsement["id"]; ?>" 
                                               data-comment="<?php echo $endorsement["message"]; ?>" class="editDaisyNominations">Edit</a>
                                        </li>
                                    <?php } ?>
                                    <?php if (isset($endorsement['status']) && $endorsement['status'] != 1) { ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeGuestNdorseStatus('<?php echo $endorsement["id"]; ?>', 4)">Delete</a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 text-center pull-right">
                        <?php
                        $namedetailkey["User"] = array("fname" => "", "lname" => "", "image" => "");
                        if ($endorsement["type"] == "anonymous") {
                            $namedetailkey["User"]["lname"] = "****";
                        } else {
                            if (isset($userdetails[$endorsement["endorser_id"]])) {
                                $namedetailkey = $userdetails[$endorsement["endorser_id"]];
                                $imageuser = $userdetails[$endorsement["endorser_id"]];
                                $namedetailkey["User"]["image"] = $imageuser["User"]["image"];
                            }
                        }

                        if ($namedetailkey["User"]["image"] != "" && file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $namedetailkey["User"]["image"])) {
                            $profile_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $namedetailkey["User"]["image"];
                            $image = $this->Html->image($profile_imagenew, array('width' => '64', 'height' => '64', 'id' => 'org_image', 'class' => 'img-circle no-hand'));
                        } else {
                            $image = $this->Html->Image("user.png", array("class" => "text-center no-hand", "alt" => "32*32", "width" => "64", "height" => "64"));
                        }
                        echo $image;
                        ?>
                        <div class="clearfix"></div>
                        <span class="nodorsedby">nDorsed by </span><br />
                        <span class="rohan-col">
                            <?php
                            echo ucfirst($namedetailkey["User"]["fname"]) . " " . ucfirst($namedetailkey["User"]["lname"]);
                            ?>
                        </span></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 orange-bg">
                    <?php $likeword = ($endorsement["like_count"] <= 1) ? "like" : "likes"; ?>
                    <div class="col-md-3 pull-left">
                        <div class="col-md-8 text-center">
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <h4 class="" style="margin:4px 0">
                            <?php
                            //=========calculating time difference from present time.
                            $createddate = new DateTime($endorsement["created"]);
                            $now = new DateTime();
                            $timediff = (array) $now->diff($createddate, true);
                            $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second",);
                            foreach ($timediff as $key => $difference) {
                                if ($difference != 0) {
                                    $diffkey = $arraytimediff[$key];
                                    if ($key == "h" || $key == "i" || $key == "s") {
                                        $plural = ($difference <= 1) ? "" : "s";
                                        echo $difference . " " . $diffkey . $plural . " ago";
                                    } else {
                                        echo date("M d", strtotime($endorsement["created"]));
                                    }
                                    break;
                                }
                            }
                            ?>
                        </h4>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-10 text-center">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
?>
<div class="modal fade" id="myModal_daisy_update" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-body">
                <div class="form-group">
                    <h3>Edit DAISY nomination</h3>
                    <input id="update_endorsement_id" type="hidden" name="update_endorsement_id" value="">
                    <input id="dept_id" type="hidden" name="dept_id" value="">
                    <input id="endorsed_id" type="hidden" name="endorsed_id" value="">
                    <input id="custom_user" type="hidden" name="custom_user" value="">
                    <br/>
                    <span style="font-size: 16px;">Nominee First Name: </span>
                    <input id="nominee_fname" type="text" name="nominee_fname" value="" style="width: 50%;margin-left: 5%;">
                    <br/>
                    <span style="color: red;margin-left: 33%;" class="nominee_fname_error error hide">Please enter nominee first name.</span>
                    <br/>
                    <span style="font-size: 16px;">Nominee Last Name: </span>
                    <input id="nominee_lname" type="text" name="nominee_lname" value="" style="width: 50%;margin-left: 5%;">
                    <br/>
                    <br/>
                    <span style="font-size: 16px;">Department Name: </span>
                    <input id="dept_name" type="text" name="dept_name" value="" style="width: 50%;margin-left: 7%;">
                    <span style="color: red;" class="dept_name_error error hide">Please enter dept name.</span>
                    <br/>
                    <br/>
                    <span style="font-size: 16px;">nDorsement message : </span>
                    <br/>
                    <textarea id="endorsementMessage" style="width: 100%;height: 150px;margin-top: 10px;"></textarea>
                    <span style="color: red;" class="update_message_error error hide">Please enter message.</span>


                </div>
            </div>
            <div class="modal-footer"  >
                <button type="button" id="update_endorse_details" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">CANCEL</span></button>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {

        $(".editDaisyNominations").on("click", function () {
            $("#nominee_name").attr('readonly', false);
            $("#dept_name").attr('readonly', false);
            var endormentId = $(this).attr('data-id');
            var endormentComment = $(this).attr('data-comment');
            var deptId = $(this).attr('data-deptid');
            var deptName = $(this).attr('data-deptname');
            var nomineeFName = $(this).attr('data-fname');
            var nomineeLName = $(this).attr('data-lname');
            var nomineeCustom = $(this).attr('data-custom-user');
            var deptCustom = $(this).attr('data-custom-dept');
            var endorsedUserId = $(this).attr('data-userid');

            $(".error").addClass('hide');

            $("#endorsementMessage").val(endormentComment);
            $("#update_endorsement_id").val(endormentId);
            $("#nominee_fname").val(nomineeFName);
            $("#nominee_lname").val(nomineeLName);
            $("#dept_name").val(deptName);
            $("#dept_id").val(deptId);
            $("#endorsed_id").val(endorsedUserId);
            $("#custom_user").val(nomineeCustom);

            if (nomineeCustom == 0) {
                $("#nominee_name").attr('readonly', 'readonly');
            }

            if (deptCustom == 0) {
                $("#dept_name").attr('readonly', 'readonly');
            }
            $("#myModal_daisy_update").modal("show");

        });

        $("#update_endorse_details").click(function () {

            var endorsementMessage = $.trim($("#endorsementMessage").val());
            var nomineeFName = $.trim($("#nominee_fname").val());
            var nomineeLName = $.trim($("#nominee_lname").val());
            var deptName = $.trim($("#dept_name").val());
            var endorsementId = $.trim($("#update_endorsement_id").val());
            var nomineeId = $.trim($("#endorsed_id").val());
            var deptId = $.trim($("#dept_id").val());
            var customUser = $.trim($("#custom_user").val());

            $(".update_message_error").addClass('hide');
            var org_id = "<?php echo $orgDetail['Organization']['id']; ?>";
            var error = 0;
            $(".error").addClass('hide');
            if (endorsementMessage == '') {
                $(".update_message_error").removeClass('hide');
                error = 1;
            }
            if (nomineeFName == '') {
                $(".nominee_fname_error").removeClass('hide');
                error = 1;
            }


            if (error == 0) {
                $.ajax({
                    type: "POST",
                    url: siteurl + 'ajax/updateDaisyNotification',
                    dataType: 'json',
                    data: {id: endorsementId, message: endorsementMessage, 'nomineeFName': nomineeFName, 'nomineeLName': nomineeLName, 'deptName': deptName, nomineeId: nomineeId, 'deptId': deptId, 'customUser': customUser},
                    success: function (response) {
//                        console.log(response);
//                        return false;
                        if (response.status == false) {
                            alertbootboxcb(response.msg, function () {
                            });
                        } else {
                            alertbootboxcb("DAISY nomination has been updated!", function () {
                            });

                            $(".daisy_message_" + endorsementId).html(endorsementMessage);
                            $("#edit_link_" + endorsementId).attr('data-comment', endorsementMessage);
                            $("#edit_link_" + endorsementId).attr('data-deptname', deptName);
                            $("#edit_link_" + endorsementId).attr('data-fname', nomineeFName);
                            $("#edit_link_" + endorsementId).attr('data-lname', nomineeLName);
                            $("#user_nominee_name_" + endorsementId).html(nomineeFName + ' ' + nomineeLName);
                            $("#myModal_daisy_update").modal("hide");
                        }
                    },
                    error: function (response) {
                    }
                });
            }
        });
    });

</script>