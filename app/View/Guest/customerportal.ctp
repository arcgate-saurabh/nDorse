<?php
$path = Router::url('/', true);
$path = str_replace("http", "https", $path);
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width initial-scale=1.0; maximum-scale=1.0; user-scalable=1;" />
<div class="bg">
<?php echo $this->Html->Image("/images/guest-bg.jpeg", array("class" => "img-responsive", "alt" => "")); ?>
</div>
<section class="guest-bg ">
    <div class="guest-header">
        <div class="main-width">
            <h2>Welcome To <br> <span class="comp-name">UMC/LCMC Health’s</span> <br> Guest Portal for Recognition</h2>
        </div>
    </div>

    <div class="guest-content text-center">
        <div class="main-width congratulations">
            <div class="comp-logo">
<?php echo $this->Html->Image("/images/comp-logo.png", array("class" => "img-responsive", "alt" => "")); ?>
            </div>
            <from>
                <div class="form-group text-center"> 
                    <h2>Congratulations!</h2>
                    <h3>Thank You For nDorsing</h3>
                    <h1>CarRita Tanner</h1>
                </div>
                <div class="form-group note"> 
                    <h5>NOTE:<br>
                        You’ll receive an E-mail <br> 
                        notification as confirmation when <br>
                        your message is recieved.</h5>
                </div>


                <div class="form-group ">
                    <button class="btn guest-btn btn-block" type="submit" id="">Close</button>
                </div>
            </from>
        </div>
    </div>

    <div class="guest-footer text-center">
        <div class="powered-by">
<?php echo $this->Html->Image("/images/powered-by.png", array("class" => "img-responsive", "alt" => "")); ?>
        </div>
    </div>
</section>

<div class="MT30"></div>