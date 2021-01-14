<?php
///===================element to search data for live endorsements 
if (!empty($orgdata["Endorsement"])) {
    ?>
    <div class="col-md-12 text-center"><div class="msg">Feeds are read only</div></div>
    <div class="clearfix"></div>
    <?php foreach ($orgdata["Endorsement"] as $endorsement) { ?>
        <section class="lady-lake farhan">
            <div class="row">
                <div class="col-md-3 pull-left" >
                    <?php
                    if (isset($endorsement['type']) && $endorsement['type'] == 'guest') {
                        ?>
                        <div class="GuestTag">Guest nDorsment</div>
                    <?php } ?>
                    <!--<div class="GuestTag">Guest nDorsment</div>-->

                    <div class="col-md-8 text-center">
                        <?php
                        $firstname = "";
                        $lastname = "";
                        if ($endorsement["endorsement_for"] == "user") {

                            $userdetail = $userdetails[$endorsement["endorsed_id"]];
                            $firstname = ucfirst($userdetail["User"]["fname"]);
                            $lastname = ucfirst($userdetail["User"]["lname"]);
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
                        <div class="far-user "><?php echo $firstname . " " . $lastname; ?></div>
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
                </div>
                <div class="col-md-3">
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
        <?php echo $this->Html->Image("like.png", array("alt" => "img", 'width' => '25', 'class' => 'no-hand')); ?>
                            <span class="likes"><?php echo $endorsement["like_count"] . " " . $likeword; ?> </span>
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
                    <div class="col-md-3 ">
                        <div class="col-md-10 text-center">
                            <?php
                            if (trim($endorsement["message"]) != "") {
                                if ($endorsement["is_read"] == 0) {
                                    echo $this->Html->Image("email.png", array("alt" => "img", 'width' => '25', 'class' => 'no-hand pull-right'));
                                } else {
                                    echo $this->Html->Image("open-env.png", array("alt" => "img", 'width' => '25', 'class' => 'no-hand pull-right'));
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php }
} ?>
