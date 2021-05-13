<?php
//print_r($endorsedata);
?>
<script type="text/javascript">
    var endorsetype = "public";
    var totalendorsepage = '<?php echo $endorsepage; ?>';
    var endorsepage = 2;</script>
<!--Fancybox Starts -->
<?php
echo $this->Html->css('/js/fancybox/jquery.fancybox.css');
//echo $this->Html->script('fancybox/jquery-1.10.2.min.js');
echo $this->Html->script('fancybox/jquery.fancybox.js');
echo $this->Html->script('fancybox/fancybox/jquery.fancybox.pack.js');
echo $this->Html->script('fancybox/jquery.fancybox-media.js');
?>

<!--Fancybox Ends --> 
<?php
//if (!empty($endorsedata)) {
//    pr($subcenterData); exit;  
?>
<div class="col-md-12">
    <section class="new-post flexPostBox">
        <div class="btn-group mt-top" data-toggle="buttons" style="margin-left: -13px;">
            <label class="btn btn-primary active orgfilterradio">
                <input type="radio" name="feedtype" id="all" value="" autocomplete="off" checked> All
            </label>
            <?php if (!empty($followingIdsArray)) { ?>
                <label class="btn btn-primary orgfilterradio">
                    <input type="radio" name="feedtype" id="following" value="following" autocomplete="off"> Following
                </label>
            <?php } ?>
            <!--            <label class="btn btn-primary orgfilterradio">
                            <input type="radio" name="feedtype" id="posts" value="post" autocomplete="off"> Posts
                        </label>
                        <label class="btn btn-primary orgfilterradio">
                            <input type="radio" name="feedtype" id="nDorsements" value="endorse" autocomplete="off"> nDorsements
                        </label> -->
        </div>
        <div class="posiRel">
            <!-- VIDEO SECTION -->  
            <?php
