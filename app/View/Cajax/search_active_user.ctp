<?php
//pr($searchResult);
//exit;
$activeuserdata = isset($searchResult['user']) ? $searchResult['user'] : array();
?>
<script>
    totaluserpages = '<?php echo $searchResult['total_pages']; ?>';
</script>
<?php
if (isset($activeuserdata) && count($activeuserdata) > 0) {
    foreach ($activeuserdata as $users) {
        $ifEmpty = false;
        $user_image = Router::url('/', true) . "img/user.png";
        $user_image = "user.png";
        if (isset($users['image']) && $users['image'] != '') {
            $user_image = $users['image'];
        }
        $lastSeenDate = date("M d", $users["last_used_date"]);
        $activeStatus = 'offline';

        if (isset($users["last_used_date"]) && $users["last_used_date"] == 0) {
            $activeStatus = 'away';
        } else if (isset($loginTimeDiffrance) && $loginTimeDiffrance < 3) {
            $activeStatus = 'online';
        }
        ?>
        <section id="" class="userlist">
            <div class="Dear-Details" id="" post_id="">
                <div class="Name-Post "> 
                    <div class="namenimg">
                        <div class="col-md-4">
                            <span class="<?php echo $activeStatus; ?>"></span>
                            <?php
                            $user_profile = Router::url('/', true) . "img/user.png";
                            ?>
                            <?php
                            echo $this->Html->image($user_image, array('class' => 'img-circle hand show-user-profile', 'width' => '50px', 'height' => '50px',
                                'align' => 'left', 'data-user-id' => $users["id"], 'title' => $users["name"], 'data-logged-in' => $users["id"]));
                            ?>
                            <h4 class="range"><?php echo ucfirst($users["name"]); ?> </h4>
                            <h5><?php echo $users["about"]; ?></h5>
                            <h6><?php echo $users["dept_name"]; ?></h6>
                        </div>
                        <div class="col-md-4">
                            <h6 class="last-seen">Last Seen:
                                <?php
                                if (isset($users["last_used_date"]) && $users["last_used_date"] == 0) {
                                    echo "Not logged-in yet.";
                                } else {
                                    $servertime = $users['curr_time'];
                                    $createddate = new DateTime(date("Y-m-d H:i:s", $users['last_used_date']));
                                    echo $this->App->getFeedTimeInterval($createddate, $servertime, $lastSeenDate);
                                }
                                ?></h6>
                        </div>
                        <div class="col-md-4">
                            <a href="javascript:void(0);" data-toggle="modal" data-target=".endorse-now-popupmodel">
                                <?php echo $this->Html->image('nDorse-now.png', array('class' => 'show-options111', 'align' => 'right', 'style' => 'height: 35px')) ?>
                            </a>
                            <div class="clearfix"></div>
                        </div>
                        <?php
                        $following = $unfollowing = '';
                        if ($users['is_following']) {
                            $unfollowing = 'hidden';
                        } else {
                            $following = 'hidden';
                        }
                        ?>

                        <div class="userFollow follow <?php echo $following; ?>" id="following_<?php echo $users["id"]; ?>"  data-attr="following" data-user-id = '<?php echo $users["id"]; ?>'></div>
                        <div class="userFollow unfollow <?php echo $unfollowing; ?>" id="unfollowing_<?php echo $users["id"]; ?>" data-attr="unfollowing" data-user-id = '<?php echo $users["id"]; ?>'></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </section>

        <?php
    }
} else {
    echo '<div class="no-data-search">No Data available</div>';
}
?>