<?php
//pr($postdata); //exit;                        

// $the_date = strtotime("2017-12-28 19:30:00");
//echo(date_default_timezone_get() . "<br />");
//echo(date("Y-d-mTG:i:sz",$the_date) . "<br />");
//echo(date_default_timezone_set("UTC") . "<br />");
//echo(date("Y-d-mTG:i:sz", $the_date) . "<br />");
// exit;
//echo $temp = cnvt_usrTime_to_UTC("2017-12-12 17:30:00", 'UTC');
//
//function cnvt_usrTime_to_UTC($dt_start_time_formate, $UTC_TimeZone) {
//
//        $LocalTime_start_time = new DateTime($dt_start_time_formate);
//        $tz_start = new DateTimeZone($UTC_TimeZone);
//        $LocalTime_start_time->setTimezone($tz_start);
//        $array_start_time = (array) $LocalTime_start_time;
//
//        return $UTC_Time_Start_Time = $array_start_time['date'];
//    }
//
//
//exit;

?>
<script src="http://momentjs.com/downloads/moment.js"></script>
<script src="http://momentjs.com/downloads/moment-timezone-with-data.js"></script>
<div class="nDorse-process  col-md-12"> 
    <!--    <div class="marg-top"></div>--> 
    <!-- Step 02 --> 
    <?php echo $this->Form->create('endorsement', array('class' => '', 'enctype' => 'multipart/form-data')); ?>
    <!-- Post to start-->  
    <?php
    if ($org_user_role == 'admin') {
        echo $this->Form->input('post_id', array('value' => $postdata['id'], 'type' => 'hidden', 'name' => 'post_id'));
        echo $this->Form->input('post_schedule_id', array('value' => $postdata['PostSchedule']['id'], 'type' => 'hidden', 'name' => 'post_schedule_id'));
        echo $this->Form->input('feed_trans_id', array('value' => $postdata['FeedTran']['id'], 'type' => 'hidden', 'name' => 'feed_trans_id'));
        echo $this->Form->input('usertimzone', array('value' => '', 'type' => 'hidden','id' => 'usertimzone', 'name' => 'usertimzone'));
        ?>
        <section>
            <div class="row">
                <div class='col-sm-12'>
                    <div class="col-md-6 Posting">
                        <span class="radio">
                            <div class="input radio">
                                <?php
                                $postLaterChecked = $postnowChecked = "";
                                if ($postdata['FeedTran']['visibility_check'] == 1) {
                                    $postLaterChecked = "checked='checked'";
                                } else {
                                    $postnowChecked = "checked='checked'";
                                }
                                ?>
                                <div class="col-md-4">
                                    <input type="radio" name="report_type" id="postnow" class="postclick"  value="postnow" <?php echo $postnowChecked; ?> >
                                    <label for="postnow">Post Now</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" name="report_type" class="postclick" id="postlater" value="postlater" <?php echo $postLaterChecked; ?> >
                                    <label for="postlater">Post Later</label>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="date-pickers col-md-6">
                        <span class="pull-left col-md-2" style="top: 5px; text-align:left;">Date & Time</span>
                        <div class='col-md-4'>

                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker3'>
                                    <?php
                                    $formateToPutInInput = date("m/d/Y", time());
                                    if (isset($postdata['PostSchedule']['date']) && $postdata['PostSchedule']['date'] != '') {
                                        $formateToPutInInput = formatToPutInInput($postdata['PostSchedule']['date']);
                                    }
                                    ?>
                                    <input type='text' name="post_date" id="post_date" class="form-control" value="<?php echo $formateToPutInInput; ?>" />

                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker4'>
                                    <input type='text' name="post_time" id="post_time" class="form-control" value="<?php echo $postdata['PostSchedule']['time']; ?>" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="error col-md-offset-2 datetimeerror" style="display: none;">*Please select date and time to post</span>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="search-icn" style="margin-top:10px;">
                    <div class="Push-Notifications">
                        <?php
                        $pushChecked = "";
                        if ($postdata['push_notification'] == 1) {
                            $pushChecked = "checked='checked'";
                        }
                        ?>
                        <input id="alertshown" class="alertshown" value="0" type="hidden">
                        <input id="cvid" type="checkbox" class="OrgCvactivestatus css-checkbox push_notification" <?php echo $pushChecked; ?> value="active" name="push_notification">
                        <label for="cvid" class="css-label txt-white">Send Push notification </label>
                    </div>
                      <!--          <input type="text" placeholder="SEARCH EMPLOYEE OR DEPARTMENT..." class="form-control" value="">--> 
                    <?php //echo $this->Form->input('searchKey', array('placeholder' => "Search For Employee or Department", 'class' => "form-control", 'label' => false, 'value' => ""));      ?> <span class="error" id="searchError"></span>
                    <?php
                    if (isset($visibleFiltersTags) && count($visibleFiltersTags) > 0) {
                        ?>
                        <div class="selected-values">
                            <div class="col-md-11" id="selectedValues">
                                <?php
                                foreach ($visibleFiltersTags as $index => $filterDATA) {
                                    foreach ($filterDATA as $ID => $value) {
                                        ?>
                                        <span class="js_selectedValue" data-endorsedid="<?php echo $ID; ?>" data-endorsementfor="entity"><?php echo $value; ?></span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-md-1 pull-right"> </div>
                            <div class="clearfix"></div>
                        </div>
                    <?php } ?>

                    <!--                    <div class="selected-values hidden">
                                            <div class="col-md-11" id="selectedValues">
                    
                    
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <button class="btn btn-clear-all js_clearAll" type="button">Clear All</button>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>-->
                    <div id="nDorse-search" class="hidden">
                        <div class="" style="position:absolute; right:10px; top:10px;">
                            <button class="btn btn-xs btn-warning">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!----- Post to end ----->  
    <?php } ?>
    <section>

        <div class="row">
            <h3>Add Title <span style="font-size:18px">(Optional)</span></h3>
            <div class="panel panel-default">
                <div class="col-md-12">
                    <input placeholder="Add Title..." class="add-msg" name="title"  maxlength="1000" value="<?php echo $postdata['title']; ?>"/>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <h3>Message<span style="font-size:18px"></span></h3>
            <div class="panel panel-default">
                <div class="col-md-12">
                    <textarea placeholder="Add Message..." id="message" class="add-msg" name="message" maxlength="3000"><?php echo $postdata['message']; ?></textarea>
                </div>
                <div class="clearfix"></div>
            </div>
            <label class="error" id="validationError" ></label>
        </div>
    </section>
    <section>
        <div class="row">

            <?php if (isset($postdata['attatched_image']) && count($postdata['attatched_image'])) { ?>
                <div class="col-md-12" >
                    <h3 class="attach-img" style=" display: inline; position:relative; top: -6px;"> &nbsp;Attached Images</h3>
                    <div class="panel panel-default" id="imagePanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                        <?php
                        if (isset($postdata['attatched_image']) && count($postdata['attatched_image'])) {
                            foreach ($postdata['attatched_image'] as $index => $imagePath) {
                                ?>
                                <div class="col-md-2 js_thumbDiv">
                                    <div class="onefive">
                                        <img src="<?php echo $imagePath; ?>" class="attached-item" alt="" id="thumb_0" width="100" height="100">                      
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <label class="error" id="validImageError" ></label>
                </div>
            <?php } ?>

            <?php if (isset($postdata['emojis_image']) && count($postdata['emojis_image'])) { ?>
                <div class="col-md-12" > <span>
                        <h3 class="attach-img"> &nbsp;Attached Stickers</h3>
                    </span>
                    <div class="panel panel-default" id="stickerPanel" style="padding:10px;">
                        <?php
                        if (isset($postdata['emojis_image']) && count($postdata['emojis_image'])) {
                            foreach ($postdata['emojis_image'] as $index => $emojis_imagePath) {
                                ?>
                                <div class="col-md-2 js_thumbDiv">
                                    <div class="onefive">
                                        <img src="<?php echo $emojis_imagePath; ?>" class="attached-item" alt="" id="thumb_0" width="100" height="100">                      
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php } ?>
            <!-- Add Files section start-->
            <?php
            if (isset($postdata['attatched_files']) && count($postdata['attatched_files'])) {
                ?>
                <div class="col-md-12" style="margin-top:8px;" >
                    <!--                <div class="fileUpload">
                                        <input type="file" class="upload" id="postFiles" name="img" accept=".ppt,.pdf,.doc,.xls,.pptx,.docx,.xlsx,.ppt" multiple >
                                        <label for="postFiles" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/addClient.png"  align="left" width="30" alt=""/> </label>
                                    </div>-->
                    <h3 class="attach-img" style=" display: inline; position:relative; top: -6px;"> &nbsp;Attached Files
    <!--                    <span class="allowed-files">(Allowed files types: doc, pdf, ppt, xls & Max total files size upto 10Mb.)</span>-->
                    </h3>

                    <!-- Running Code start DO NOT DELETE THIS CODE-->
                    <div class="panel panel-default" id="filesPanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                        <?php
                        if (isset($postdata['attatched_files']) && count($postdata['attatched_files'])) {
                            foreach ($postdata['attatched_files'] as $index => $filesArray) {
//                            pr($filesArray);
                                ?>
                                <div class="col-md-3 attach-files js_thumbDiv">
                                    <div class="attach-files-inner onefive">
                                        <!--                                    <div class="attach-files-close">
                                                                                <button class="btn btn-default btn-xs js_removeFiles" rel="1" type="button">X</button>
                                                                            </div>-->
                                        <span>
                                            <?php
                                            $extImg = '';
                                            switch ($filesArray['type']) {
                                                case "XLS":
                                                    $extImg = 'xls.png';
                                                    break;
                                                case "PPT":
                                                    $extImg = 'ppt.png';
                                                    break;
                                                case "DOC":
                                                    $extImg = 'doc.png';
                                                    break;
                                                case "PDF":
                                                    $extImg = 'pdf.png';
                                                    break;
                                                default:
                                                    break;
                                            }
                                            echo $this->Html->image($extImg, array('class' => 'no-hand'));
                                            ?>
                                        </span>
                                        <span class="file-name"><?php echo $filesArray['name']; ?></span>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                    <!-- Running Code end--> 

                    <div class="clearfix"></div>
                    <label class="error" id="validFileError" ></label>
                    <div class="clearfix"></div>
                    <label class="error" id="validTotalFileError" ></label>
                </div>
            <?php } ?>
            <!-- Add Files section End-->

            <div class="col-md-12">
                <button class="btn btn-orange" type="submit" id="endorseSubmit">Send</button>
                <div class="text-center hidden js_Loader"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
            </div>
        </div>
    </section>
    <?php echo $this->Form->end(); ?> 
    <!-- Step 02 --> 

</div>
<div class="modal fade bs-example-modal-lg nDorse-process js_emojis" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >Select Stickers</h4>
            </div>
            <div class="modal-body" style="max-height:250px; overflow:auto;">
                <?php foreach ($emojis as $emoji) {
                    ?>
                    <div class="sticker-container" >
                        <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>"><img src="<?php echo $emoji->url; ?>" class="attached-item" width="90" alt=""/></div>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-orange pull-left js_selectEmojis">Done </button>
            </div>
            <?php echo $this->Form->end(); ?> </div>
    </div>
</div>
<style>
    .fileUpload {
        position: relative;
        overflow: hidden;
        display:inline;
        /*margin: 10px; */
    }
    .fileUpload input.upload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        cursor: pointer;
        opacity: 0;
        display:hidden;
        filter: alpha(opacity=0);
    }
</style>
<script>
    $(function () {




        var post_date = $("#post_date").val();
        var post_time = $("#post_time").val();
        var dateNow = new Date(post_date);
        if (post_date.length > 0) {
            dateNow = new Date(Date.parse(post_date));
        }

//            var dateNow = new Date();
//        alert(dateNow);

        $('#datetimepicker4').datetimepicker({
//            format: 'LT',
            format: 'HH:mm',
            defaultDate: dateNow
//            use24hours: true
//            minDate:moment()
        });
        $('#datetimepicker3').datetimepicker({
            viewMode: 'months',
            format: 'MM/DD/YYYY',
//                minDate: dateNow,
            defaultDate: dateNow
//             minDate:moment()
        });

        $('.postclick').on("click", function () {
            var postType = $(this).val();
            if (postType == 'postnow') {
                $(".date-pickers").fadeOut('slow');
            } else {//postlater
                $(".date-pickers").fadeIn('slow');
            }
            console.log(postType);
        });

    });
    var emojiUrl = '<?php echo $emojiUrl; ?>';
    var usertimzone = moment.tz.guess();
    $("#usertimzone").val(usertimzone);

</script>
<?php

function formatToPutInInput($date) {

    $dateArray = explode("-", $date);

    $year = $dateArray['0'];
    $mnth = $dateArray['1'];
    $date = $dateArray['2'];

    return $mnth . "/" . $date . "/" . $year;
}
?>