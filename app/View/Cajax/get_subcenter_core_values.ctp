<div class="col-md-6">
    <?php
//    pr($coreValues); exit;
    $totalCoreValues = count($coreValues); //exit;
    $halfCoreValues = ceil($totalCoreValues / 2);
    $count = 1;
//    pr($coreValues);exit;
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

<!-- Modal -->
<div class="modal fade cvText" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                <h4 class="modal-title customMSGModmodel" id="myModalLabel" ></h4>
            </div>
            <div class="modal-body customMSGModmodelBody" style="color: #333;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-orange" data-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-orange DNRBttn" data-id="0" > Do not remind me again</button>-->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

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