<?php
//pr($orgVideoListArray); exit;
///===================element to search data for guest endorsement
if (!empty($orgVideoListArray)) {
//    pr($orgVideoListArray);
    
    ?>
    <div class="clearfix"></div>
    <?php foreach ($orgVideoListArray as $index => $video) { ?>
        <section class="lady-lake farhan" id="video_section_<?php echo $video['id'];?>">
            <?php // pr($endorsement['status']);  ?>
            <div class="row">
                <div class="col-md-3 pull-left" >
                    <div class="col-md-8 text-center">
                        <a class="fancybox-media"  id="inline" href="#data_<?php echo $index;?>">

                            <?php
                            
                            $image = $this->Html->Image($video['thumbnail'], array("class" => "img-circle", "alt" => "64x64", "width" => "64px", "height" => "64px"));
                            ?>

                            <?php echo $image; ?> <!--<img alt="" data-src="holder.js/64x64" class="media-object" style="width: 64px; height: 64px;" src="img/user.svg" data-holder-rendered="true"> -->
                        </a>
                        <div class="far-user vid-url"><?php echo $video['video_url']; ?></div>
                        
                        <div class="post-thumb">
                            <div class="imgcontainer videocontainer">
                                <div style="display:none">
                                    <div id="data_<?php echo $index;?>" style="max-width:1024px;">
                                        <video width="100%" height="auto" controls>
                                            <source src="<?php echo $video['video_url']; ?>" type="video/mp4">
                                            <source src="<?php echo $video['video_url']; ?>" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="col-md-6 text-center" style="margin:20px 0;">
                    <?php if ($video['status'] == 1) { ?>
                        <a class="btn statusbttn btn-success btn-sm ml15 Pending active" data-name="Active" disabled="disabled" data-value="0">Showing</a>
                    <?php } else if ($video['status'] == 2) { ?>
                        <p class="btn statusbttn btn-danger btn-sm ml15 " >In-active</p>
                    <?php } else { ?>
                        <p class="btn statusbttn btn-danger btn-sm ml15 Rejected" data-name="In-Active" data-value="3">In-active</p>
                    <?php } ?>



                </div>

                <div class="col-md-3">
                    <div class="pull-right ">
                        <div class="col-md-6 col-sm-12">
                            <a href="javascript:void(0);" rel="<?php echo $video['id']; ?>_one" class="dots">
                                <?php echo $this->Html->Image("3dots.png", array("align" => "pull-right")); ?>
                            </a>
                            
                            <div class="arrow_box <?php echo $video['id']; ?>_one" style="position: absolute; right: -18px; z-index: 2; display: none;">
                                <div style="border:0px solid #f00; margin-top:-35px; margin-right:5px;" class="pull-right">
                                    <?php echo $this->Html->Image("popupArrow.png"); ?>
                                </div>
                                <ul>
                                    <?php if (isset($video['status']) && $video['status'] == 1) { ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="changeVideoStatus('<?php echo $video["org_id"]; ?>','<?php echo $video["id"]; ?>', 3)">In-Active</a>
                                    </li>
                                    <?php } ?>
                                    <?php if (isset($video['status']) && $video['status'] == 2) { ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeVideoStatus('<?php echo $video["org_id"]; ?>','<?php echo $video["id"]; ?>', 1)">Re-Publish</a>
                                        </li>
                                    <?php } ?>
                                    <?php if (isset($video['status']) && $video['status'] == 3) { ?>
                                        <li>
                                            <a href="javascript:void(0)" onclick="changeVideoStatus('<?php echo $video["org_id"]; ?>','<?php echo $video["id"]; ?>', 1)">Re-Publish</a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 text-center pull-right">
                        <?php //user_image
                         $video["user_image"];
//                        if ($endorsement["type"] == "anonymous") {
//                            $namedetailkey["User"]["lname"] = "****";
//                        } else {
//                            if (isset($userdetails[$endorsement["endorser_id"]])) {
//                                $namedetailkey = $userdetails[$endorsement["endorser_id"]];
//                                $imageuser = $userdetails[$endorsement["endorser_id"]];
//                                $namedetailkey["User"]["image"] = $imageuser["User"]["image"];
//                            }
//                        }

                        if ($video["user_image"] != "" && file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $video["user_image"])) {
                            $profile_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $video["user_image"];
                            $image = $this->Html->image($profile_imagenew, array('width' => '64', 'height' => '64', 'id' => 'org_image', 'class' => 'img-circle no-hand'));
                        } else {
                            $image = $this->Html->Image("user.png", array("class" => "text-center no-hand", "alt" => "32*32", "width" => "64", "height" => "64"));
                        }
                        echo $image;
                        ?>
                        <div class="clearfix"></div>
                        <span class="nodorsedby">Posted by </span><br />
                        <span class="rohan-col">
                            <?php
                            echo ucfirst($video['uploaded_by_name']);
                            ?>
                        </span></div>
                </div>
            </div>
            <div class="row">
                
                <div class="col-md-12 orange-bg">
                    
                    <?php $likeword = ""; ?>
                    <div class="col-md-3 pull-left">
                        <div class="col-md-8 text-center">
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <h4 class="" style="margin:4px 0">
                            <?php
                            //=========calculating time difference from present time.
                            $createddate = new DateTime($video["created"]);
                            $now = new DateTime();
                            $timediff = (array) $now->diff($createddate, true);
                            $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second",);
                            foreach ($timediff as $key => $difference) {
                                if ($difference != 0) {
                                    $diffkey = $arraytimediff[$key];
                                    if ($key == "h" || $key == "i" || $key == "s") {
                                        $plural = ($difference <= 1) ? "" : "s";
                                        echo $difference . " " . $diffkey . $plural . " ago";
                                    } else {
                                        echo date("M d", strtotime($video["created"]));
                                    }
                                    break;
                                }
                            }
                            ?>
                        </h4>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-10 text-center">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
?>
<!--<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="">
        <div class="modal-content" align="center">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="modal-title">ARE YOU SURE YOU WANT TO DELETE?</h4>
                <p>This will lorem ipsum text dolor a sit</p>
            </div>
            <div class="modal-footer">
                <div class="text-center"><button onclick="" type="button" class="btn btn-primary btn-blue">Yes</button>
                    <button type="button" class="canceldelete btn btn-primary btn-blue">No</button>
                </div>
            </div>
        </div>
    </div>
</div>-->