<?php
$path = Router::url('/', true);
$path = str_replace("http", "https", $path);
$this->assign('title', 'DAISY');
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width initial-scale=1.0; maximum-scale=1.0; user-scalable=1;" />
<div class="bg">
    <?php echo $this->Html->Image("/images/daisy-bg.JPG", array("class" => "img-responsive", "alt" => "")); ?>
</div>
<div class="daisy-guest">
    <section class="guest-bg">
        <div class="guest-header">
            <div class="main-width-removed">
                <h2>
                    <br>
                    <!--Welcome To <br>-->
                    <span>
                        The DAISY Award<sup>©</sup> For Extraordinary Nurses Nomination Portal
                    </span>
                    <!-- <span class="comp-name">
                        
                    </span> -->
                    <br> <?php echo $orgDetail['Organization']['name']; ?></h2>
            </div>
        </div>
        <div class="guest-content text-center daisy-thnks main-width">
            <div class="text-center">
                    <?php echo $this->Html->Image("/images/daisy-thank.png", array("class" => "", "alt" => "")); ?>
                    <h1>
                        <!--Thank you for taking the time to nominate an extraordinary nurse for this award!-->
                        Thank you for taking the time to nominate your extraordinary nurse for The DAISY Award. Your nurse will know how much you appreciate the care you received.
                    </h1>
            </div>
        </div>

        <div class="guest-content text-center hidden">
            <div class="main-width congratulations">
                <div class="comp-logo">
                    <?php // echo $this->Html->Image("/images/comp-logo.png", array("class" => "img-responsive", "alt" => "")); ?>
                    <?php
                    $cpLogo = isset($orgDetail['Organization']['cp_logo']) ? $orgDetail['Organization']['cp_logo'] : $orgDetail['Organization']['image'];
                    $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $cpLogo;
                    $org_imagenew = str_replace("http", "https", $org_imagenew);
                    if (isset($cpLogo) && $cpLogo != '') {
                        echo $this->Html->Image($org_imagenew, array("class" => "img-responsive", "alt" => ""));
                    } else {
                        echo $this->Html->image('comp_pic.png', array("class" => "img-responsive", "alt" => ""));
                    }
                    ?>
                </div>
                <?php
                echo $this->Form->create('endorse', array('url' => array('controller' => 'guest', 'action' => 'index', 'id' => $encryptID), 'type' => 'post', 'id' => 'guestfeedback'));
                ?>
                <div class="form-group text-center"> 
                    <h2>Congratulations!</h2>
                    <h3>Thank You For nDorsing</h3>
                    <h1><?php echo $endorsedName; ?></h1>
                </div>
                <div class="form-group note"> 
                    <h5>NOTE:<br>
                        You'll receive an E-mail <br> 
                        notification as confirmation when <br>
                        your message is recieved.</h5>
                </div>


                <div class="form-group ">
                    <button class="btn guest-btn btn-block" type="submit" id="">Close</button>
                </div>
                </form>
            </div>
        </div>

        <div class="guest-footer text-center">
            <div class="powered-by">
                <?php echo $this->Html->Image("/images/powered-by.png", array("class" => "img-responsive", "alt" => "")); ?>
            </div>
        </div>
    </section>
</div>
<div class="MT30"></div>
<script>
     $(document).ready(function () {
         setTimeout(function () {
             window.location.href = siteurl + 'daisy/index/' + '<?php echo $encryptID; ?>';
         }, 10000);
     });

</script>