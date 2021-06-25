<?php
echo $this->Element("commonModal");
echo $this->Element("iframeModal");
$rootUrl = Router::url('/', true);
//$rootUrl = str_replace("http", "https", $rootUrl);
//Added by saurabh on 23/06/2021
//$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
?>

<div class="banner">
    <div class="container" style="position: relative;">
        <div class="col-md-5">
            <span>
                <?php echo $this->Html->Image("/images/logo.png", array("width" => "138")); ?>
            </span>
            <h2 class="">WELCOME TO <br />
                THE REVOLUTION.</h2>
            <h4>nDorse is a mobile and web based application that allows real time positive reinforcement and feedback by saying something nice to your colleagues and friends.</h4>
            <span>
                <?php echo $this->Html->link($this->Html->Image("/images/get-app.png", array("alt" => "")), array("controller" => "site", "action" => "contact"), array("escape" => false)); ?>
            </span>
        </div>
        <div class="col-md-7"> <span class="mt20">
                <?php echo $this->Html->Image("/images/mob-hand.png", array("alt" => "")); ?></span>
            <div class="adm-rep-btn"></div>
        </div>
        <div class="login-right">
            <div class="login-form">

                <?php echo $this->Form->create('User', array('type' => 'post', 'url' => array('controller' => 'client', 'action' => 'home_login'), 'class' => 'form-signin')); ?>
                <div class="form-group">
                    <a href="" class="btn btn-default btn-rounded mb-4 ssouLink" data-toggle="modal" data-target="#modalADFSLoginForm">
                        Single Sign-On
                    </a> 
                </div> 
                <div>

                    <div class="text-center visible-lg visible-md"> <img src="<?php echo $rootUrl; ?>img/or-login-hor.png" alt="" /> </div>
                </div>
                <p><?php echo $this->Session->Flash(); ?></p>
                <div class="form-group">
                    <div class="input email">
                        <input name="User[email]" placeholder="Email" class="form-control" maxlength="500" type="email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input password">
                        <input name="User[password]" placeholder="Password" class="form-control" type="password">
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Log In</button>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="rememberme" class="css-checkbox" id="UserRememberme">
                        <label class="css-label" for="UserRememberme">Remember Me</label>
                        <div class="pull-right text-right">
                            <div class="text-right">
                                <a module="client" href="<?php echo Router::url('/', true) . "client/forgotPassword"; ?>" id="forgotPassword">Forgot Password?</a>
                            </div>
                            <div class="text-right">
                                <a href="<?php echo Router::url('/', true) . "client/recoverUsername"; ?>" id="recoverUsername">Recover Username?</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>


            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="container">
    <section class="real-time">
        <div class="text-center">
            <div class="col-md-4 ">
                <?php echo $this->Html->Image("/images/bage.png", array("alt" => "")); ?>
                <h3>THE ONLY APP YOU'LL EVER NEED TO KEEP YOUR WORK FORCE MOTIVATED</h3>
                <p class="text-left">Getting a pat on your back or knowing you made a small difference in someone's day - a positive reinforcement that what your doing matters goes a long way in keeping us motivated. <br />
                    <br />
                    nDorse allows good deeds and "star" people to be recognized. The reward is in the recognition! </p>
                <!--                <div class="home-contact">
                    <div class="pull-left"><?php //echo $this->Html->Image("/images/phone.png", array("alt" => "", "align" => "left"));                 ?></div>
                    <div class="pull-right text-left">
                        <h5><strong>Contact</strong></h5>
                        <p class="text-left">NDORSE LLC<br />
                            Email:  <a href="mailto:support@ndorse.net?Subject=" target="_top" >support@ndorse.net</a>
                            </p>
                    </div>
                    <div class="clearfix"></div>
                </div>-->
            </div>
            <div class="col-md-4 ">
                <?php echo $this->Html->Image("/images/edit.png", array("alt" => "")); ?>
                <h3>WHAT SETS US APART....</h3>
                <p class="text-left"><strong>REAL TIME</strong>, immediate and mobile based ability to endorse and acknowledge friends and colleagues.<br />
                    <br />
                    At work, nDorse allows acknowledgement in a sophisticated, efficient fashion of good deeds, skills or institutional core values visible to your friends, colleagues, and to institutional administration. </p>
                <div>
                    <iframe width="320" height="200" src="https://www.youtube.com/embed/UmAXQPVgkic" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-4 ">
                <?php echo $this->Html->Image("/images/work-force.png", array("alt" => "")); ?>
                <h3>nDorse FOR THE WORK FORCE!</h3>
                <p class="text-left">Institutions and companies employ positive reinforcement strategies to help employees understand their company goals and mission and to keep the work force motivated. <br />
                    <br />
                    Most leadership consider this an investment in their well-being of the institution and its employees.<br />
                    <br />
                    nDorse incorporates institutional objectives and provides a real time web based tool for positive reinforcement. <br />
                    <br />
                    Data analysis allows generation of several reports to help identify STAR employees and departments! </p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div>&nbsp;</div>
    </section>
</div>
<section class="follow-us">
    <div class="container">
        <div class="col-md-12 text-center">
            <?php echo $this->Html->Image("/images/follow-us.png", array("alt" => "")); ?>
            <div class="social"><a href="https://www.facebook.com/nDorsellc/" target="blank">
                    <?php echo $this->Html->Image("/images/fb.png", array("alt" => "")); ?></a>
                <a href="https://twitter.com/ndorsellc" data-show-count="false" target="blank">
                    <?php echo $this->Html->Image("/images/twitter.png", array("alt" => "")); ?></a>
                <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalADFSLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <?php
