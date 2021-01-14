<div class="nDorse-process  col-md-12"> 
    <!--    <div class="marg-top"></div>--> 
    <!-- Step 02 --> 
    <input type="hidden" value="<?php echo $endorsementLimit; ?>" name="type" id="endorseLimit">
    <?php echo $this->Form->create('endorsement', array('class' => '')); ?>
    <section>
        <div class="row">
            <h3>Optional Message to Send <span style="font-size:18px">(Max. 3000 Characters)</span>:</h3>
            <div class="panel panel-default">
                <div class="col-md-12">
                    <textarea placeholder="Add Message..." class="add-msg" name="message"  maxlength="3000"></textarea>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>

    <section>
        <div class="row">

            <div class="col-md-12" >
                <div class="fileUpload">
                    <input type="file" class="upload" id="endorseImages" name="img" accept=".jpg,.png,.gif,.jpeg" multiple >
                    <label for="endorseImages" style="margin:0 0; padding:0 0"> <img src="<?php echo Router::url('/', true); ?>img/addClient.png"  align="left" width="30" alt=""/> </label>
                </div>
                <h3 class="attach-img" style=" display: inline; position:relative; top: -6px;"> &nbsp;Attach Images</h3>
                <div class="panel panel-default hidden" id="imagePanel" style="padding:10px; max-height:275px; overflow:auto;margin:5px 0;">
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
                <label class="error" id="validImageError" ></label>
            </div>
            <div class="col-md-12" > <span><img src="<?php echo Router::url('/', true); ?>img/addClient.png"  align="left" width="30" alt="" data-toggle="modal" data-target=".js_emojis" class="hand"/>
                    <h3 class="attach-img"> &nbsp;Attach Stickers</h3>
                </span>
                <div class="panel panel-default  hidden" id="stickerPanel" style="padding:10px;">
                    <div class="clearfix"></div>
                </div>
            </div>

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
                <?php foreach ($emojis as $emoji) { ?>
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
    var emojiUrl = '<?php echo $emojiUrl; ?>';
    var endorsementLimit = '<?php echo $endorsementLimit; ?>';
</script>