<?php
//print_r($endorsedata);
?>
<script type="text/javascript">
    var endorsetype = "public";
    var totalendorsepage = '<?php echo $endorsepage; ?>';
    var endorsepage = 2;
</script>
<?php if (!empty($endorsedata)) {
    ?>

    <div class="col-md-12">
        <section>
            <div class="row">
                <div class="search-icn ">
                    <input type="text" placeholder="Search nDorsements or Posts by Title…" id="searchendorsements" class="form-control">
                    <div class="selected-values hidden">
                        <div class="col-md-11" id="selectedValues"></div>
                        <div class="col-md-1 pull-right">
                            <button class="btn btn-clear-all js_clearAll_endorse" type="button">Clear All</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="livesearch"></div>
                </div>
            </div>
            <div class="col-md-12 tect-center hidden" style="text-align: center; margin-top: 10px;"><a class="btn btn-orange-small btn-xs js_newLiveFeeds" href="<?php echo Router::url('/', true); ?>endorse">New Updates</a></div>
            <div class="clearfix"></div>
        </section>
        <div class="row">
            <section id="endorsementlist" style="margin-top:-20px;">
                <?php
                foreach ($endorsedata as $endorse) {
                    if ($endorse['list_type'] == 'wallpost') {
//                        pr($endorse);
//                        exit;
                        $remainingImage = $endorse['imagecount'] + $endorse['emojiscount'] - 5;
                        $likeimag = "like.png";
                        if ($endorse["is_like"] > 0) {
                            $likeimag = "liked.png";
                        }
                        ?>
                        <!-- Post_In_Feed section added by javed on 26-dec,2016 -->

                        <div class="Dear-Details" id="feed_<?php echo $endorse["id"]; ?>" post_id ="<?php echo $endorse["id"]; ?>" >
                            <div class="Name-Post " > 
                                <div class="namenimg" >
                                    <?php
                                    $userImage = Router::url('/', true) . "img/user.png";
                                    if (isset($endorse['user_image']) && $endorse['user_image'] != '') {
                                        $userImage = $endorse['user_image'];
                                    }
                                    ?>
                                    <img alt="" class="img-circle hand show-user-profile" src="<?php echo $userImage; ?>" width="50px" height="50px" align="left" title="<?php echo $endorse["user_name"]; ?>" data-user-id="<?php echo $endorse["user_id"]; ?>" data-logged-id="<?php echo $logged_user_id; ?>">

                                    <h4 class="range"><?php echo $endorse['user_name']; ?></h4>
                                    <h5><?php echo $endorse['user_job_title']; ?></h5>
                                    <div class="menu-down"><?php echo $this->Html->image('menu-down.png', array('class' => 'show-options', 'align' => 'right')) ?>
                                        <div class="clearfix"></div>
                                        <div class="menu-cont">
                                            <ul>
                                                <a href="javascript:void(0);" data-toggle="modal" data-target=".endorse-now-popupmodel">
                                                    <li class="nDorse-now">nDorse Now!</li>
                                                </a>
                                                <?php if ($user_role == 2 || $logged_user_id == $endorse["user_id"]) { ?>
                                                    <!--                                                    <a href="javascript:void(0);">-->
                                                    <li class="delete-post hand delete-post-from-feed"  data-post-id="<?php echo $endorse['post_id']; ?>">Delete this post</li>
                                                    <!--</a>-->
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="data-url hand live-feeds-post" id="feed_<?php echo $endorse["id"]; ?>" post_id ="<?php echo $endorse["id"]; ?>">
                                    <h3><?php echo $endorse['title']; ?></h3>
                                    <p><?php
                                        //echo $endorse['message']; 
                                        if (isset($endorse['message']) && $endorse['message'] != '') {
                                            $message = $endorse['message'];
                                            $mystring = 'http';
                                            $pos = strpos($message, $mystring);
                                            $clickableData = make_clickable($message);
                                            echo $clickableData;
                                        }
                                        ?></p>
                                    <div class="clearfix"></div>
                                    <div class="detail-img">
                                        <?php
                                        if ($endorse['imagecount'] > 0 || !empty($endorse['post_image'])) {
                                            $count = 1;
                                            foreach ($endorse['post_image'] as $index => $postImage) {
                                                if ($count > 5)
                                                    continue;
                                                if ($count == 5) {
                                                    ?>
                                                    <div class="img-cont"> <img alt="img" src="<?php echo $postImage; ?>" class="img-responsive" />
                                                        <?php if ($remainingImage > 0) { ?>
                                                            <span class="new-one">
                                                                <h3>+<?php echo $remainingImage; ?> </h3>
                                                            </span>
                                                        <?php }
                                                        ?>
                                                    </div>
                                                <?php } else {
                                                    ?>
                                                    <div class="img-cont"><img alt="img" src="<?php echo $postImage; ?>" class="img-responsive" /></div>
                                                    <?php
                                                }
                                                $count++;
                                            }
                                            ?>
                                        <?php }
                                        ?>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

                            </div>
                            <?php
                            if (isset($user_do_not_remind) && $user_do_not_remind != '') {
                                $dataTarget = "";
                                $dataToggle = "";
                                if ($user_do_not_remind == 0) {
                                    $dataTarget = "#one";
                                    $dataToggle = "modal";
                                }
                            }
                            if (isset($endorse["is_like"]) && $endorse["is_like"] > 0) {
                                $dataTarget = "";
                                $dataToggle = "";
                            }

                            $likeCaption = " Like";
                            if ($endorse["like_count"] > 1)
                                $likeCaption = " Likes";
                            ?>
                            <div class="orange-bg no-hand">
                                <div class="col-md-2 text-center"> <a href="javascript:void(0)"> 
                                        <a class="show-me-popup" href="javascript:void(0);"> 
                                            <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-post">
                                        </a> 
                                        <span class="show-me-popup" class="likes like-img-post hand" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"] . $likeCaption; ?></span> 
                                </div>
                                <span class="show-popup-flag show-me-popup-new_<?php echo $endorse["id"]; ?>" data-toggle="modal" data-target="#one" class="likes like-img-post hand" post="<?php echo $endorse["id"]; ?>" ></span> 
                                <div class="col-md-8 text-center"> <span>
                                        <?php
                                        $post_date = date("M d", $endorse["created"]);
                                        $createddate = new DateTime(date("Y-m-d H:i:s", $endorse["created"]));
                                        echo $this->App->getFeedTimeInterval($createddate, $servertime, $post_date);
                                        ?>
                                    </span> </div>
                                <div class="col-md-2 text-center hand live-feeds-post" id="feed_<?php echo $endorse["id"]; ?>" post_id ="<?php echo $endorse["id"]; ?>"> 
                                    <?php if ($endorse['post_files'] > 0) { ?>
                                        <img alt="img" src="<?php echo Router::url('/', true); ?>img/attach.png" class="marg-right hand" width="20"> 
                                    <?php } ?>
                                    <img alt="img" src="<?php echo Router::url('/', true); ?>img/post-comnt.png" class="marg-right hand" width="20">
                                    <span class="comnt-count">
                                        <?php echo $endorse['comments_count']; ?>
                                    </span>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <?php
                    } else if ($endorse['list_type'] == 'endorse') {
                        $corevalue = "";
                        $endorser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
                        if ($endorse["endorsement_for"] == "department") {
                            $endorsed_image = Router::url('/', true) . "img/department.png";
                        } elseif ($endorse["endorsement_for"] == "entity") {
                            $endorsed_image = Router::url('/', true) . "img/sub-org.png";
                        }
                        if (isset($endorse["endorser_image"]) && $endorse["endorser_image"] != "") {
                            //echo $endorse["endorser_image"];
                            $user_image = explode("/", $endorse["endorser_image"]);
                            if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                                $endorser_image = $endorse["endorser_image"];
                            }
                        }

                        if (isset($endorse["endorsed_image"]) && $endorse["endorsed_image"] != "") {
                            //	echo $endorse["endorsed_image"];

                            $user_image = explode("/", $endorse["endorsed_image"]);
                            //echo WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1];
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
                        //$readimg = "email-animated.gif";
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
                        <div class="live-feeds"  >
                            <div class="row hand">
                                <div class="live-feeds-ndorse" id="feed_<?php echo $endorse["id"]; ?>" endorse_id ="<?php echo $endorse["id"]; ?>">
                                    <div class="col-md-2 text-center"> <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php echo $no_handclass; ?>" user_id="<?php echo $endorse["endorsed_id"]; ?>" endorse_type="<?php echo $endorse["endorsement_for"]; ?>" src="<?php echo $endorsed_image; ?>">
                                        <h5><?php echo ucfirst($endorse["endorsed_name"]); ?> </h5>
                                    </div>
                                    <div class="col-md-8 text-center">
                                        <div class='feed-vertical'> <?php echo $corevalue; ?></div>
                                    </div>
                                    <div class="col-md-2 text-center"> <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php if ($ndorser_anonymous == "anonymous") { ?>no-hand<?php } ?>" user_id="<?php echo $endorse["endorser_id"]; ?>" endorse_type="<?php echo $ndorser_anonymous; ?>" src="<?php echo $endorser_image; ?>">
                                        <h5>nDorsed by<br />
                                            <span class="nDorsed-by"><?php echo ucfirst($endorser_name); ?></span> </h5>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="orange-bg no-hand">
                                    <div class="col-md-2 text-center"> <a href="javascript:void(0)"> <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-endorse"></a><span class="likes like-img-endorse hand" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"]; ?> Like </span> </div>
                                    <div class="col-md-8 text-center"> <span>
                                            <?php
                                            //=========calculating time difference from present time.
                                            //$createddate = new DateTime(date("m/d/Y h:i:s",$endorse["created"]));
                                            //echo date("Y-m-d H:i:s",$endorse["created"]);
                                            $createddate = new DateTime(date("Y-m-d H:i:s", $endorse["created"]));

                                            $now = new DateTime(date("Y-m-d H:i:s", $servertime));
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
                                    <div class="col-md-2 text-center" >
                                        <?php if ($endorse["is_reply"] > 0) { ?>
                                            <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/reply.png" class="marg-right no-hand" />
                                        <?php } ?>
                                        <?php if (($endorse["imagecount"] > 0 || $endorse["emojiscount"] > 0) && $ndorser_anonymous != "anonymous") { ?>
                                            <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/attach.png" class="marg-right no-hand" />
                                        <?php } ?>
                                        <?php if (trim($endorse["message"]) != "" && $ndorser_anonymous != "anonymous") { ?>
                                            <a href="javascript:void(0)"><img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $readimg; ?>" class="marg-right no-hand" /></a>
                                        <?php } ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </section>
        </div>
        <div style="text-align: center" class="col-md-offset-2"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
    </div>
<?php } else { ?>
    <div class='no-data-nDorse' >No Data available</div>
<?php } ?>
<div class="modal fade nDorse-process like-nDorse" role="dialog" aria-labelledby="myLargeModalLabel" id="one" >
    <div class="modal-dialog" style="width:400px;">
        <div class="modal-content" style="background:#fff;">
            <div class="modal-header">
                <h4>Do you wish to nDorse someone?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group" data-toggle="modal" data-target=".endorse-now-popupmodel" data-dismiss="modal" >
                    <button class="btn btn-block" type="submit" style="margin-right:10px;">Yes </button>
                </div>
                <div class="form-group">
                    <button class="btn btn-block" type="button" data-dismiss="modal" aria-label="Close"> No </button>
                </div>
                <div class="form-group enable-do-not-remind-feed" data-logged-id="<?php echo $logged_user_id; ?>">
                    <button class="btn btn-block" type="button" data-dismiss="modal" aria-label="Close"> Do not remind me again</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

/** Function created by Babulal Prasad to get and make LINK from text
 * @date 02022017 
 * @param type $text
 * @return type Link (Anchor Tag) with text
 */
function make_clickable($text) {
    $regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
    return preg_replace_callback($regex, function ($matches) {
        return "<a target='_blank' href={$matches[0]}>{$matches[0]}</a>";
    }, $text);
}
?>