<?php //echo $org_user_role;                     ?>
<div class="123nDorse-process  col-md-12">
    <!--    <div class="marg-top"></div>-->
    <!-- Step 02 -->
    <?php
    echo $this->Form->create('endorsement', array('class' => '', 'enctype' => 'multipart/form-data'));
    echo $this->Form->input('org_id', array('type' => 'hidden', 'value' => $org_details['id'], 'name' => 'org_id'));
    echo $this->Form->input('daisy_show_values', array('type' => 'hidden', 'value' => $org_details['daisy_show_core_values'], 'name' => 'daisy_show_values', 'id' => 'daisy_show_core_values'));
    echo $this->Form->input('source', array('type' => 'hidden', 'value' => 'web_app', 'name' => 'source'));
    echo $this->Form->input('department_unit_id', array('type' => 'hidden', 'value' => '', 'name' => 'department_unit_id'));
    ?>
    <!-- Post to start-->
    <?php
//    echo $org_user_role;
    if ($org_user_role == 'admin' || $org_user_role == 'elite') {
        ?>
        <section>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="daisy-logo">
                        <img src="<?php echo Router::url('/', true); ?>img/daisy_logo.png" alt="" />
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!--            <div class="search-icn" style="margin-top:10px;">
                                <h3>Name of the nurse you are nominating:</h3>
                                <h3>Name of the nurse whom you'd like to give thanks:</h3>
                                <h3>Name of nurse you are nominating:</h3>
                <?php // echo $this->Form->input('searchKey', array('id'=> 'daisySearchKey', 'placeholder' => "Search for Nurse", 'class' => "form-control", 'label' => false, 'value' => ""));  ?> <label class="error" id="searchError"></label>
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
                            </div>-->
            </div>

            <div class="row" style="margin-top:10px;">

                <div class="col-md-4" >
                    <h3>Name of nurse you are nominating:</h3>
                    <?php echo $this->Form->input('searchKey', array('id' => 'daisySearchKey', 'placeholder' => "First Name", 'class' => "form-control", 'label' => false, 'value' => "")); ?> 
                    <label class="error" id="searchError"></label>

                    <!--                <div class="selected-values hidden">
                                        <div class="col-md-11" id="selectedValues">
                                        </div>
                                        <div class="col-md-1 pull-right">
                                            <button class="btn btn-clear-all js_clearAll" type="button">Clear All</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>-->
                    <div id="daisy-search" class="hidden">
                        <div class="" style="position:absolute; right:10px; top:10px;">
                            <button class="btn btn-xs btn-warning">CLOSE</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h3>&nbsp;</h3>
                    <?php echo $this->Form->input('lastname', array('id' => 'endorse_lastname', 'placeholder' => "Last Name", 'class' => "form-control", 'label' => false, 'value' => "")); ?> 
                </div>
                <div class="col-md-4">
                    <h3>&nbsp;</h3>
                    <?php echo $this->Form->input('department_unit', array('id' => 'department_unit', 'placeholder' => "Department or Unit", 'class' => "form-control", 'label' => false, 'value' => "")); ?> 
                </div>
            </div>
            <?php if ($org_details['daisy_show_awards'] == 1) { ?>
                <div class="row">
                    <h3>Type of Award: </h3>
                    <select class="form-control" name="award_type">
                        <?php
                        $eactiveAwards = json_decode($org_details['daisy_active_awards'], true);
                        foreach ($DAISYAwards as $id => $award) {
                            if (in_array($id, $eactiveAwards)) {
                                ?>
                                <option value="<?php echo $id; ?>"><?php echo $award; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            <?php } ?>

            <div class="row">
                <div class="d-flexCenter">
                    <h3 class="mt0">Are you uploading a scanned nomination form?</h3>
                    <div class="col-md-6 mt15">
                        <span class="radio">
                            <div class="input radio">
                                <div class="col-md-3">
                                    <input type="radio" name="scanned_form" class="" id="scanned_form_1" value="1" >
                                    <label for="scanned_form_1">Yes</label>
                                </div>
                                <div>
                                    <input type="radio" name="scanned_form" id="scanned_form_2" value="0" checked="checked">
                                    <label for="scanned_form_2">No</label>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Post to end -->
    <?php } ?>
    <?php
    if ($org_details['daisy_show_comment_box'] == 1) {

        $daisyMessageLimit = $org_details['daisy_message_limit'];
        ?>

        <section>
            <div class="row">
                <div class="d-flexEnd">
                    <h3 class="pull-left">Share Your Story:<span style="font-size:18px"></span></h3>
                    <span class="character_counts pull-right">0 Character Count</span>
                </div>
                <div class="clearfix"></div>
                <div class="panel panel-default daisy-border">
                    <div class="col-md-12">
                        <textarea placeholder="Please tell us specifically how this nurse made a special difference in your experience as a patient or family member." id="user_msg" class="add-msg" data-min="<?php echo $daisyMessageLimit; ?>" name="message" data-optional="0"  maxlength="3000"></textarea>
                        <!--<textarea placeholder="Share Your Story..." id="user_msg" class="add-msg" name="message" maxlength="3000"></textarea>-->
                    </div>
                    <div class="clearfix"></div>
                </div>
                <label class="error" id="messageError"></label>
            </div>
        </section>
    <?php } ?>
    <?php if ($org_details['daisy_show_core_values'] == 1) { ?>
        <section>
            <div class="row">
                <h3>Select Core Values (If applicable):</h3>
                <div class="panel panel-default ndorse-cvalue daisy-border">
                    <div class="col-md-6">
                        <?php
                        $totalCoreValues = count($coreValues);
                        $halfCoreValues = ceil($totalCoreValues / 2);
                        $count = 1;
//                    pr($coreValues);exit;
                        foreach ($coreValues as $coreValue) {
//                        pr($coreValue->custom_message_enabled); 
                            //pr($coreValue);

                            $disabledUserArray = array();
                            $disabledUser = json_decode($coreValue['custom_message_disabled_user_id'], true);


                            if (!empty($disabledUser)) {
                                $disabledUserArray = $disabledUser;
                            }

//                        pr($disabledUserArray);
//                        pr($user_id); //exit;
                            $modelBox = "";
                            $modalOpen = 0;
                            if (empty($disabledUserArray)) {
                                $disabledUserArray = array();
                            }
//                            pr($disabledUserArray);
                            if (!in_array($user_id, $disabledUserArray)) {
                                if ($coreValue['custom_message_enabled'] == 1) {
                                    //$modelBox = 'data-toggle="modal" data-target="#myModal"';
                                    $modalOpen = 1;
                                }
                            }
//                        exit;
                            ?>
                            <div class="checkbox core-value">
                                <input type="checkbox" value="<?php echo $coreValue['id']; ?>" class="css-checkbox js_coreValue" data-model="<?php echo $modalOpen; ?>" id="corevalue_<?php echo $coreValue['id']; ?>" name="corevalue[]">
                                <label for="corevalue_<?php echo $coreValue['id']; ?>" data-id="<?php echo $coreValue['id']; ?>" class="css-label core_value_check" <?php echo $modelBox; ?>>
                                    <?php echo $coreValue['name']; ?> </label>
                                <span class="core_custom_message_text hide">
                                    <?php echo $coreValue['custom_message_text']; ?></span>
                            </div>
                            <?php
                            if ($count == $halfCoreValues) {
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                            }
                            $count++;
                        }
                        ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <label class="error" id="coreValueError"></label>
            </div>
        </section>
    <?php } ?>
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default hidden" id="imagePanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                    <div class="clearfix hidden-xs"></div>
                </div>
                <!-- <div class="clearfix hidden-xs"></div> -->
                <label class="error" id="validImageError"></label>
            </div>
            <!-- Add Files section start-->
            <div class="col-md-12 upload_document_section" style="display:none;">
                <div class="fileUpload">
                    <input type="file" class="upload" id="postFiles" name="img" accept=".ppt,.pdf,.doc,.xls,.pptx,.docx,.xlsx,.ppt" multiple>
                    <label for="postFiles" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/daisyAddClient.png" align="left" width="30" alt="" /> </label>
                </div>
                <h3 class="attach-img" style=" display: inline; position:relative; top: -6px;"> &nbsp;Upload Additional Documents
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
                <button class="btn btn-daisy" type="submit" id="endorseSubmit">Submit</button>
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
                <h4 class="modal-title">Select Stickers</h4>
            </div>
            <div class="modal-body" style="max-height:250px; overflow:auto;">
                <?php foreach ($emojis as $emoji) {
                    ?>
                    <div class="sticker-container">
                        <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>"><img src="<?php echo $emoji->url; ?>" class="attached-item" width="90" alt="" /></div>
                    </div>
                <?php } ?>
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
        $(".character_counts").html($.trim($("#user_msg").val()).length + " Characters");

        $('#user_msg').on('keyup', function (e) {
            $(".character_counts").html($.trim($(this).val()).length + " Characters");
        });

        $('input[type=radio][name=scanned_form]').change(function () {
            if (this.value == 1) {
                $('.upload_document_section').fadeIn();
            } else {
                $('.upload_document_section').fadeOut();
            }
        });

    });
</script>