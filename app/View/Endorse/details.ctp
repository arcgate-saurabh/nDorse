<?php
//print_r($endorsedata);
$endorser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
if ($endorsedata["endorsement_for"] == "department") {
    $endorsed_image = Router::url('/', true) . "img/department.png";
} elseif ($endorsedata["endorsement_for"] == "entity") {
    $endorsed_image = Router::url('/', true) . "img/sub-org.png";
}

if (isset($endorsedata["endorse_image"])) {
    $user_image = explode("/", $endorsedata["endorse_image"]);
    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
        $endorser_image = Router::url('/', true) . PROFILE_IMAGE_DIR . "small/" . $user_image[count($user_image) - 1];
    }
}

if (isset($endorsedata["endorsed_image"])) {
    $user_image = explode("/", $endorsedata["endorsed_image"]);
    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {

        $endorsed_image = Router::url('/', true) . PROFILE_IMAGE_DIR . "small/" . $user_image[count($user_image) - 1];
    }
}
$createddate = date("m/d/Y", strtotime($endorsedata["created"]));
$ndorser_anonymous = "user";
$endorser_name = $endorsedata["endorser_name"];
$show_user_profile = 'show-user-profile';
$ndorser_anonymous = "user";
if ($endorsedata["type"] == "anonymous") {
    $endorser_name = "****";
    $show_user_profile = '';
    $endorser_image = Router::url('/', true) . "img/user.png";
    $ndorser_anonymous = "anonymous";
}
?>

<div class="nDorse-Details">
    <section>
        <div class="">
            <div class="grey-bg">
                <div class="col-md-4 col-sm-4">
                    <div class="text-left nDorsed">

                        <img src="<?php echo $endorsed_image; ?>" user_id="<?php echo $endorsedata["endorsed_id"]; ?>"  
                             endorse_type="anonymous<?php //echo $endorsedata["endorsement_for"];                         ?>" 
                             data-user-id="<?php echo $endorsedata["endorsed_id"]; ?>" data-logged-id="<?php echo $logged_user_id; ?>"
                             width="100" class="img-circle ndorse_click hand show-user-profile"  alt=""/>

                        <span class="rohan-space"><?php echo ucfirst($endorsedata["endorsed_name"]); ?></span>
                        <p class="ndorser-detail">nDorsed</p>
                        <?php
//                        pr($endorsedata);
                        if ($logged_user_id != $endorsedata["endorsed_id"]) {
                            ?>
                            <?php
                            $following = $unfollowing = '';
                            if ($endorsedata['endorsed_following']) {
                                $unfollowing = 'hidden';
                            } else {
                                $following = 'hidden';
                            }
                            ?>

                            <div class="userFollow follow <?php echo $following; ?>" id="following_<?php echo $endorsedata["endorsed_id"]; ?>"  data-attr="following" data-user-id = '<?php echo $endorsedata["endorsed_id"]; ?>'></div>
                            <div class="userFollow unfollow <?php echo $unfollowing; ?>" id="unfollowing_<?php echo $endorsedata["endorsed_id"]; ?>" data-attr="unfollowing" data-user-id = '<?php echo $endorsedata["endorsed_id"]; ?>'></div>

                        <?php } ?>

                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="date-detail hidden-xs"><?php //echo $createddate;                         ?>

                        <?php
                        //=========calculating time difference from present time.
                        //$createddate = new DateTime(date("m/d/Y h:i:s",$endorse["created"]));
                        //echo date("Y-m-d H:i:s",$endorse["created"]);
