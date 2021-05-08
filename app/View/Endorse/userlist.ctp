<script type="text/javascript">
    var totaluserpages = '<?php echo $total_pages; ?>';
    var pagenumber = 2;
</script>
<?php if (!empty($activeuserdata)) {
    ?>
    <div class="col-md-12">
        <section>
            <div class="row">
                <div class="search-icn ">
                    <input type="text" placeholder="Search users" id="searchendorsements" class="form-control">
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
                foreach ($activeuserdata as $users) {
//                    pr($users);
                    $user_image = Router::url('/', true) . "img/user.png";
                    $user_image = "user.png";
                    if (isset($users['image']) && $users['image'] != '') {
                        $user_image = $users['image'];
                    }

                    $loginTimeDiffrance = round(abs($users["last_used_date"] - $users["curr_time"]) / 60, 2);

                    $lastSeenDate = date("M d Y", $users["last_used_date"]);
                    $activeStatus = 'offline';

                    if (isset($users["last_used_date"]) && $users["last_used_date"] == 0) {
                        $activeStatus = 'away';
                    } else if (isset($loginTimeDiffrance) && $loginTimeDiffrance < 3) {
                        $activeStatus = 'online';
                    }

                    $endorser_name = $users["name"];
                    $ndorser_anonymous = "user";
                    ?>
                    <section id="" class="userlist">
                        <div class="Dear-Details" id="" post_id="">
                            <div class="Name-Post "> 
                                <div class="namenimg pos-rel">
                                    <div class="col-md-4 col-xs-12">
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
                                    <div class="col-md-4 col-xs-12 ">
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
                                    <div class="right-element">
                                        <a href="javascript:void(0);" data-toggle="modal" data-target=".endorse-now-popupmodel" data-userid="<?php echo $users['id']; ?>" class="active-userlist-endorse">
                                            <?php echo $this->Html->image('nDorse-now.png', array('class' => 'show-options111', 'align' => 'right','style'=> 'height: 35px')) ?>
                                        </a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="userFollow follow" title="unfollow">
                                        
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </section>

                    <?php
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

function remove_emoji($text) {
    $text = preg_replace('/\\\\u[0-9A-F]{4}/i', '', $text);
    return $text;
}
?>
<script>
    $(document).ready(function () {

        $('.active-userlist-endorse').on('click', function () {
            var userID = $(this).attr('data-userid');
            
            
            
            $(document).find("#selected_user_id").val(userID);
            
        });


//        var publicnDorseVisible = "<?php // echo $endorse['public_endorse_visible'];                           ?>";
//        if (publicnDorseVisible == 0) {
//            $(document).find(".my-nDorse-btn").removeClass('ndorseNowBtn');
//        }
    });
</script>
