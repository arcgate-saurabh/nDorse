<?php
//print_r($postdate);
?>
<script type="text/javascript">
    var endorsetype = "public";
    var totalendorsepage = '<?php echo $endorsepage; ?>';
    var endorsepage = 2;

</script>
<?php if (!empty($postdata)) { ?>

    <div class="col-md-12">
        <div class="row"><section id="endorsementlist" style="margin-top:-20px;">
                <?php
                foreach ($postdata as $pendingData) {
                    //pr($loggedinUser['image']);
                    $userImage = $loggedinUser['image'];

                    $userName = $loggedinUser['fname'] . " " . $loggedinUser['lname'];
                    $logged_user_id = $loggedinUser['id'];
                    //exit;
                    //pr($pendingData); exit;
                    //exit;
                    ?>
                    <!-- Post_In_Feed section added by javed on 26-dec,2016 -->

                    <div class="Dear-Details" id="feed_<?php echo $pendingData['Post']["id"]; ?>" post_id ="<?php echo $pendingData['Post']["id"]; ?>" >
                        <div class="Name-Post " > 
                            <div class="namenimg" >
                                <img alt="" class="img-circle hand show-user-profile" src="<?php echo $userImage; ?>" width="50px" height="50px" align="left" title="<?php echo $userName; ?>" data-user-id="<?php echo $loggedinUser["id"]; ?>" data-logged-id="<?php echo $logged_user_id; ?>">
                                <h4 class="range"><?php echo $userName; ?></h4>
                                <div class="menu-down"><?php echo $this->Html->image('menu-down.png', array('class' => 'show-options', 'align' => 'right')) ?>
                                    <div class="clearfix"></div>
                                    <div class="menu-cont">
                                        <ul>
                                            <!--                                                    <a href="javascript:void(0);">-->
                                            <li class="delete-post hand delete-post-from-feed"  data-post-id="<?php echo $pendingData['Post']['id']; ?>">Delete this post</li>
                                            <!--</a>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="data-url hand live-feeds-post" id="feed_<?php echo $pendingData['Post']["id"]; ?>" post_id ="<?php echo $pendingData['Post']["id"]; ?>">
                                <h3><?php echo remove_emoji($pendingData['Post']['title']); ?></h3>
                                <p><?php
                                    //echo $pendingData['message']; 
                                    if (isset($pendingData['message']) && $pendingData['message'] != '') {
                                        $message = remove_emoji($pendingData['message']);
                                        //$message = $pendingData['message'];
                                        $mystring = 'http';
                                        $pos = strpos($message, $mystring);
                                        $clickableData = make_clickable($message);
                                        echo $clickableData;
                                    }
                                    ?></p>
                                <div class="clearfix"></div>
                                <div class="detail-img">
                                    <?php /* if ($pendingData['imagecount'] > 0 || !empty($pendingData['post_image'])) {
                                      $count = 1;
                                      foreach ($pendingData['post_image'] as $index => $postImage) {
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
                                      <?php } */ ?>
                                    <p>Scheduled Time : <?php echo $pendingData['FeedTrans']['publish_date']; ?> </p>
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
                        if (isset($pendingData["is_like"]) && $pendingData["is_like"] > 0) {
                            $dataTarget = "";
                            $dataToggle = "";
                        }
                        ?>
                        <div class="orange-bg no-hand">
                            <span class="show-popup-flag show-me-popup-new_<?php echo $pendingData['Post']["id"]; ?>" data-toggle="modal" data-target="#one" class="likes like-img-post hand" post="<?php echo $pendingData['Post']["id"]; ?>" ></span> 
                            <div class="col-md-8 text-center"> <span>
                                    <?php
                                    //pr($pendingData[0]['curr_time']); exit;
                                    $servertime = $pendingData[0]['curr_time'];
                                    $post_date = date("M d", $pendingData[0]['create_date']);
                                    $createddate = new DateTime(date("Y-m-d H:i:s", $pendingData[0]['create_date']));
                                    //$createddate = $pendingData['PostSchedule']['created'];
//                                    echo $createddate; 
//                                    exit;
                                    echo $this->App->getFeedTimeInterval($createddate, $servertime, $post_date);
                                    ?>
                                </span> 
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php } ?>

            </section>

        </div>
        <div style="text-align: center" class="col-md-offset-2"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
    </div>
<?php } else { ?>
    <div class='no-data-nDorse' >No Data available</div>
<?php } ?>
<?php

function remove_emoji($text) {
    $text = preg_replace('/\\\\u[0-9A-F]{4}/i', '', $text);
    return $text;
}
?>