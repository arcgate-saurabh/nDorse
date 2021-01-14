<?php
//pr($userName); //exit;

echo $this->Element("commonModal");
echo $this->Element("iframeModal");
?>

<div class="login ">
    <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
    <div class="panel panel-default">
        <?php if (isset($errormsg) && $errormsg == '') { ?>
            <div class="panel-heading">
                <h2>Hi <?php echo $userName; ?>, Reset Your Password</h2>    
            </div>
        <?php } ?>
        <div class="panel-body">
            <p><?php echo $this->Session->Flash(); ?></p>
            <div class="col-md-12">
                <div class="col-md-12 col-sm-12"> 
                    <?php
                    echo $this->Form->create('User', array('class' => 'form-signin'));
                    if(isset($userId)){
                        echo $this->Form->input('id', array('type' => 'hidden', 'User.name' => 'id', 'value' => $userId));
                    }
                    ?>
                    <div class="form-group"> 
                        <?php if (isset($errormsg) && $errormsg != '') { ?>
                            <label class="error" style="text-align: center;"><?php echo $errormsg; ?></label>
                        <?php } ?>
                        <?php if (isset($errormsg) && $errormsg == '') { ?>
                            <?php echo $this->Form->input('new_password', array('placeholder' => "New Password", 'class' => "form-control", "id" => "new_password", 'label' => false,'type' =>'password')); ?> 
                        <?php } ?>   
                    </div>
                    <?php if (isset($errormsg) && $errormsg == '') { ?>
                        <div class="form-group"> 
                            <?php echo $this->Form->input('confirm_password', array('placeholder' => "Confirm Password", 'class' => "form-control", "id" => "confirm_password", 'label' => false,'type' =>'password')); ?> 
                        </div>

                        <div class="form-group">
                            <button class="btn btn-lg btn-primary btn-block" id="changepasswordsubmitset" type="submit">Reset</button>
                        </div>
                    <?php } ?>   
                    <?php echo $this->Form->end(); ?> </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 text-center dont-have">
                <h4>Don't have an account yet?</h4>
                <div class="form-group"> <?php echo $this->Html->link('Sign Up', Router::url('/', true) . 'client/register', array('class' => 'btn small btn-orange')); ?>
                    <div class="faq"><a class="showInIframe" id="showFaqs" href="<?php echo Router::url('/', true); ?>client/faq">FAQ</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
