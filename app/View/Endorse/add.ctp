<div class="nDorse-process  col-md-12"> 
    <!--    <div class="marg-top"></div>--> 
    <!-- Step 02 --> 
    <input type="hidden" value="<?php echo $endorsementLimit; ?>" name="type" id="endorseLimit">
    <?php echo $this->Form->create('endorsement', array('class' => '')); ?>
    <section>
        <div class="row">
            <div class="max">*Max limit per month: <?php echo $endorsementLimit; ?> per employee <br />
                * Employees at limit are locked </div>
            <div class="selectedSubcenter" style="display: none;color: orange;"> You have selected user from Subcenter-"MGH". Now you can search users from this subcenter only.</div>
            <div class="search-icn" style="margin-top:10px;">
                <input type="hidden" value="<?php echo $type; ?>" name="type" id="endorseType">

<!--          <input type="text" placeholder="SEARCH EMPLOYEE OR DEPARTMENT..." class="form-control" value="">--> 
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
    <section>
        <div class="row"> 
            <!--        <h2>Select Core Values</h2>-->
            <div class="panel panel-default ndorse-cvalue">
                <div class="col-md-6">
                    <?php
//                    pr($coreValues);
                    $totalCoreValues = count($coreValues);
                    $halfCoreValues = ceil($totalCoreValues / 2);
                    $count = 1;
                    //pr($coreValues);exit;
                    foreach ($coreValues as $coreValue) {
//                        pr($coreValue->custom_message_enabled); 
                        //pr($coreValue);

                        $disabledUserArray = array();
                        $disabledUser = $coreValue->custom_message_disabled_user_id;


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
                        if (!in_array($user_id, $disabledUserArray)) {
                            if ($coreValue->custom_message_enabled == 1) {
                                //$modelBox = 'data-toggle="modal" data-target="#myModal"';
                                $modalOpen = 1;
                            }
                        }
//                        exit;
                        ?>
                        <div class="checkbox core-value">
                            <input type="checkbox"  value="<?php echo $coreValue->id; ?>" class="css-checkbox js_coreValue" data-model="<?php echo $modalOpen; ?>" id="corevalue_<?php echo $coreValue->id; ?>" name="corevalue[]">
                            <label for="corevalue_<?php echo $coreValue->id; ?>" data-id="<?php echo $coreValue->id; ?>" class="css-label core_value_check"  <?php echo $modelBox; ?>><?php echo $coreValue->name; ?> </label>
                            <span class="core_custom_message_text hide"><?php echo $coreValue->custom_message_text; ?></span>
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
            <label class="error" id="coreValueError" ></label>
        </div>
    </section>
    <!-- HashTag Section Start -->
    <?php if (!empty($hashtags)) { ?>
        <section class="hashTagClass">
            <div class="row">
                <h3>Add HashTags:</h3>
                <input type="hidden" value="" name="hashtags" id="hashtags">
                <div class="panel-default">
                    <?php foreach ($hashtags as $index => $hash) { ?>
                        <button type="button" name="" data-id="<?php echo $hash->id; ?>" id="hashtag_<?php echo $hash->id; ?>" class="btn btn-default btn-sm hashtag-btn">#<?php echo $hash->name; ?></button>
                    <?php } ?>
                </div>

                <span class="error" id="messageError"></span>
            </div>
        </section>
    <?php } ?>
    <!-- HashTag Section END-->


    <?php if ($allowComments == 1 && $type != "anonymous") { ?>
        <section>
            <div class="row">
                <h3><?php if ($optionalComments != 0) { ?> <?php } ?> Share Your Story / Send a Message <span style="font-size:18px">(Max. 3000 Characters)</span>:</h3><span class="character_counts">0 Character</span>

                <div class="panel panel-default">
                    <div class="col-md-12">
                        <textarea placeholder="Add Message..." id="user_msg" class="add-msg" data-min="<?php echo $endorseMessageMinLimit; ?>" name="message" data-optional="<?php echo $optionalComments; ?>"  maxlength="3000"></textarea>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <span class="error" id="messageError"></span>
            </div>
        </section>
    <?php } ?>
    <section>
        <div class="row">
            <?php if ($allowAttachments == 1 && $type != "anonymous") { ?>
                <div class="col-md-12" >
                    <div class="fileUpload">
                        <input type="file" class="upload" id="endorseImages" name="img" accept=".jpg,.png,.gif,.jpeg" multiple >
                        <label for="endorseImages" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/addClient.png"  align="left" width="30" alt=""/> </label>
                    </div>
                    <h3 class="attach-img atch-img-tag"> &nbsp;Attach Images</h3>
                    <div class="panel panel-default hidden" id="imagePanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                        <div class="clearfix"></div>
                    </div>
                    <!-- <div class="clearfix"></div> -->
                    <label class="error" id="validImageError" ></label>
                </div>
                <div class="col-md-12 MT30" >
                    <div class="fileUpload">
                        <label data-toggle="modal" data-target=".js_emojis" class="hand addstickerplusicon">
                            <img src="<?php echo Router::url('/', true); ?>img/addClient.png"  align="left" width="30" alt="" data-toggle="modal" data-target=".js_emojis" class="hand addstickerplusicon"/>
                        </label>
                    </div>
                    <h3 class="attach-img atch-img-tag"> &nbsp;Attach Stickers</h3>
                    <div class="panel panel-default  hidden" id="stickerPanel" style="padding:10px; margin-top: 15px;">
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } ?>
            <div class="col-md-12 MT30">
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
                <?php /* foreach ($emojis->default as $emoji) { ?>
                  <div class="sticker-container" >
                  <div class="sticker-img js_addSticker" rel="<?php echo $emoji->image; ?>"><img src="<?php echo $emoji->url; ?>" class="attached-item" width="90" alt=""/></div>
                  </div>
                  <?php } */ ?>
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


    $(".character_counts").html($.trim($("#user_msg").val()).length + " Characters");

    $('#user_msg').bind('keyup', function (e) {
        $(".character_counts").html($.trim($(this).val()).length + " Characters");
    });

    var emojiUrl = '<?php echo $emojiUrl; ?>';
    var endorsementLimit = '<?php echo $endorsementLimit; ?>';
    $(".addstickerplusicon").on("click", function () {
        $(".js_addSticker").removeClass('js_stickerAdded');
    });



    $(document).ready(function () {

        var selectedUserId = '<?php echo $selectedUserId; ?>';
        var selectedUsername = '<?php echo $selectedUsername; ?>';
        var selectUserSubcenterID = '<?php echo $selectUserSubcenterID; ?>';

//        alert(selectedUserId + " / " + selectedUsername + "/ " + selectUserSubcenterID);

        if (selectedUserId != '') {
            $('#nDorse-search').append('<div class="searched-values js_searched" data-endorsementfor="user" data-endorsedid="' + selectedUserId + '" data-subcenterid="' + selectUserSubcenterID + '"><span class="js_searchedName">' + selectedUsername + ' </span></div>');
            $('.js_searched').click();
            $('#nDorse-search').remove(".js_searched");
        }
        $(".js_coreValue").on('click', function () {
            var DataModalShow = $(this).attr('data-model');
            if (DataModalShow == 1) {
                if ($(this).is(":checked")) {
                    $('#myModal').modal('show');
                }
            }
        });


        $(".core_value_check").on('click', function () {
            $('.customMSGModmodel').html($(this).html());
            var customMsg = $(this).closest('.core-value').find('.core_custom_message_text').html();
            var coreValueID = $(this).attr('data-id');
            $(".customMSGModmodelBody").html(customMsg);
            $('.DNRBttn').attr('data-id', coreValueID);


        });

        /** Added by Babulal Prasad @17012019
         * Enable do not remind for this user core value custom message
         */
        $(document).on("click", ".DNRBttn", function () {
            var coreValueID = $(this).attr('data-id');
            $(document).find('#corevalue_' + coreValueID).attr('data-model', '0');
            $('#myModal').modal('hide');
//            return false;
            $.ajax({
                type: "POST",
                url: siteurl + 'cajax/setDoNotRemindCoreValue',
                data: {"core_value_id": coreValueID},
                success: function (data, textStatus, xhr) {
                    var jsonparser = $.parseJSON(data);
                    var status = jsonparser["result"]["status"];
                    if (status) {
                        $(document).find('#corevalue_' + coreValueID).attr('data-model', '0');
                        $('#myModal').modal('hide');
                    }
                },
            });

        });




    });

</script>

<!-- Modal -->
<div class="modal fade cvText" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                <h4 class="modal-title customMSGModmodel" id="myModalLabel"></h4>
            </div>
            <div class="modal-body customMSGModmodelBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-orange" data-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-orange DNRBttn" data-id="0" > Do not remind me again</button>-->
            </div>
        </div>
    </div>
</div>