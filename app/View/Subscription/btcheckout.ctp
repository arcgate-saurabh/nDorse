<?php
if(isset($redirectUrl)) {
    header( "refresh:5;url=" . $redirectUrl );
}

if(isset($error)) {
    if(!empty($error)) {
     ?>
    <div class="col-md-6 col-md-offset-2 thanks-subscription">
        <h3><?php echo $msg; ?></h3>
        <h4>Please wait. We are redirecting you to purchase subscription page.</h4>
        <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
        <br /><br />
    </div>
    <?php 
    } 
}else {?>
<div class="col-md-6 col-md-offset-2 thanks-subscription">
  <h3>Thank you <br />
    for purchasing the subscription.<br />
    </h3>
    <h4>Please wait. We are redirecting you to organization page.</h4>
    <div class="text-center"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div>
    <br /><br />
</div>
<?php } ?>