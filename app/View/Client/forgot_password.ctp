<div class="forgot-password">
    <div class="row">
        <div class="div-center">
            <h2>Enter Email Associated with Account:</h2>
            <?php echo $this->Form->create('Forgot', array('class' => '', 'id' => "forgotPasswordForm")); ?>
            <div class="form-group">
                <input type="text" placeholder="Username Or Email" id="fo_email" class="form-control" name="email">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-block btn-orange">Reset Password</button>
            </div>
            <?php echo $this->Form->end(); ?>

            <?php // echo $this->Form->create('Reset', array('class' => '', 'id' => "resetPasswordForm")); ?>
<!--            <input type="hidden"  id="re_email" class="form-control" name="email">
            <div class="form-group text-left">
                <span>Secret Code (From Email)</span>
                <input type="text" placeholder="Secret Code" id="re_verification_code" class="form-control" name="verification_code">
            </div>

            <div class="form-group text-left">
                <span>New Password: </span>
                <input type="password" placeholder="Password" id="re_password" class="form-control" name="password">
            </div>

            <div class="form-group text-left">
                <span>Confirm New Password: </span>
                <input type="password" placeholder="Confirm Password" id="re_confirm_password" class="form-control" name="confirm_password">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-block btn-orange">Change Password</button>
            </div>-->
            <?php // echo $this->Form->end(); ?>
        </div>
    </div>
</div>
<!--
<div class="form-group" >
<label >Forgot Password</label>
<input type="text" class="form-control" placeholder="Email" name="email"/>
</div>
<div class="form-group">
    <button class="btn  btn-orange btn-block" type="submit">Submit </button>
</div>-->
