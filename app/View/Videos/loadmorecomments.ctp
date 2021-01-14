<?php
if (isset($postCommentData['postcommentlist']) && !empty($postCommentData['postcommentlist'])) {
    foreach ($postCommentData['postcommentlist'] as $index => $postComment) {
        $servertime = $postCommentData['server_time'];
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
<script>
    $(document).ready(function () {
        var total_page = "<?php echo $postCommentData['total_pages']; ?>";
        var current_page = parseInt("<?php echo $postCommentData['current_page']; ?>");
        var next_page = parseInt(current_page + parseInt(1));
        if (current_page == total_page || current_page > total_page) {
            $('.loadmorecomments').addClass('hidden');
        } else {
            $('.loadmorecomments').attr('data-page-no', next_page);
        }
    });
</script>