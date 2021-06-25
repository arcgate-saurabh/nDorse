<?php
echo $this->Element("commonModal");
echo $this->Element("iframeModal");
$rootUrl = Router::url('/', true);
//$rootUrl = str_replace("http", "https", $rootUrl);
//Added by saurabh on 23/06/2021
//$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
?>
<div class="login ">
    <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>Log In</h2>
        </div>
        <div class="panel-body">
            <p><?php echo $this->Session->Flash(); ?></p>
            <div class="newLoginsec">
                <div>
                    <div class="form-group">
                        <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#modalADFSLoginForm">
                            Single Sign-On
                        </a> 
                    </div> 
                </div>
                <div>

                    <div class="text-center visible-lg visible-md"> <img src="<?php echo $rootUrl; ?>img/or-login-hor.png" alt="" /> </div>
                </div>
                <div>
                    <?php echo $this->Form->create('User', array('class' => 'form-signin')); ?>
                    <div class="form-group"> 
                      <!--            <input type="text" class="form-control" id="email" placeholder="Email" />-->
                        <?php if (isset($errorMsg)) { ?>
                            <label class="error"><?php echo $errorMsg; ?></label>
                        <?php } ?>
                        <?php echo $this->Form->input('email', array('placeholder' => "Email", 'class' => "form-control", 'label' => false)); ?> </div>
                    <div class="form-group"> 
                      <!--            <input type="password" class="form-control" id="email" placeholder="Password" />--> 
                        <?php echo $this->Form->input('password', array('placeholder' => "Password", 'class' => "form-control", 'label' => false)); ?> </div>
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary btn-block" type="submit">Log In</button>
                    </div>
                    <div class="checkbox"> <?php echo $this->Form->checkbox('rememberme', array('class' => 'css-checkbox', "hiddenField" => FALSE, 'label' => false)); ?>
                        <label class="css-label" for="UserRememberme">Remember Me</label>
                        <div class="pull-right text-right">
                            <div class="text-right"><a href="<?php echo Router::url('/', true) . "client/forgotPassword"; ?>" module="client" id="forgotPassword">Forgot Password?</a></div>
                            <div class="text-right"><a href="<?php echo Router::url('/', true) . "client/recoverUsername"; ?>" id="recoverUsername">Recover Username?</a></div>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 text-center dont-have">
                    <h4>Don't have an account yet?</h4>
                    <div class="form-group"> <?php echo $this->Html->link('Sign Up', Router::url('/', true) . 'client/register', array('class' => 'btn small btn-orange')); ?>
                        <div class="faq"><a class="showInIframe" id="showFaqs" href="<?php echo Router::url('/', true); ?>client/faq">FAQ</a></div>
                    </div>
                </div>
            </div>



<!-- <div class="form-group"> <a href="<?php echo $gplusLoginUrl; ?>"><img src="<?php echo Router::url('/', true); ?>img/g+.png" alt="" class="img-resp" /></a> </div> -->

        </div>
    </div>
</div>

<div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <?php
        echo $this->Form->create('Ldap', array('url' => 'ldaplogin'), array('class' => 'form-signin'));
        echo $this->Form->input('org_id', array('type' => 'hidden', 'name' => 'org_id', 'value' => 148));
        ?>
        <div class="modal-content login">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title">
                    <img src="<?php echo $rootUrl; ?>img/login-icon.png" alt="" /><br/>Active Directory Login</h4>

            </div>
            <div class="modal-body mx-3">
                <div class="md-form mb-5 ">
                    <label class="css-label" style="color: black;" for="ldap-username">Your username</label>
                    <input type="username" id="ldap-username" name="username" class="form-control validate" placeholder="Username">
                    <span id="ldap-username-error" class="error"></span>
                </div>

                <div class="md-form mb-4">
                    <label class="css-label" style="color: black;" for="ldap-pass">Your password</label>
                    <input type="password" id="ldap-pass" name="password" class="form-control validate" placeholder="Password">
                    <span id="ldap-password-error" class="error"></span>
                </div>
                <div class="md-form mb-4">
                    <input type="button" class="btn btn-default activeDirectorySubmit btn-block"  value="Login"/>
                </div>
            </div>

        </div>
        <?php $this->form->end(); ?>
    </div>
</div>

<div class="modal fade" id="modalADFSLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <?php
        echo $this->Form->create('Ldap', array('url' => 'ldaplogin'), array('class' => 'form-signin'));
        echo $this->Form->input('org_id', array('type' => 'hidden', 'name' => 'org_id', 'value' => 148));
        ?>
        <div class="modal-content login">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title">
                    <img src="<?php echo $rootUrl; ?>img/login-icon.png" alt="" /><br/>Single Sign-On</h4>

            </div>
            <div class="modal-body mx-3">
                <div class="md-form mb-5 ">
                    <label class="css-label" style="color: black;" for="ldap-username">Enter Your Organization Code</label>
                    <input type="text" id="adfs-org-short-code" name="organization_short_code" class="form-control validate" placeholder="Enter Short Code">
                    <span id="adfs-org-short-code-error" class="error"></span>
                </div>

                <div class="md-form mb-4" style="margin-top: 15px;">
                    <a href="javascript:void(0);" id="adfs_login_link">
                        <input type="button" class="btn btn-default  btn-block hidden adfs_login_button"  value="Login" style="font-size: 20px;" />
                    </a>
                </div>
            </div>

        </div>
        <?php $this->form->end(); ?>
    </div>
</div>

<!--<div class="text-center">
    <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#modalLoginForm">Launch
        Modal Login Form</a>
</div>-->