//					$createddate = new DateTime(date("Y-m-d H:i:s",strtotime($endorsedata["created"])));
//					
//                    $now = new DateTime();
//                    $timediff = (array)$now->diff($createddate, true);
//                    $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second", ); 
//                    foreach($timediff as $key => $difference){
//                        if($difference!=0){
//                            $diffkey = $arraytimediff[$key];
//                            if($key == "h" || $key == "i" || $key == "s"){
//                                $plural = ($difference <=1)?"":"s";
//                                echo $difference." ".$diffkey.$plural." ago";
//                            }else{
//                               echo $createddate = date("m/d/Y",strtotime($endorsedata["created"]));
//                            }
//                            break;
//                        }
//                    }
                        echo $createddate = date("m/d/Y", strtotime($endorsedata["created"]));
                        ?>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="text-right nDorser">
                        <span class="rohan-space hidden-xs"><?php echo ucfirst($endorser_name); ?></span> 

                        <img src="<?php echo $endorser_image; ?>" 
                             data-user-id="<?php echo $endorsedata["endorser_id"]; ?>" data-logged-id="<?php echo $logged_user_id; ?>"
                             user_id="<?php echo $endorsedata["endorser_id"]; ?>" endorse_type="anonymous<?php //echo $ndorser_anonymous;                   ?>"  
                             width="100" class="img-circle ndorse_click hand <?php echo $show_user_profile; ?>" alt=""/>

                        <span class="rohan-space visible-xs"><?php echo ucfirst($endorser_name); ?></span> 

                        <p class="ndorsed-detail">nDorser</p>
                        <?php
                        if ($logged_user_id != $endorsedata["endorser_id"]) {
                            ?>
                            <?php
                            $following = $unfollowing = '';
                            if ($endorsedata['endorser_following']) {
                                $unfollowing = 'hidden';
                            } else {
                                $following = 'hidden';
                            }
                            ?>
                            <div class="userFollow follow <?php echo $following; ?>" id="following_<?php echo $endorsedata["endorser_id"]; ?>"  data-attr="following" data-user-id = '<?php echo $endorsedata["endorser_id"]; ?>'></div>
                            <div class="userFollow unfollow <?php echo $unfollowing; ?>" id="unfollowing_<?php echo $endorsedata["endorser_id"]; ?>" data-attr="unfollowing" data-user-id = '<?php echo $endorsedata["endorser_id"]; ?>'></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-xs-12 visible-xs">
                    <div class="date-detail ">
                        <?php
                        echo $createddate = date("m/d/Y", strtotime($endorsedata["created"]));
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
    <section>
        <?php $endorsecorevalues = $this->App->commoncorevaluesarrangement($endorsedata["core_values"]);
        ?>
        <div class="">
            <h3>Core Values Shown :</h3>
            <!--code to delete nDorsement -->
            <!--<input type="button" class="delete-post hand delete-post-from-feed"  data-endorse-id="<?php echo $endorsedata['id']; ?>" value="Delete this nDorsement">-->

            <div class="core-values-shown1">
                <table class="table table-hover table-core-value-shown">
                    <?php foreach ($endorsecorevalues as $val) { ?>
                        <tr>
                            <td><span style="color:<?php echo $val["color_code"]; ?>;"><?php echo $val["name"]; ?></span></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </section>

    <!--HashTag Section Start -->
    <?php if (!empty($endorsedata['hashtags'])) { ?>
        <section class="hashTagClass">
            <div class="">
                <h3>HashTags :</h3>
                <input type="hidden" value="" name="hashtags" id="hashtags">
                <div class="panel-default">
                    <?php foreach ($endorsedata['hashtags'] as $index => $hash) { ?>
                        <button type="button" name="" data-id="<?php echo $hash['id']; ?>" id="hashtag_<?php echo $hash['id']; ?>" class="btn btn-success btn-sm hashtag-btn">#<?php echo $hash['name']; ?></button>
                    <?php } ?>
                </div>

                <span class="error" id="messageError"></span>
            </div>
        </section>
    <?php } ?>
    <!-- HashTag Section END-->

    <?php
    $message = remove_emoji($endorsedata["message"]);
    if ($ndorser_anonymous != "anonymous") {
        ?>
        <?php 
        //pr($endorseMessageMinLimit);
        if(isset($this->params->query['mode']) &&  $this->params->query['mode'] == "edit") { ?>
           <section>
                <div class="nDorse-Details-msg">
                    <h3><?php if ($optionalComments != 0) { ?> <?php } ?> Share Your Story / Send a Message <span style="font-size:18px">(Max. 3000 Characters)</span>:</h3>
                    <span class="character_counts">0 Character</span>
                        <div class="mesg">
                            <!-- <textarea class="js-add-msg js-get-msg" maxlength="3000" rows="5" cols="100"><?php //echo $message; ?></textarea> -->
                            <!-- <div class="col-md-12"> -->
                                <textarea id="user_msg_val" class="add-msg add-msg-val js-add-msg js-get-msg" data-min="<?php echo $endorseMessageMinLimit; ?>" name="message" data-optional="<?php echo $optionalComments; ?>"  maxlength="3000" rows="5" cols="100"><?php echo $message; ?>
                                </textarea>
                            <!-- </div> -->
                        </div>

                        
                        <input type="button" class="btn btn-success js-edit-endorse-message" value="Save Message" data-endorse-id = "<?php echo $endorsedata["id"]; ?>" data-endorse-msg = "<?php echo $endorsedata["message"]; ?>" />
                        
                        <input type="button" class="btn btn-primary js-cancel-editing" value="Reset Message" data-endorse-id = "<?php echo $endorsedata["id"]; ?>" />
                        <span class="error" id="endMessageError"></span>
                        <span class="empty-message-err" style="margin-left: 1%; color: red;display: none;">Your message is empty. Please enter a message to post successfully.</span>
                </div>
                
            </section>     
        <?php } else { ?>
            <section>
                <div class="nDorse-Details-msg">
                    <h3>Message :</h3>
                    <div class="mesg">
                        <p><?php echo $message; ?></p>
                    </div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>
    <section>
        <div class="">
            <?php if (!empty($endorsedata["attatched_image"]) && $ndorser_anonymous != "anonymous") {
                ?>
                <div class="col-md-12" style="padding-left:0"> <span class="">
                        <h3 class="attach-img"> &nbsp;Attached Images</h3>
                    </span>
                    <div id="imagePanel" class="panel panel-default" style="padding:10px; max-height:275px; overflow:auto;">
                        <?php
                        $index = 1;
                        foreach ($endorsedata["attatched_image"] as $imageval) {
                            $bigimg = str_replace("/small", "", $imageval);
                            ?>

                            <div class="col-md-2 js_thumbDiv">
                                <div class="onefive"><img src="<?php echo $imageval; ?>" bigimg ="<?php echo $bigimg; ?>"  index="<?php echo $index; ?>" type="image" width="100" class="attached-item attached-item1 detail_img_<?php echo $index; ?>"  alt=""/> </div>
                            </div>
                            <?php
                            $index++;
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php } ?>
            <?php if (!empty($endorsedata["emojis_image"]) && $ndorser_anonymous != "anonymous") { ?>
                <div class="col-md-12" style="padding-left:0"> <span class="">
                        <h3 class="attach-img"> &nbsp;Attached Stickers</h3>
                    </span>
                    <div class="panel panel-default" style="padding:10px; max-height:275px; overflow:auto;">
                        <?php
                        $index = 1;
//                        pr($endorsedata["emojis_image"]);
//                        $emojiImage = json_decode($endorsedata["emojis_image"]);
//                        pr($emojiImage);
                        foreach ($endorsedata["emojis_image"] as $imageval) {
//                            $imageval = Router::url('/', true) . "app/webroot/" . BITMOJIS_IMAGE_DIR . $imageval;
//                            $imageval = str_replace("http", "https", $imageval);
//                            if (strpos($imageval, 'localhost') < 0 || strpos($imageval, 'staging') < 0) {
//                                $imageval = str_replace("http", "https", $imageval);
//                            }
                            ?>
                            <div class="col-md-2 js_thumbDiv" >
                                <div class="onefive"> <img src="<?php echo $imageval; ?>" bigimg ="<?php echo $imageval; ?>" index="<?php echo $index; ?>" type="emojis" width="100" class="attached-item attached-emojis detail_emojis_<?php echo $index; ?>" alt=""/> </div>
                            </div>
                            <?php
                            $index++;
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php } ?>

        </div>
        <div class="clearfix"></div>
    </section>

    <?php if (isset($endorsedata["endorse_reply_count"]) && $endorsedata["endorse_reply_count"] > 0) { ?>
        <section>
            <div class="nDorse-Details-msg">
                <h3>Reply</h3>
                <div class="mesg">
                    <?php if ($endorsedata["reply"] != "") { ?>
                        <p class="endorsed-reply"><strong><?php echo $endorsedata["endorsed_name"]; ?></strong> replied - <?php echo remove_emoji($endorsedata["reply"]); ?> </p>
                    <?php } ?>
                    <?php if ($endorsedata["reply_counter"] != "") { ?>
                        <p class="endorser-reply"><strong class="text-link"><?php echo $endorsedata["endorser_name"]; ?></strong> replied - <?php echo remove_emoji($endorsedata["reply_counter"]); ?> </p>
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php } ?>


    <?php if ($endorsedata["is_reply"] == 1) { ?>
        <div class="col-md-12">
            <div class="form-group"> <a href="javascript:void(0);" class="btn btn-orange-small" data-toggle="modal" data-target="#myModalreply">Reply</a> </div>
        </div>
    <?php } ?>

    <!-- Message section START -->
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center hidden toploader"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
            </div>
        </div>
        <div class="panel panel-default comment-section">
            <div class="like-count comment_container"> 
                <img class="like-img-post" post="<?php echo $endorsedata["id"]; ?>"  id="likes_<?php echo $endorsedata["id"]; ?>"
                     src="<?php echo Router::url('/', true); ?>img/like-comment.png" width="20" /> 

                <?php
                $likeCaption = " Like";
                if ($endorsedata['like_count'] > 1)
                    $likeCaption = " Likes";
                ?>
                <span class="range likers postlikeslist" style="cursor: pointer;" post="<?php echo $endorsedata["id"]; ?>" id="likes_range_<?php echo $endorsedata["id"]; ?>"> <?php echo $endorsedata['like_count'] . $likeCaption; ?></span>

                <?php
                if (isset($endorseCommentData['result']['data']) && !empty($endorseCommentData['result']['data'])) {
                    $totalPages = $endorseCommentData['result']['data']['total_pages'];
                    $currentPage = $endorseCommentData['result']['data']['current_page'];

                    if ($currentPage < $totalPages) {
                        $nextPage = $currentPage + 1;
                        ?>
                        <a href="javascript:void(0);" class="loadmorecomments" data-page-no="<?php echo $nextPage; ?>" data-post-id="<?php echo $endorsedata["id"]; ?>">Load More Comments</a>
                        <?php
                    }
                }
                ?>

                <hr />
                <?php
                if (isset($endorseCommentData['result']['data']) && !empty($endorseCommentData['result']['data']['endorsecommentlist'])) {
                    ?>    
                    <?php
                    foreach ($endorseCommentData['result']['data']['endorsecommentlist'] as $index => $postComment) {
                        $servertime = $endorseCommentData['result']['data']['server_time'];
                        $commentdate = date("M d", $postComment["created"]);
                        ?>
                        <div class="comment-detail"> 

                            <img alt="" class="img-circle " src="<?php echo $postComment['user_image']; ?>" width="50px" height="50px" align="left">
                            <div class="col-md-10">
                                <h4><?php echo $postComment['user_name']; ?></h4>
                                <h5><?php echo $postComment['comment']; ?></h5>
                                <h6 class="hours">
                                    <?php
                                    $createddate = new DateTime(date("Y-m-d H:i:s", $postComment["created"]));
                                    echo $this->App->getFeedTimeInterval($createddate, $servertime, $commentdate);
                                    ?>
                                </h6>
                            </div>

                            <div class="clearfix"></div>
                            <hr />
                        </div>
                        <?php
                    }
                }
                ?>

            </div>
            <div class="clearfix"></div>
            <?php echo $this->Form->create('endorse', array('class' => '')); ?>
            <div class="comment-detail"> 
                <?php echo $this->Form->input('endorsement_id', array('type' => 'hidden', 'name' => 'endorsement_id', 'value' => $endorsedata["id"])); ?>
                <img alt="" class="img-circle " src="<?php echo $loggeduserimage; ?>" width="50px" height="50px" align="left">
                <div class="col-md-11">
                    <textarea placeholder="Write a comment..." class="add-msg" name="message"  maxlength="3000" style="min-height: 45px;"></textarea>
                    <input type="button" class="submit-comment" value="Comment" />
                    <span class="empty-comment-err" style="margin-left: 1%; color: red;display: none;">Your comment is empty. Please enter a comment to post successfully.</span>
                </div>
                <div class="clearfix"></div>
            </div>
            <?php echo $this->Form->end(); ?> 
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center hidden js_Loader"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
            </div>
        </div>
    </section>
    <!-- Message section END -->


</div>
<div class="modal fade" id="myModalreply" tabindex="-1" role="dialog" 
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <!-- Modal Header -->

            <div class="modal-header">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <h3>Enter the text for reply</h3>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">

                <div class="form-group">
                    <?php //echo $this->Form->input('reply', array('placeholder' => 'Enter Your text Here...','class' => 'my-pro-textarea', 'label' => false,'type'=>'textarea'));     ?> 
                    <textarea id="reply" placeholder="Enter the text for reply"> </textarea>
                    <div id="replyerr" class="error" style="display:none;"></div>
                </div>

                <div class="clearfix"></div>
                <!-- Modal Footer -->

            </div>
            <div class="modal-footer">
                <button type="button" id="savereply" data-eid="<?php echo $endorsedata["id"]; ?>" class="btn btn-orange-small pull-left"> Submit </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myPhotoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <h3 style="margin-bottom: -20px;">Gallery</h3>
            </div>
            <div class="modal-body">                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<?php

function remove_emoji($text) {
    $text = preg_replace('/\\\\u[0-9A-F]{4}/i', '', $text);
    return $text;
}
?>
<script type="text/javascript">
    $(".character_counts").html($.trim($("#user_msg_val").val()).length + " Characters");

    $('#user_msg_val').bind('keyup', function (e) {
        $(".character_counts").html($.trim($(this).val()).length + " Characters");
    });
</script>