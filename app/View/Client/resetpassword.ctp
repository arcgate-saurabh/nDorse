<div class="my-profile col-md-12">
    <?php if ($successmsg != "") { ?>
        <div id="flashmessage" class="msg text-center col-md-12" style="margin:10px 0"><?php echo $successmsg; ?></div>
    <?php } elseif ($errormsg != "") { ?>
        <div id="flashmessage" class="error text-danger text-center col-md-12"><?php echo $errormsg; ?></div>
    <?php } ?>
    <?php echo $this->Form->create('User', array("method" => "post")); ?>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Current Password :</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('current_password', array('type' => 'password', 'placeholder' => 'Current Password', 'value' => "", 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">New Password :</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('password', array('type' => 'password', 'placeholder' => 'New Password', 'value' => "", 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">Confirm Password :</div>
            </div>
            <div class="col-md-10">
                <div class="input"> <?php echo $this->Form->input('confirm_password', array('type' => 'password', 'placeholder' => 'Confirm Password', 'value' => "", 'class' => 'form-control', 'label' => false)); ?> </div>
            </div>
        </div>
    </section>
    <section class="">
        <div class="row">
            <div class="col-md-2">
                <div class="my-pro-label">&nbsp;</div>
            </div>
            <div class="col-md-10">
                <div class="input ">
                    <button type="button" class="btn btn-orange" id="changepasswordsubmit" >SAVE</button>
                </div>
            </div>
        </div>
    </section>
    <?php echo $this->Form->end(); ?> </div>
