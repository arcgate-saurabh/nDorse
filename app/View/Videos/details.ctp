<?php
//pr($postdata['id']); exit;
$posteduser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
if (isset($postdata["user_image"])) {
    $user_image = explode("/", $postdata["user_image"]);
    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
        $posteduser_image = Router::url('/', true) . PROFILE_IMAGE_DIR . "small/" . $user_image[count($user_image) - 1];
    }
}
//pr($postdata);
$createddate = date("m/d/Y", strtotime($postdata["created"]));
$post_date = date("M d", $postdata["created"]);
$ndorser_anonymous = "user";
$user_name = $postdata["user_name"];
$ndorser_anonymous = "user";
?>
<section class="Post-Details">
    <div class="Dear-Details"> 
        <img alt="" class="img-circle show-user-profile" src="<?php echo $posteduser_image; ?>" 
             data-user-id="<?php echo $postdata["user_id"]; ?>" data-logged-id="<?php echo $logged_user_id; ?>"
             width="50px" height="50px" align="left">
        <div class="col-md-10">
            <h4 class="range"><?php echo $postdata['user_name']; ?></h4>
            <h5><?php echo $postdata['user_job_title']; ?></h5>
            <h6>
                <?php
                $createddate = new DateTime(date("Y-m-d H:i:s", $postdata["created"]));
                $servertime = $postdata["server_time"];
                echo $this->App->getFeedTimeInterval($createddate, $servertime, $post_date);
                ?>

            </h6>
        </div>
        <div class="clearfix"></div>
        <div class="data-url">
            <h3><?php echo remove_emoji($postdata['title']); ?></h3>
            <p><?php
                //echo $postdata['message'];
                if (isset($postdata['message']) && $postdata['message'] != '') {
//                    $message = $postdata['message'];
                    $message = remove_emoji($postdata['message']);
                    $mystring = 'http';
                    $pos = strpos($message, $mystring);
                    $clickableData = make_clickable($message);
                    echo $clickableData;
                }
                ?></p>
        </div>
        <div class="clearfix"></div>
    </div>
