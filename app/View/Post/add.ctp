<!--<script src="http://momentjs.com/downloads/moment.js"></script>-->
<!--<script src="http://momentjs.com/downloads/moment-timezone-with-data.js"></script>-->
<?php
//echo $org_user_role; 
echo $this->Html->script('momentNew');
echo $this->Html->script('moment_timezone_with_data');
?>
<div class="nDorse-process  col-md-12">
    <!--    <div class="marg-top"></div>-->
    <!-- Step 02 -->
    <?php
    echo $this->Form->create('endorsement', array('class' => '', 'enctype' => 'multipart/form-data'));
    echo $this->Form->input('usertimzone', array('value' => '', 'type' => 'hidden', 'id' => 'usertimzone', 'name' => 'usertimzone'));
    ?>
    <!-- Post to start-->
    <?php
//    echo $org_user_role;
//    pr($subcenterArray);
    if ($org_user_role == 'admin' || $org_user_role == 'elite') {
        ?>
        <section>
            <div class="row">
                <div class='col-sm-12 col-xs-12'>
                    <div class="col-md-6 Posting col-xs-12 ">
                        <span class="radio">
                            <div class="input radio">
                                <div class="col-md-4 col-xs-12 col-lg-6">
                                    <input type="radio" name="report_type" id="postnow" class="postclick" value="postnow" checked="checked">
                                    <label for="postnow">Post Now</label>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <input type="radio" name="report_type" class="postclick" id="postlater" value="postlater">
                                    <label for="postlater">Post Later</label>
                                </div>
                            </div>
                        </span>
                    </div>
                    <div class="date-pickers col-md-6 col-xs-12" style="display: none;">
                        <span class="pull-left col-md-2 date-time">Date & Time</span>
                        <div class="visible-xs clearfix"></div>
                        <div class='col-md-4'>
                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker3'>
                                    <input type='text' name="post_date" id="post_date" class="form-control" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker4'>
                                    <input type='text' name="post_time" id="post_time" class="form-control" />
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
            </div>
            <div class="search-icn" style="margin-top:10px;">
                <div class="row addFlexbox">
                    <div class='col-md-3'>
                        <div class="Push-Notifications">
                            <input id="alertshown" class="alertshown" value="0" type="hidden">
                            <input id="cvid" type="checkbox" class="OrgCvactivestatus css-checkbox push_notification" value="active" name="push_notification">
                            <label for="cvid" class="css-label txt-white">Send Push notification </label>
                        </div>
                    </div>
                    <div class='col-md-3'>
                        <div class="Push-Notifications">
                            <input id="alertshown1" class="alertshown1" value="0" type="hidden">
                            <input id="cvid1" type="checkbox" class="OrgCvactivestatus css-checkbox push_notification" value="active" name="email_notification">
                            <label for="cvid1" class="css-label txt-white">Send Email notification </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-4">
                        <div class="centerSubForm Push-Notifications">
                            <label>Facility:</label>
                            <select class="form-control" name="subcenter" id="subcenter">
                                <option value="0"><b>
                                    <?php echo $orgName; ?></b> (All)</option>
                                <?php
                                foreach ($subcenterArray as $id => $subcenter) {
                                    $selected = '';
                                    if ($user_subcenterID == $id) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                    <option <?php echo $selected; ?> value="
                                                                     <?php echo $id; ?>">
                                                                         <?php echo $subcenter; ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!--  <input type="text" placeholder="SEARCH EMPLOYEE OR DEPARTMENT..." class="form-control" value="">-->
                    <?php echo $this->Form->input('searchKey', array('placeholder' => "Search For Employee or Department", 'class' => "form-control", 'label' => false, 'value' => "")); ?> <span class="error" id="searchError"></span>
                    <div class="selected-values hidden">
                        <div class="col-md-11" id="selectedValues">
                        </div>
                        <div class="col-md-1 pull-right">
                            <button class="btn btn-clear-all js_clearAll" type="button">Clear All</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div id="nDorse-search" class="hidden">
                        <div class="" style="position:absolute; right:10px; top:10px;">
                            <button class="btn btn-xs btn-warning">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Post to end -->
    <?php } ?>
    <section>
        <div class="row">
            <h3>Add Title <span style="font-size:18px">(Optional)</span></h3>
            <div class="panel panel-default">
                <div class="col-md-12">
                    <input placeholder="Add Title..." class="add-msg" name="title" maxlength="1000" />
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
                    <textarea placeholder="Add Message..." id="message" class="add-msg" name="message" maxlength="3000"></textarea>
                </div>
                <div class="clearfix"></div>
            </div>
            <label class="error" id="validationError"></label>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="fileUpload">
                    <input type="file" class="upload" id="endorseImages" name="img" accept=".jpg,.png,.gif,.jpeg" multiple>
                    <label for="endorseImages" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/addClient.png" align="left" width="30" alt="" /> </label>
                </div>
                <h3 class="attach-img atch-img-tag" style=""> &nbsp;Attach Images</h3>
                <div class="panel panel-default hidden" id="imagePanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                    <div class="clearfix hidden-xs"></div>
                </div>
                <!-- <div class="clearfix hidden-xs"></div> -->
                <label class="error" id="validImageError"></label>
            </div>
            <div class="col-md-12 MT30">
                <div class="fileUpload">
                    <label data-toggle="modal" data-target=".js_emojis" class="hand addstickerplusicon">
                        <img src="<?php echo Router::url('/', true); ?>img/addClient.png" align="left" width="30" alt="" data-toggle="modal" data-target=".js_emojis" class="hand addstickerplusicon" />
                    </label>
                </div>
                <h3 class="attach-img atch-img-tag"> &nbsp;Attach Stickers</h3>
                <div class="panel panel-default MT30  hidden" id="stickerPanel" style="padding:10px;">
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- Add Files section start-->
            <div class="col-md-12 MT30" style="">
                <div class="fileUpload">
                    <input type="file" class="upload" id="postFiles" name="img" accept=".ppt,.pdf,.doc,.xls,.pptx,.docx,.xlsx,.ppt" multiple>
                    <label for="postFiles" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/addClient.png" align="left" width="30" alt="" /> </label>
                </div>
                <h3 class="attach-img" style=" display: inline; position:relative; top: -6px;"> &nbsp;Attach Files
                    <span class="allowed-files">(Allowed files types: doc, pdf, ppt, xls & Max total files size upto 10Mb.)</span>
                </h3>
                <!-- Running Code start DO NOT DELETE THIS CODE-->
                <div class="panel panel-default hidden" id="filesPanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                    <div class="clearfix hidden-xs"></div>
                </div>
                <!-- Running Code end-->
                <div class="clearfix hidden-xs"></div>
                <label class="error" id="validFileError"></label>
                <div class="clearfix"></div>
                <label class="error" id="validTotalFileError"></label>
            </div>
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
                <h4 class="modal-title"><strong>Select Stickers</strong></h4>
            </div>
            <div class="modal-body">
                <div class="addSticker">
                    <div class="sticker-content">
                        <?php if (!empty($emojis->custom)) { ?>
                            <div class="sticker-container-edit">
                                <?php
                                foreach ($emojis->custom as $emoji) {
                                    ?>
                                    <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>">
                                        <img src="<?php echo $emoji->url; ?>" class="attached-item" alt="">
                                    </div>
                                <?php } ?>

                            </div>
                            <hr/>
                        <?php } ?>
                        <div class="sticker-container-edit">
                            <?php
                            foreach ($emojis->default as $emoji) {
                                ?>
                                <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>">
                                    <img src="<?php echo $emoji->url; ?>" class="attached-item" alt="">
                                </div>
                            <?php } ?>

                        </div>

                    </div>
                </div>
                <?php /* foreach ($emojis->custom as $emoji) {
                  ?>
                  <div class="sticker-container" >
                  <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>">
                  <img src="<?php echo $emoji->url; ?>" class="attached-item" width="90" alt=""/>
                  </div>
                  </div>
                  <?php } ?>
                  <div class="clearfix"></div>
                  <?php
                  foreach ($emojis->default as $emoji) {
                  ?>
                  <div class="sticker-container" >
                  <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>"><img src="<?php echo $emoji->url; ?>" class="attached-item" width="90" alt=""/></div>
                  </div>
                  <?php } */ ?>
                <div class="clearfix"></div> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-orange pull-left js_selectEmojis">Done </button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<style>
    .fileUpload {
        position: relative;
        overflow: hidden;
        display: inline;
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
        display: hidden;
        filter: alpha(opacity=0);
    }
</style>
<script>
    $(function () {
        var dateNow = new Date();
        $('#datetimepicker4').datetimepicker({
            //            format: 'LT',
            format: 'HH:mm',
            defaultDate: dateNow
                    //            use24hours: true
                    //            minDate:moment()
        });
        $('#datetimepicker3').datetimepicker({
            viewMode: 'years',
            format: 'MM/DD/YYYY',
            minDate: dateNow,
            defaultDate: dateNow
                    //             minDate:moment()
        });

        $('.postclick').on("click", function () {
            var postType = $(this).val();
            if (postType == 'postnow') {
                $(".date-pickers").fadeOut('slow');
            } else { //postlater
                $(".date-pickers").fadeIn('slow');
            }
            console.log(postType);
        });
    });
    var emojiUrl = '<?php echo $emojiUrl; ?>';
    var usertimzone = moment.tz.guess();
    $("#usertimzone").val(usertimzone);

    $(".addstickerplusicon").on("click", function () {
        $(".js_addSticker").removeClass('js_stickerAdded');
    });
</script>