<?php //print_r($endorsedata);exit; ?>
<script type="text/javascript">
    var endorsetype2 = "endorser";
    var totalendorsepage_2 = '<?php echo $endorsepage2; ?>';
    var endorsepage_2 = 2;
</script>


<?php if (!empty($endorsedata2)) { ?>
    <section>
        <div class="search-icn ">
            <input type="text" placeholder="Search nDorsements..." id="searchendorsements" class="form-control">
            <div class="selected-values hidden"><div class="col-md-11" id="selectedValues"></div><div class="col-md-1 pull-right"><button class="btn btn-clear-all js_clearAll_endorse" type="button">Clear All</button></div>
                <div class="clearfix"></div>
            </div>
            <div id="livesearch"></div>
        </div>
        <div class="clearfix"></div>
    </section>
    <section style="padding:10px;">
        <div class="col-md-12 time-range">
            <div class="pull-left">
                <h3>Search nDorsements by date</h3>
            </div>
            <div class="pull-right">
                <button class="btn btn-default" id="showdatawithoutdate2">SHOW ALL</button>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="select-date col-md-12">
            <div class="col-md-3 form-group">
                <label> From</label>

                <?php echo $this->Form->input('startdate', array('placeholder' => 'Select a Start Date', 'type' => 'text', 'id' => 'datepicker_start_2', 'class' => 'form-control date', 'label' => false)); ?>
            </div>
            <div class="col-md-3 form-group">
                <label> To</label>
                <?php echo $this->Form->input('enddate', array('placeholder' => 'Select a End Date', 'type' => 'text', 'id' => 'datepicker_end_2', 'class' => 'form-control date', 'label' => false)); ?>
            </div>
            <div class="col-md-6 ">
                <button class="btn btn-default" id="endorsesearchsection2">SEARCH</button>

            </div>
        </div>
        <div class="clearfix"></div>
    </section>
    <section id="endorsementlist2">
        <?php
        foreach ($endorsedata2 as $endorse) {

            $corevalue = "";
            $endorser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
            if ($endorse["endorsement_for"] == "department") {
                $endorsed_image = Router::url('/', true) . "img/department.png";
            } elseif ($endorse["endorsement_for"] == "entity") {
                $endorsed_image = Router::url('/', true) . "img/sub-org.png";
            }
            if (isset($endorse["endorser_image"])) {
                $user_image = explode("/", $endorse["endorser_image"]);
                if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                    $endorser_image = $endorse["endorser_image"];
                }
            }

            if (isset($endorse["endorsed_image"])) {
                $user_image = explode("/", $endorse["endorsed_image"]);
                if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                    $endorsed_image = $endorse["endorsed_image"];
                }
            }

            $endorse["corevalues"] = $this->App->commoncorevaluesarrangement($endorse["corevalues"]);

            foreach ($endorse["corevalues"] as $coreval) {
                if ($corevalue != "") {
                    $corevalue.="; ";
                }
                if (trim($coreval["name"]) != "") {
                    $corevalue.='<span style="color:' . $coreval["color_code"] . ';">' . trim($coreval["name"]) . '</span>';
                }
            }
            $endorsedate = date("M d", $endorse["created"]);
            $readimg = "email.png";
            if ($endorse["is_read"] > 0) {
                $readimg = "open-env.png";
            }
            $likeimag = "like.png";
            if ($endorse["is_like"] > 0) {
                $likeimag = "liked.png";
            }
            $endorser_name = $endorse["endorser_name"];
            $ndorser_anonymous = "user";
            if ($endorse["type"] == "anonymous") {
                $endorser_name = "****";
                $endorser_image = Router::url('/', true) . "img/user.png";
                $ndorser_anonymous = "anonymous";
            }
            $no_handclass = "";
            if ($endorse["endorsement_for"] == "department" || $endorse["endorsement_for"] == "entity") {
                $no_handclass = "no-hand";
            }
            ?>
            <div class="live-feeds new-bg-feeds" id="live_feed_<?php echo $endorse["id"]; ?>" >
                <?php
                if (isset($endorse['type']) && $endorse['type'] == 'guest') {
                    ?>
                    <div class="GuestTag">Guest nDorsment</div>
                <?php } ?>
                <!-- Delete nDorsement code start -->
                <?php 
                if ($org_user_role == 'admin' || $logged_user_id == $endorse["endorser_id"]) { ?>
                    <div class="col-md-12">
                        <div class="menu-down"><?php echo $this->Html->image('menu-down.png', array('class' => 'show-options', 'align' => 'right')) ?>
                            <div class="clearfix"></div>
                            <div class="menu-cont">
                                <ul>
                                    <li class="delete-post1 hand delete-endorse-from-feed"  data-endorse-id="<?php echo $endorse['id']; ?>"><i class="fa fa-trash"></i> Delete this nDorsement</li>

                                </ul>
                                <!-- Edit nDorsement code starts here -->
                                <ul>
                                    <li class="edit-post hand live-feeds-edit-ndorse-message" id="feed_<?php echo $endorse["id"]; ?>" endorse_id ="<?php echo $endorse["id"]; ?>"><i class="fa fa-pencil"></i> Edit message</li>

                                </ul>
                                <!-- Edit nDorsement code ends here -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!-- Delete nDorsement code end -->    
                <div class="row hand">
                    <div class="live-feeds-ndorse" id="feed_<?php echo $endorse["id"]; ?>" endorse_id ="<?php echo $endorse["id"]; ?>">
                        <div class="col-md-2 text-center col-xs-3"> <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php echo $no_handclass; ?>" user_id="<?php echo $endorse["endorsed_id"]; ?>" endorse_type="<?php echo $endorse["endorsement_for"]; ?>" src="<?php echo $endorsed_image; ?>">
                            <h5><?php echo ucfirst($endorse["endorsed_name"]); ?> </h5>
                        </div>
                        <div class="col-md-8 text-center col-xs-6"><div class='feed-vertical'> <?php echo $corevalue; ?></div></div>
                        <div class="col-md-2 text-center col-xs-3"> <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php if ($ndorser_anonymous == "anonymous") { ?>no-hand<?php } ?>" user_id="<?php echo $endorse["endorser_id"]; ?>" endorse_type="<?php echo $ndorser_anonymous; ?>" src="<?php echo $endorser_image; ?>">
                            <h5>nDorsed by<br />
                                <span class="nDorsed-by"><?php echo ucfirst($endorser_name); ?></span> </h5>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="orange-bg no-hand">
                        <div class="col-md-2 col-xs-3 text-center"> <a href="javascript:void(0)">

                                <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-endorse"></a>
                            <span class="likes" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"]; ?> Like </span> </div>
                        <div class="col-md-8 col-xs-6 text-center"> <span>
                                <?php
                                //=========calculating time difference from present time.
                                //$createddate = new DateTime(date("m/d/Y h:i:s",$endorse["created"]));
                                //echo date("Y-m-d H:i:s",$endorse["created"]);
                                $createddate = new DateTime(date("Y-m-d H:i:s", $endorse["created"]));

                                //$now = new DateTime();
                                $now = new DateTime(date("Y-m-d H:i:s", $servertime2));
                                $timediff = (array) $now->diff($createddate, true);
                                $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second",);
                                foreach ($timediff as $key => $difference) {
                                    if ($difference != 0) {
                                        $diffkey = $arraytimediff[$key];
                                        if ($key == "s") {
                                            echo "few seconds ago";
                                        } elseif ($key == "h" || $key == "i") {
                                            $plural = ($difference <= 1) ? "" : "s";
                                            echo $difference . " " . $diffkey . $plural . " ago";
                                        } else {
                                            echo $endorsedate;
                                        }
                                        break;
                                    }
                                }
                                ?>
                            </span> </div>
                        <div class="col-md-2 col-xs-3 text-center"><?php if ($endorse["is_reply"] > 0) { ?><img class="no-hand" width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/reply.png"><?php } ?>
                            <?php if (($endorse["imagecount"] > 0 || $endorse["emojiscount"] > 0) && $ndorser_anonymous != "anonymous") { ?><img class="no-hand" width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/attach.png"><?php } ?>
                            <?php if (trim($endorse["message"]) != "" && $ndorser_anonymous != "anonymous") { ?>
                                <a href="javascript:void(0)"><img class="no-hand" width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $readimg; ?>"></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </section>
    <div style="text-align: center" class="col-md-offset-2"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
<?php } else { ?>
    <div class='no-data-nDorse' >No Data available</div>
<?php } ?>
