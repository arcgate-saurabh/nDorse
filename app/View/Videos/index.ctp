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
        <section>
            <div class="row">
                <div class="search-icn ">
                    <input type="text" placeholder="Search nDorsements..." id="searchendorsements" class="form-control">
                    <div class="selected-values hidden"><div class="col-md-11" id="selectedValues"></div><div class="col-md-1 pull-right"><button class="btn btn-clear-all js_clearAll_endorse" type="button">Clear All</button></div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="livesearch"></div>
                </div>
            </div>

            <div class="col-md-12 tect-center hidden" style="text-align: center; margin-top: 10px;"><a class="btn btn-orange-small btn-xs js_newLiveFeeds" href="<?php echo Router::url('/', true); ?>endorse">New Updates</a></div>
            <div class="clearfix"></div>

        </section>
        <div class="row"><section id="endorsementlist" style="margin-top:-20px;">
                <?php
                foreach ($postdata as $endorse) {

                    $corevalue = "";
                    $endorser_image = $endorsed_image = Router::url('/', true) . "img/user.png";
//		if($endorse["endorsement_for"] == "department"){
//			$endorsed_image = Router::url('/', true)."img/department.png";
//		}elseif($endorse["endorsement_for"] == "entity"){
//			$endorsed_image = Router::url('/', true)."img/sub-org.png";
//			
//		}
                    //pr($endorse); exit;
                    if (isset($endorse["user_image"]) && $endorse["user_image"] != "") {
                        //echo $endorse["endorser_image"];
                        $user_image = explode("/", $endorse["user_image"]);
                        if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                            $endorser_image = $endorse["user_image"];
                        }
                    }

//			if(isset($endorse["endorsed_image"]) && $endorse["endorsed_image"]!="" ){
//			//	echo $endorse["endorsed_image"];
//			
//			$user_image = explode("/",$endorse["endorsed_image"]);
//			//echo WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1];
//			if(file_exists(WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image[count($user_image)-1])){
//				$endorsed_image = $endorse["endorsed_image"];
//			 }
//			}
                    //$endorse["corevalues"] =  $this->App->commoncorevaluesarrangement($endorse["corevalues"]);       
//                foreach($endorse["corevalues"] as $coreval){
//                 	if($corevalue!=""){
//			 $corevalue.="; ";	
//			}
//			if(trim($coreval["name"])!=""){
//				$corevalue.='<span style="color:'.$coreval["color_code"].';">'.trim($coreval["name"]).'</span>';
//			}
//		}


                    $endorsedate = date("M d", $endorse["created"]);
                    $readimg = "email.png";
                    if ($endorse["is_read"] > 0) {
                        $readimg = "open-env.png";
                    }
                    $likeimag = "like.png";
                    if ($endorse["is_like"] > 0) {
                        $likeimag = "liked.png";
                    }
                    $endorser_name = $endorse["user_name"];


                    $no_handclass = "";
//		if($endorse["endorsement_for"] == "department" || $endorse["endorsement_for"] == "entity"  ){
//			$no_handclass="no-hand";
//		}
                    ?>
                    <div class="live-feeds"  >
                        <div class="row hand">
                            <div class="live-feeds-ndorse" id="feed_<?php echo $endorse["id"]; ?>" post_id ="<?php echo $endorse["id"]; ?>">
                                <div class="col-md-2 text-center"> 
                                    <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user <?php echo $no_handclass; ?>"  src="<?php echo $endorser_image; ?>">
                                    <h5><?php echo ucfirst($endorse["user_name"]); ?> </h5>
                                </div>
                                <div class="col-md-8 text-center"><div class='feed-vertical'> <?php echo $corevalue; ?></div></div>
                                <div class="col-md-2 text-center"> 
                                    <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user no-hand" user_id="<?php echo $endorse["user_id"]; ?>"  src="<?php echo $endorser_image; ?>">
                                    <h5>nDorsed by<br />
                                        <span class="nDorsed-by"><?php echo ucfirst($endorser_name); ?></span> </h5>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="orange-bg no-hand">
                                <div class="col-md-2 text-center"> <a href="javascript:void(0)">

                                        <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img delete-img-post"></a>
                                        <span class="likes hand" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"]; ?> Like </span> </div>
                                        <!--<img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $likeimag; ?>" post="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_endorse_<?php echo $endorse["id"]; ?>" class="like-img like-img-post"></a><span class="likes like-img-endorse hand" endorse="<?php echo $endorse["id"]; ?>" like="<?php echo $endorse["is_like"]; ?>" id="likes_<?php echo $endorse["id"]; ?>"><?php echo $endorse["like_count"]; ?> Like </span> </div>-->

                                <div class="col-md-8 text-center"> 
                                    <span>

                                        <?php
                                        //=========calculating time difference from present time.

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
                                    </span> 
                                </div>
                                <div class="col-md-2 text-center" >
                                    <?php if ($endorse["is_reply"] > 0) { ?>
                                        <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/reply.png" class="marg-right no-hand" />
                                    <?php } ?>
                                    <?php if (($endorse["imagecount"] > 0 || $endorse["emojiscount"] > 0)) { ?>
                                        <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/attach.png" class="marg-right no-hand" />
                                    <?php } ?>
                                    <?php //if (trim($endorse["message"]) != "" && $ndorser_anonymous != "anonymous") { ?>
                                    <a href="javascript:void(0)"><img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/<?php echo $readimg; ?>" class="marg-right no-hand" /></a>
                                    <?php //}  ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
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