</section>
<div class="nDorse-Details">
    <?php
    $postImages = array_merge($postdata["attatched_image"], $postdata["emojis_image"]);
    if (!empty($postImages)) {
        ?>
        <section class="">
            <div class="bo">
                <h3 class="attach-img"> &nbsp;Attached Images</h3>
                <div id="imagePanel" class="img-contnr panel panel-default" style="padding:0 10px; display:flex; overflow:scroll;overflow-y:hidden">
                    <?php
                    $index = 1;
                    foreach ($postImages as $imageval) {
                        $bigimg = str_replace("/small", "", $imageval);
                        ?>
                        <!--                        <div class="col-md-2 js_thumbDiv">-->
                        <div class="js_thumbDiv">
                            <div class="onefive">
                                <img src="<?php echo $imageval; ?>" bigimg ="<?php echo $bigimg; ?>"  index="<?php echo $index; ?>" type="image" width="100" class="attached-item attached-item1 detail_img_<?php echo $index; ?>"  alt=""/> 
                            </div>
                        </div>
                        <?php
                        $index++;
                    }
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
    <?php } ?>

    <?php
    $postFiles = $postdata["attatched_files"];
    if (!empty($postFiles)) {
        ?>
        <section class="">
            <div class="bo">
                <h3 class="attach-img"> &nbsp;Attached Files</h3>
                <div id="imagePanel" class="img-contnr panel panel-default" style="padding:10px;  overflow:auto;overflow-x:hidden">
                    <?php
                    $index = 1;
                    foreach ($postFiles as $file) {
                        ?>
                        <!--                        <div class="col-md-2 js_thumbDiv">-->
                        <div class="attach-files col-md-3">
                            <div class="attach-files-inner onefive">
                                <?php
                                //echo $file['type'] . "<br>";
                                //echo $this->Html->link('<i class="ion-android-download"></i> ' . $file['name'], array('controller' => 'post', 'action' => 'download', 'file' => $file['url_web'], 'filename' => $file['name']), array("class" => "downloadfile", 'escape' => false));
                                echo "<div class='download-file' data-id='".$postdata['id']."'>";
                                echo $this->Html->link(
                                        $this->Html->image('download.png'), array("controller" => "post", "action" => "download", 'file' => $file['url_web'], 'filename' => $file['name']), array('escape' => false)
                                );
//								echo $this->Html->image("download.png", url);
                                echo "</div>";
                                $fileType = $file['type'];
                                echo "<span>";
                                switch ($fileType) {
                                    case 'DOC':
                                        echo $this->Html->image('doc.png', array('class' => 'no-hand'));
                                        break;
                                    case 'PPT':
                                        echo $this->Html->image('ppt.png', array('class' => 'no-hand'));
                                        break;
                                    case 'PDF':
                                        echo $this->Html->image('pdf.png', array('class' => 'no-hand'));
                                        break;
                                    case 'XLS':
                                        echo $this->Html->image('xls.png', array('class' => 'no-hand'));
                                        break;

                                    default:
                                        break;
                                }
                                echo "</span>";
                                ?>
                                <span class="file-name" ><?php echo $file['name']; ?></span>
                            </div>
                        </div>
                        <?php
                        $index++;
                    }
                    ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>
    <?php } ?>

    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center hidden toploader"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
            </div>
        </div>
        <div class="panel panel-default comment-section">
            <div class="like-count comment_container"> 
                <img class="like-img-post" post="<?php echo $postdata["id"]; ?>" like="<?php echo $postdata["is_like"]; ?>" id="likes_<?php echo $postdata["id"]; ?>"
                     src="<?php echo Router::url('/', true); ?>img/like-comment.png" width="20" /> 

                <?php
                $likeCaption = " Like";
                if ($postdata['like_count'] > 1)
                    $likeCaption = " Likes";
                ?>
                <span class="range likers postlikeslist" style="cursor: pointer;" post="<?php echo $postdata["id"]; ?>" id="likes_range_<?php echo $postdata["id"]; ?>"> <?php echo $postdata['like_count'] . $likeCaption; ?></span>

                <?php
                
                if (isset($postCommentData['result']['data']) && !empty($postCommentData['result']['data'])) {
                    $totalPages = $postCommentData['result']['data']['total_pages'];
                    $currentPage = $postCommentData['result']['data']['current_page'];

                    if ($currentPage < $totalPages) {
                        $nextPage = $currentPage + 1;
                        ?>
                        <a href="javascript:void(0);" class="loadmorecomments" data-page-no="<?php echo $nextPage; ?>" data-post-id="<?php echo $postdata["id"]; ?>">Load More Comments</a>
                        <?php
                    }
                }
                ?>

                <hr />
                <?php
                
                if (isset($postCommentData['result']['data']) && !empty($postCommentData['result']['data']['postcommentlist'])) {
                    ?>    
                    <?php
                    foreach ($postCommentData['result']['data']['postcommentlist'] as $index => $postComment) {
                        $servertime = $postCommentData['result']['data']['server_time'];
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
            <?php echo $this->Form->create('post', array('class' => '')); ?>
            <div class="comment-detail"> 
                <?php echo $this->Form->input('post_id', array('type' => 'hidden', 'name' => 'post_id', 'value' => $postdata["id"])); ?>
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
                    <?php //echo $this->Form->input('reply', array('placeholder' => 'Enter Your text Here...','class' => 'my-pro-textarea', 'label' => false,'type'=>'textarea'));                ?>
                    <textarea id="reply" placeholder="Enter the text for reply"> </textarea>
                    <div id="replyerr" class="error" style="display:none;"></div>
                </div>
                <div class="clearfix"></div>
                <!-- Modal Footer --> 

            </div>
            <div class="modal-footer">
                <button type="button" id="savereply" data-eid="<?php echo $postdata["id"]; ?>" class="btn btn-orange-small pull-left"> Submit </button>
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
            <div class="modal-body"> </div>
        </div>
        <!-- /.modal-content --> 
    </div>
    <!-- /.modal-dialog --> 
</div>
<div class="modal fade likesmodel LikeListings" id="one" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">

</div>
<script>
    
</script>
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