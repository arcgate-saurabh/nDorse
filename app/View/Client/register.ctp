<?php
$registeredEmail = $this->Session->read('register');
echo $this->Element("iframeModal");
?>
<?php if ( !empty($registeredEmail)) { ?>
    <div class="congratulation">
        <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
        <div class=" col-md-12 text-center">
            <h2>A verification code is sent<br />
                to your email address.</h2>
            <br />
            <br />
            <h2>Please enter the code in the box<br />
                below to verify your email address.</h2>
            <?php echo $this->Form->create('Verification', array('class' => 'form-signin')); ?>
            <div class="form-group text-center div-center" style="margin:20px auto">
<!--                <input type="text" class="form-control" placeholder="Verification Code" />-->
                <?php if (isset($errorMsg)) { ?>
                    <label class="error"><?php echo $errorMsg; ?></label>
                <?php } ?>
                <?php echo $this->Form->input('verification_code', array('placeholder' => "Verification Code", 'class' => "form-control", 'label' => false)); ?>
            </div>
            <div class="checkbox div-center" style="text-align:left;">
<!--                <input type="checkbox"  value="" class="css-checkbox" id="cor08" name="cor08">-->
                <?php echo $this->Form->checkbox('acceptTnc', array('class' => 'css-checkbox', "hiddenField" => FALSE, 'label' => false)); ?>
                <label for="VerificationAcceptTnc" class="css-label i-accept"><a  class="showInIframe" id="showTerms" href="<?php echo Router::url('/', true); ?>client/tnc">I agree to End User License Agreement</a> </label>
            </div>
            <div class="form-group div-center">
                <button class="btn btn-orange btn-block" type="submit">Submit </button>
                <button class="btn btn-orange btn-block" type="button" id="sendTnc">Send End User License Agreement on email</button>
            </div>
            <?php echo $this->Form->end();?>
        </div>
    </div>
<?php } else { ?>

<div class="login">
        <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2>Register</h2>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-5">
                        <?php echo $this->Form->create('User', array('class' => 'form-signin')); ?>
                        <?php if (isset($errorMsg)) { ?>
                            <label class="error"><?php echo $errorMsg; ?></label>
                        <?php } ?>
                        <div class="form-group">
            <!--              <input type="text" class="form-control" id="email" placeholder="Email" />-->
                            <?php echo $this->Form->input('email', array('placeholder' => "Email", 'class' => "form-control", 'label' => false)); ?>
                        </div>
                        <div class="form-group">
            <!--              <input type="password" class="form-control" id="email" placeholder="Password" />-->
                            <?php echo $this->Form->input('password', array('placeholder' => "Password", 'class' => "form-control", 'label' => false)); ?>
                        </div>
                        <div class="form-group">
            <!--              <input type="password" class="form-control" id="email" placeholder="Confirm Password" />-->
                            <?php echo $this->Form->input('confirm_password', array('placeholder' => "Confirm Password", 'type' => 'password', 'class' => "form-control", 'label' => false)); ?>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-lg btn-primary btn-block" type="submit">Continue</button>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                    <div class="col-md-2 text-center visible-md visible-lg"> <img src="<?php echo Router::url('/', true); ?>img/or-login.png" alt="" /> </div>
                    <div class="col-xs-12 text-center hidden-lg hidden-md or-hr"> <img src="<?php echo Router::url('/', true); ?>img/or.png" class="img-resp" alt="" /></div>
                    <div class="col-md-5 col-sm-12 text-center">
                        <div class="visible-lg visible-md"><br /><br /><br /></div>
                        <!--<div class="form-group"> <a href="<?php echo $fbLoginUrl; ?>"><img src="<?php echo Router::url('/', true); ?>img/fb.png" alt="" class="img-resp" /></a> </div>-->
                        <div class="form-group"> <a href="<?php echo $gplusLoginUrl; ?>"><img src="<?php echo Router::url('/', true); ?>img/g+.png" alt="" class="img-resp" /></a> </div>
                        <!--<div class="form-group"> <a href="<?php echo $linkedinLoginUrl; ?>"><img src="<?php echo Router::url('/', true); ?>img/linkedin.png" alt="" class="img-resp" /></a> </div>-->
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 text-center dont-have">
                    <h4>Already have an account?</h4>
                    <div class="form-group">
                        <?php echo $this->Html->link('Sign In', Router::url('/', true) . 'client/login', array('class' => 'btn small btn-orange')); ?>
                        <div class="faq"><a class="showInIframe" id="showFaqs" href="<?php echo Router::url('/', true); ?>client/faq">FAQ</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>