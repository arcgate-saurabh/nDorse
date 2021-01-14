<?php
if(isset($endorseCommentData['EndorsementComment']) && !empty($endorseCommentData['EndorsementComment'])){
    //$servertime = $endorseCommentData['PostComment']['created'];
    //$commentdate = date("M d", $postComment["created"]);
    ?>
    <div class="comment-detail"> 
        <img alt="" class="img-circle " src="<?php echo $endorseCommentData['EndorsementComment']['user_image']; ?>" width="50px" height="50px" align="left">
        <div class="col-md-10">
            <h4><?php echo $endorseCommentData['EndorsementComment']['username']; ?></h4>
            <h5><?php echo $endorseCommentData['EndorsementComment']['comment']; ?></h5>
            <h6 class="hours">
                <?php
                //$createddate = new DateTime(date("Y-m-d H:i:s", $postComment["created"]));
                //echo $this->App->getFeedTimeInterval($createddate, $servertime, $commentdate);
                ?>
                few seconds ago
            </h6>
        </div>

        <div class="clearfix"></div>
        <hr />
    </div>
<?php }
?>