//            echo $featured_video_enabled; exit;
            if (isset($featured_video_enabled) && $featured_video_enabled == 1) {
                ?>
                <div class="post-thumb">
                    <div class="imgcontainer videocontainer">
                        <?php
                        //pr($orgVideoList);
                        if (!empty($orgVideoList)) {
                            $i = 0;
                            foreach ($orgVideoList as $index => $videodata) {
                                //pr($videodata); 
                                $seenClass = 'unseen';
                                if (!empty($videodata['viewed_by'])) {
                                    if (in_array($logged_user_id, $videodata['viewed_by'])) {
                                        $seenClass = 'seen';
                                    }
                                }
                                ?>
                                <div id="featuredvideo_<?php echo $videodata['id']; ?>">
                                    <div style="position: relative;">
                                        <a class="fancybox-media unseenvideo " data-id="<?php echo $videodata['id']; ?>"  id="inline" href="#data_<?php echo $index; ?>">
                                            <?php
                                            echo $this->Html->image($videodata['thumbnail'], array('alt' => 'Image', 'class' => $seenClass));
                                            ?>
                                            <div class="watch-status"></div>
                                        </a> 
                                        <?php if ($i == 0) { ?>
                                            <h6>Featured Videos</h6>
                                        <?php } $i++; ?>

                                        <?php if ($org_user_role == 'admin') { ?>
                                            <a href="javascript:void(0);">
                                                <div class="watch-close" data-id="<?php echo $videodata['id']; ?>"></div>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div style="display:none">
                                        <div id="data_<?php echo $index; ?>" style="max-width:1024px;">
                                            <video width="100%" height="auto" controls>
                                                <source src="<?php echo $videodata['video_url']; ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>                                
                                    </div>
                                </div>
                                <?php
                            }
                        }

//                    echo $org_user_role;
                        if ($org_user_role == 'admin') {
                            echo $this->Form->create('videoupload', array('url' => array('controller' => 'endorse', 'action' => 'videoupload'), 'id' => 'videoupload', "enctype" => "multipart/form-data"));
                            ?>
                            <div class="fileUpload" style="float: left; position: relative;">
                                <input class="upload hide" id="featuredVideoupload" name="endorse.featuredVideoupload" accept=".mov,.mp4,.avi,.3gp" multiple="" type="file" style="float: left;">
                                <label for="featuredVideoupload" style="margin:0 0 0 5px; padding:0 0; height: 70px; width: 70px;"> 
                                    <!-- <img src="http://192.168.3.151/nDorseV2/img/add-story.png" align="left" width="30" style="float: left;" alt=""> -->
                                    <?php echo $this->Html->image('add-story.png', array('class' => 'show-options pull-left video_add_button', 'align' => 'left')) ?>
                                </label>
                                <!-- <h6>&nbsp;</h6> -->
                                <?php echo $this->Html->image('loader.gif', array('class' => 'tempin upload_loader', 'align' => 'left', 'style' => 'display: none;')) ?>
                            </div>

                            <div class="panel panel-default hidden " style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;"></div>
                            <span style="color: orangered; display: none;" id="validFileError"></span>
                            <!--</div>-->
                            <?php
                            echo $this->Form->end();
                            ?>
                        </div>
                    </div>
                    <!-- <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Disclaimer">?</span> -->
                    <!-- HTML to write -->
                    <span href="#" class="tTip " data-toggle="tooltip" data-placement="left" 
                          title="Featured Video 
                          -Accepted file types: MP4 & MOV
                          -Max upload file size of 150MB">?</span>
                      <?php } ?>
                <!-- VIDEO SECTION END-->
            <?php } ?>
        </div>
    </section>
    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    <section>
        <div class="row">
            <div class="col-md-7">
                <div class="search-icn ">
                    <input type="text" placeholder="Search nDorsements or Posts by Title…" id="searchendorsements" class="form-control">
                    <div class="selected-values hidden">
                        <div id="selectedValues"></div>
                        <button class="btn btn-clear-all js_clearAll_endorse" type="button">Clear All</button>
                    </div>
                    <div id="livesearch"></div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="centerSubForm">
                    <label>Facility:</label>
                    <select class="form-control" name="subcenter" id="subcenter">
                        <option value="0" selected="selected"><b><?php echo $orgName; ?></b> (All)</option>
                        <?php
                        foreach ($subcenterData as $id => $subcenter) {
                            $selected = '';
//                            if ($user_subcenterID == $id) {
//                                $selected = 'selected="selected"';
//                            }
                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $id; ?>"><?php echo $subcenter; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

        </div>
        <div class="col-md-12 tect-center hidden" style="text-align: center; margin-top: 10px;"><a class="btn btn-orange-small btn-xs js_newLiveFeeds" href="<?php echo Router::url('/', true); ?>endorse">New Updates</a></div>
        <div class="clearfix"></div>
    </section>
    <?php if (!empty($endorsedata)) { ?>
        <div class="row">
            <section id="endorsementlist" style="margin-top:-20px;">
                <?php
                foreach ($endorsedata as $endorse) {
//                    pr($endorsedata);exit;
                    if ($endorse['list_type'] == 'wallpost') {
//                        pr($endorse);
//                        exit;
                        $remainingImage = $endorse['imagecount'] + $endorse['emojiscount'] - 5;
                        $likeimag = "like.png";
                        if ($endorse["is_like"] > 0) {
                            $likeimag = "liked.png";
                        }
                        ?>
                        <!-- POST SECTIOn -->

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
                                                <?php if ($org_user_role == 'admin' || $logged_user_id == $endorse["user_id"]) { ?>
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
                                    <h3><?php echo remove_emoji($endorse['title']); ?></h3>
                                    <p><?php
                                        //echo $endorse['message']; 
                                        if (isset($endorse['message']) && $endorse['message'] != '') {
                                            $message = remove_emoji($endorse['message']);
                                            //$message = $endorse['message'];
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
                                                    <div class="img-cont"> 
                                                        <img alt="img" src="<?php echo $postImage; ?>" class="img-responsive" />
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
                                        <?php } ?>
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
                                <div class="col-md-2 col-xs-3 text-center"> <a href="javascript:void(0)"> 
                                        <a class="show-me-popup" href="javascript:void(0);"> 
                                            <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-post">
                                        </a> 
                                        <span class="show-me-popup likes postlikeslist hand" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"] . $likeCaption; ?></span> 
                                </div>
                                <span class="show-popup-flag show-me-popup-new_<?php echo $endorse["id"]; ?>" data-toggle="modal" data-target="#one" class="likes like-img-post hand" post="<?php echo $endorse["id"]; ?>" ></span> 
                                <div class="col-md-8 col-xs-6 text-center"> <span>
                                        <?php
                                        $post_date = date("M d", $endorse["created"]);
                                        $createddate = new DateTime(date("Y-m-d H:i:s", $endorse["created"]));
                                        echo $this->App->getFeedTimeInterval($createddate, $servertime, $post_date);
                                        ?>
                                    </span> </div>
                                <div class="col-md-2 col-xs-3 text-center hand" id="feed_<?php echo $endorse["id"]; ?>" post_id ="<?php echo $endorse["id"]; ?>"> 
                                    <?php if ($endorse['post_files'] > 0) { ?>
                                        <img alt="img" src="<?php echo Router::url('/', true); ?>img/attach.png" class="marg-right hand post-attachment-pin" post_id="<?php echo $endorse["id"]; ?>" width="20"> 
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
//                        pr($endorse); 
//                        echo remove_emoji($endorse['message']);
//                        exit;

                        $corevalue = "";
                        $endorser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
                        if ($endorse["endorsement_for"] == "department") {
                            $endorsed_image = Router::url('/', true) . "img/department.png";
                        } elseif ($endorse["endorsement_for"] == "entity") {
                            $endorsed_image = Router::url('/', true) . "img/sub-org.png";
                        }
                        if (isset($endorse["endorser_image"]) && $endorse["endorser_image"] != "") {
                            $user_image = explode("/", $endorse["endorser_image"]);
                            if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                                $endorser_image = $endorse["endorser_image"];
                            }
                        }

                        if (isset($endorse["endorsed_image"]) && $endorse["endorsed_image"] != "") {
                            $user_image = explode("/", $endorse["endorsed_image"]);
                            //echo WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1];
                            if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                                $endorsed_image = $endorse["endorsed_image"];
                            }
                        }
                        $endorse["corevalues"] = $this->App->commoncorevaluesarrangement($endorse["corevalues"]);
                        foreach ($endorse["corevalues"] as $coreval) {
                            if ($corevalue != "") {
                                $corevalue .= "; ";
                            }
                            if (trim($coreval["name"]) != "") {
                                $corevalue .= '<span style="color:' . $coreval["color_code"] . ';">' . trim($coreval["name"]) . '</span>';
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
                        <div class="live-feeds"  id="live_feed_<?php echo $endorse["id"]; ?>">
                            <!-- <?php
                            if (isset($endorse['type']) && $endorse['type'] == 'guest') {
                                ?>
                                                                                                                                                                                                                        <div class="GuestTag">Guest nDorsment</div>
                            <?php } ?> -->

                            <!-- Delete nDorsement code start -->
                            <?php if ($org_user_role == 'admin' || $logged_user_id == $endorse["endorser_id"]) { ?>
                                <div class="col-md-12 col-xs-12">
                                    <?php
                                    if (isset($endorse['type']) && $endorse['type'] == 'guest') {
                                        ?>
                                        <div class="GuestTag pull-right">Guest nDorsment</div>
                                    <?php } ?>
                                    <div class="menu-down"><?php echo $this->Html->image('menu-down.png', array('class' => 'show-options', 'align' => 'right')) ?>
                                        <div class="clearfix"></div>
                                        <div class="menu-cont">
                                            <ul>
                                                <li class="delete-post hand delete-endorse-from-feed"  data-endorse-id="<?php echo $endorse['id']; ?>">Delete this nDorsement</li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <!-- Delete nDorsement code end -->                            
                            <div class="row hand">
                                <div class="live-feeds-ndorse" id="feed_<?php echo $endorse["id"]; ?>" endorse_id ="<?php echo $endorse["id"]; ?>">


                                    <div class="col-md-2 col-xs-3 text-center">

                                        <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php echo $no_handclass; ?>" user_id="<?php echo $endorse["endorsed_id"]; ?>" endorse_type="<?php echo $endorse["endorsement_for"]; ?>" src="<?php echo $endorsed_image; ?>">
                                        <h5><?php echo ucfirst($endorse["endorsed_name"]); ?> </h5>
                                    </div>
            <!--                        <div class="col-md-8 text-center"><div class='feed-vertical'> <?php echo $corevalue; ?></div></div>
                        <div class="col-md-2 text-center"> <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user" user_id="<?php echo $endorse["endorser_id"]; ?> <?php if ($ndorser_anonymous == "anonymous") { ?>no-hand<?php } ?>" endorse_type="<?php echo $ndorser_anonymous; ?>" src="<?php echo $endorser_image; ?>">-->
                                    <div class="col-md-8 col-xs-6 text-center">
                                        <div class='feed-vertical autoWidth'> 
                                            <?php echo $corevalue; ?>

                                            <?php
                                            if ($endorse["type"] == "standard") {
                                                $message = $endorse['message'];
                                                $message = remove_emoji($message);
                                                $dots = '';
                                                $msgLngth = strlen($message);
                                                $dots = ($msgLngth > 80) ? '...' : '';
                                                $message = substr($message, 0, 80);
//                                                $messageArray = array();
//                                                if ($message != '') {
//                                                    $messageArray = explode(" ", $message);
//                                                }
//                                                $msgLngth = count($messageArray);
//                                                $output = array_slice($messageArray, 0, 20);
//                                                $message = implode(" ", $output);
                                                $showOpenEnvelop = 0;

//                                                if ($msgLngth > 8) {
//                                                    $showOpenEnvelop = 1;
//                                                    $dots .="...";
//                                                }


                                                $msgLngth = strlen($message);
                                                if ($endorse['public_endorse_visible'] == 1 && $msgLngth > 0) {
                                                    echo '<br> <span style="/*! border: 1px solid #F47521; */font-size: 18px;font-weight: bold; white-space:normal" class="btn mt10 mb10  ">"' . $message . '"' . $dots . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>



                                        <div class="detail-img center-block text-center" style="display: flex;flex-flow: row wrap;justify-content: center;margin-bottom: 5%;">
                                            <?php
                                            $remainingImage = $endorse['emojiscount'] - 5;
//                                        pr($endorse);
                                            if (($endorse['imagecount'] > 0 || $endorse['emojiscount'] > 0 ) || !empty($endorse['post_image'])) {
                                                $count = 1;
                                                foreach ($endorse['bitmoji_images'] as $index => $postImage) {
                                                    //pr($postImage['name']);
//                                                $emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
//                                                if (strpos($emojis_url, 'localhost') < 0 || strpos($emojis_url, 'staging') < 0) {
//                                                    $emojis_url = str_replace("http", "https", $emojis_url);
//                                                }
                                                    //$emojis_url = str_replace("http", "https", $emojis_url);

                                                    $postImage = $postImage;
                                                    if ($count > 5)
                                                        continue;
                                                    if ($count == 5) {
                                                        ?>
                                                        <div class="img-cont"> 
                                                            <img alt="img" src="<?php echo $postImage; ?>" class="img-responsive" />
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
                                            <?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-3 text-center">
                                        <!-- <div class="GuestTag">Guest nDorsment</div> -->
                                        <div class="clearfix"></div>
                                        <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php if ($ndorser_anonymous == "anonymous") { ?>no-hand<?php } ?>" user_id="<?php echo $endorse["endorser_id"]; ?>" endorse_type="<?php echo $ndorser_anonymous; ?>" src="<?php echo $endorser_image; ?>">
                                        <h5>nDorsed by<br />
                                            <span class="nDorsed-by"><?php echo ucfirst($endorser_name); ?></span> </h5>

                                    </div>

                                    <div class="clearfix"></div>

                                    <!--                          <div class="webCard">
                                                                <div class="titleHead"><h3>ORTHO-X: 6 INCH MEMORY FOAM MATTRESS (ADVANCED)</h3></div>
                                                                <div class="titleUrl"><p>Url:- <a href="#">https://www.livpuresleep.com/products/ortho-x-mattress-6-inch-memory-foam?variant=34821140512921</a></p></div>
                                                                <div class="cardImg"><img src="<?php echo Router::url('/', true); ?>img/product-img.jpg" alt="" /></div>
                                                            </div>-->


                                </div>
                                <div class="clearfix"></div>
                                <div class="orange-bg no-hand">
                                    <div class="col-md-2 col-xs-3 text-center"> <a href="javascript:void(0)"> 
                                            <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-endorse"></a>
                                        <span class="likes hand endorselikeslist" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"]; ?> Like </span> </div>
                                    <div class="col-md-8 col-xs-6 text-center"> <span>
                                            <?php
                                            //=========calculating time difference from present time.
                                            //$createddate = new DateTime(date("m/d/Y h:i:s",$endorse["created"]));
                                            //echo date("Y-m-d H:i:s",$endorse["created"]);
                                            $createddate = new DateTime(date("Y-m-d H:i:s", $endorse["created"]));
                                            $date = new DateTime();
//$timeZone = $date->getTimezone();
//echo $timeZone->getName();
//echo date_default_timezone_get();
//$timestamp = time()+date("Z");
//echo gmdate("Y/m/d H:i:s",$timestamp);
                                            $now = new DateTime(date("Y-m-d H:i:s", $servertime));
//					    pr($createddate); pr($now);
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
                                            <?php //echo $endorsedate;       ?>
                                        </span> </div>
                                    <div class="col-md-2 col-xs-3 text-center" >
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
<?php } else {
    ?>
    <div class="row">
        <section id="endorsementlist" style="margin-top:10px;">
            <div class='no-data-nDorse' >No Data available</div>
        </section>
    </div>

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
<div class="modal fade likesmodel LikeListings" id="one" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">

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

function remove_emoji($text) {
    $text = preg_replace('/\\\\u[0-9A-F]{4}/i', '', $text);
    return $text;
}
?>
<script>
    $(document).ready(function () {

        $(document).on('click', '.fancybox-close', function () {
            $('video, audio').trigger('pause');
        });
        $("#featuredVideoupload").on('change', function () {
            $("#validFileError").css('display', 'none');
            var file_data = $("#featuredVideoupload").prop("files")[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            if (isValidFile($("#featuredVideoupload").get(0).files[0])) {
                $(".video_add_button").hide();
                $(".upload_loader").css('display', 'block');
                $.ajax({
                    type: "POST",
                    url: siteurl + 'endorse/videoupload',
                    data: form_data, // serializes the form's elements.
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data)
                    {
//                        return false;
                        $(".video_add_button").show();
                        $(".upload_loader").css('display', 'none');
                        var parseData = JSON.parse(data);
                        console.log(parseData);
                        if (parseData.result.status == true) {
                            alert('Uploaded successfully');
                            $("#unseenvideo_0").attr('href', parseData.result.filepath);
                            $("#unseenvideo_0").attr('data-id', parseData.result.videoId);
                            $("#unseenvideo_0").removeClass('hide');
                            window.location.reload();
                            //$(".fileUpload").addClass("hide");
                        } else {
                            $("#validFileError").html("Please select valid files. (Allowed files: mp4 and mov)");
                            $("#validFileError").css('display', 'block');
//                            alert('Unable to upload.');
                        }
                    },
                    error: function () {
                        $(".video_add_button").show();
                        $(".upload_loader").css('display', 'none');
                    }
                });
            }
        });
        $(".unseenvideo").on("click", function () {
            $(this).closest('.videocontainer').find('img').removeClass('unseen').addClass('seen');
            var videoID = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: siteurl + 'cajax/videoviewed',
                data: {videoid: videoID},
                success: function (data, textStatus, xhr) {
                    var jsonparser = $.parseJSON(data);
                    var msg = jsonparser["result"]["msg"];
                },
            });
        });
        $(".watch-close").on("click", function () {
            //$(this).closest('.videocontainer').find('img').removeClass('unseen').addClass('seen');
            var videoID = $(this).attr('data-id');
            console.log(videoID);
            $.confirm({
                title: false,
                content: 'Deleted video will no longer be visible on the Live Feed.',
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
                                url: siteurl + 'cajax/deletevideo',
                                data: {videoid: videoID},
                                success: function (data, textStatus, xhr) {
                                    var jsonparser = $.parseJSON(data);
                                    var status = jsonparser["result"]["status"];
                                    if (status) {
                                        $("#featuredvideo_" + videoID).remove();
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
        function isValidFile(file) {
            $("#validFileError").css('display', 'none');
            var totalFileSize = parseInt(0);
//          console.log(file.size + " = " + 2097152);
//          5242880 5MB
            if (file.size > 157286400) { // 2 mb 2097152, 25-26214400, 150-157286400
                $("#validFileError").html("Please select file upto 150 mb.");
                $("#validFileError").css('display', 'block');
                return false;
            }

            var validMime = ['video/mp4', 'video/quicktime'];
            var validExtension = ["mp4", "mov", "MP4", "MOV"];
            var fileName = file.name;
            var extension = fileName.split('.').pop();
            extension = extension.toLowerCase();
            var fileMime = file.type;
            fileMime = fileMime.toLowerCase();

//            console.log("extension : "+ extension);
//            console.log("file mime type: "+ fileMime);

            if (validExtension.indexOf(extension) == -1) {
                console.log("In Valid File : " + extension);
                $("#validFileError").html("Please select valid files. (Allowed files: mp4 and mov)");
                $("#validFileError").css('display', 'block');
                return false
            } else {
                if (validMime.indexOf(fileMime) == -1) {
                    console.log("In Valid File 2, File : " + file);
                    console.log("In Valid File 2 FileMine : " + fileMime);
                    return false;
                } else {
                    console.log("Valid File");
                    return true;
                }
            }

        }

    });</script>
<script type="text/javascript">
    $(document).ready(function () {

        $.get('https://blog.sliceit.com/bad-credit-lets-fix-that/',
                function (data) {
                    var MetaData = $(data).find('meta[name=adescription]').attr("content");
                    console.log("MetaData");
                    console.log(MetaData);
                });
                



        $('.fancybox').fancybox();
        $(".fancybox-effects-a").fancybox({
            helpers: {
                title: {
                    type: 'outside'
                },
                overlay: {
                    speedOut: 0
                }
            }
        });
        $(".fancybox-effects-b").fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            helpers: {
                title: {
                    type: 'over'
                }
            }
        });
        $(".fancybox-effects-c").fancybox({
            wrapCSS: 'fancybox-custom',
            closeClick: true,
            openEffect: 'none',
            helpers: {
                title: {
                    type: 'inside'
                },
                overlay: {
                    css: {
                        'background': 'rgba(238,238,238,0.85)'
                    }
                }
            }
        });
        // Remove padding, set opening and closing animations, close if clicked and disable overlay
        $(".fancybox-effects-d").fancybox({
            padding: 0,
            openEffect: 'elastic',
            openSpeed: 150,
            closeEffect: 'elastic',
            closeSpeed: 150,
            closeClick: true,
            helpers: {
                overlay: null
            }
        });
        $('.fancybox-buttons').fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            prevEffect: 'none',
            nextEffect: 'none',
            closeBtn: false,
            helpers: {
                title: {
                    type: 'inside'
                },
                buttons: {}
            },
            afterLoad: function () {
                this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
            }
        });
        $('.fancybox-thumbs').fancybox({
            prevEffect: 'none',
            nextEffect: 'none',
            closeBtn: false,
            arrows: false,
            nextClick: true,
            helpers: {
                thumbs: {
                    width: 50,
                    height: 50
                }
            }
        });
        $('.fancybox-media')
                .attr('rel', 'media-gallery')
                .fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    prevEffect: 'none',
                    nextEffect: 'none',
                    //arrows : false,
                    helpers: {
                        media: {},
                        buttons: {}
                    }
                });
        /*
         *  Open manually
         */

        $("#fancybox-manual-a").click(function () {
            $.fancybox.open('1_b.jpg');
        });
        $("#fancybox-manual-b").click(function () {
            $.fancybox.open({
                href: 'iframe.html',
                type: 'iframe',
                padding: 5
            });
        });
        $("#fancybox-manual-c").click(function () {
            $.fancybox.open([
                {
                    href: '1_b.jpg',
                    title: 'My title'
                }, {
                    href: '2_b.jpg',
                    title: '2nd title'
                }, {
                    href: '3_b.jpg'
                }
            ], {
                helpers: {
                    thumbs: {
                        width: 75,
                        height: 50
                    }
                }
            });
        });
    });
</script>