//        echo $this->Form->create('Ldap', array('url' => 'ldaplogin'), array('class' => 'form-signin'));
//        echo $this->Form->input('org_id', array('type' => 'hidden', 'name' => 'org_id', 'value' => 148));
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
        <?php // $this->form->end();  ?>
    </div>
</div>

<script>
    $(document).ready(function () {

        $("#UserHomeLoginForm").validate({
            rules: {
                'data[User][email]': {
                    required: true,
                    email: true,
                }
                ,
                'data[User][password]': {
                    required: true,
                    minlength: 8,
                }
            },
            messages: {
                'data[User][email]': {
                    required: "Email is required",
                    email: "Invalid email"
                },
                'data[User][password]': {
                    required: "Password is required",
                    minlength: "At least {8} character",
                },
            }

        });
    });

//Forgot password
    $(document).on("click", "#forgotPassword", function (e) {
        e.preventDefault();
        var module = $(this).attr("module");
        var url = siteurl + "client/forgotPassword";
        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                $("#commonModal .modal-title").html("Forgot Password");
                $("#commonModal .modal-body").html(response);
                $("#commonModal").modal("show");
                bindForgotPassword(module);
                bindResetPassword(module);
            },
            error: function (response) {
                alertbootbox(response);
            }
        });
    });

    function bindForgotPassword(module) {
        $("#forgotPasswordForm").ajaxForm({
            url: siteurl + module + "/forgotPassword",
            dataType: 'json',
            beforeSubmit: function () {
                return $("#forgotPasswordForm").valid();// TRUE when form is valid, FALSE will cancel submit
            },
            success: function (response) {
                alertbootbox(response.msg);

                if (response.success == true) {
//                $("#commonModal").modal("hide");
                }
            }
        });

        $("#forgotPasswordForm").validate({
            rules: {
                'email': {
                    required: true,
                    email: true
                }
            },
            messages: {
                'email': {
                    required: "Email is required",
                    email: "Invalid email"
                }
            }
        });
    }

    function bindResetPassword(module) {
        $("#resetPasswordForm").ajaxForm({
            url: siteurl + module + "/setPassword",
            dataType: 'json',
            beforeSubmit: function () {
                return $("#resetPasswordForm").valid();// TRUE when form is valid, FALSE will cancel submit
            },
            success: function (response) {
                alertbootbox(response.msg);

                if (response.success == true) {
                    $("#commonModal").modal("hide");
                }
            }
        });

        $("#resetPasswordForm").validate({
            rules: {
                'verification_code': {
                    required: true,
                },
                'password': {
                    required: true,
                    minlength: 8
                },
                'confirm_password': {
                    equalTo: '#re_password',
                }
            },
            messages: {
                'verification_code': {
                    required: "Enter secret code",
                },
                'password': {
                    required: "Password is required",
                    minlength: "Atleast 8 characters are required",
                },
                'confirm_password': {
                    equalTo: "Confirm Password do not match",
                }
            }
        });
    }
    //==========function for bootbox.alert with call back function
    function alertbootbox(msg) {
        bootbox.alert({
            closeButton: false,
            "message": msg,
            "className": "bootboxalertclass",
            "callback": function () {
                console.log("successfull");
            }
        });
        //=====added class to change cross button of bootbox
    }


    $(document).on("click", "#recoverUsername", function (e) {
        e.preventDefault();
        var url = $(this).attr("href");
        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                $("#commonModal .modal-title").html("Recovery Username");
                $("#commonModal .modal-body").html(response);
                $("#commonModal").modal("show");
                bindRecoverUsername();
            },
            error: function (response) {
                alertbootbox(response);
            }
        });
    });

    function bindRecoverUsername() {
        $("#recoverUsernameForm").ajaxForm({
            url: siteurl + "client/recoverUsername",
            dataType: 'json',
            beforeSubmit: function () {
                return $("#recoverUsernameForm").valid();// TRUE when form is valid, FALSE will cancel submit
            },
            success: function (response) {
                alertbootbox(response.msg);

                if (response.success == true) {
                    $("#commonModal").modal("hide");
                }
            }
        });

        $("#recoverUsernameForm").validate({
            rules: {
                'email': {
                    required: true,
                    email: true
                }
            },
            messages: {
                'email': {
                    required: "Email is required",
                    email: "Invalid email"
                }
            }
        });
    }

    $(document).on("keyup", "#adfs-org-short-code", function (e) {
        $('#adfs-org-short-code-error').html('');
        $(".adfs_login_button").attr('value', 'Login').addClass('hidden');
        var orgCode = $(this).val();
        //console.log("Org Short code : " + orgCode);
        $('.error').hide();
        var error = false;
        if ($.trim(orgCode).length < 1) {
            $('#adfs-org-short-code-error').html('Please enter organization short code..').show();
            error = true;
        } else { //Check for organization link and show the link
            var url = siteurl + "client/getOrgShortCode";
            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: {'short_code': orgCode},
                success: function (response) {
                    console.log(response);
                    if (response.success == true) {
                        console.log(response.adfs_link);
                        $("#adfs_login_link").attr('href', response.adfs_link);
                        $(".adfs_login_button").attr('value', 'Login to ' + orgCode.toUpperCase()).removeClass('hidden');
                    } else {
                        //$('#adfs-org-short-code-error').html(response.msg).show();
                    }
                    return false;
                },
                error: function (response) {

                }
            });
            return false;
        }
        if ($.trim(password).length < 1) {
            $('#ldap-password-error').html('Please enter password.').show();
            error = true;
        }
        if (!error) {
            $("#LdapLoginForm").submit();
        }
    });

    $('.ssouLink').on('click', function () {
        $('#adfs-org-short-code').val('');
        $('.adfs_login_button').addClass('hidden');
    });

</script>
<?php echo $this->Element("footersite"); ?>
