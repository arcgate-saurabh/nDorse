<?php echo $fname; ?>, 
<br/>
<br/>
Congratulations! This is to notify that the subscription for <?php echo $organization; ?> is now active!  The subscription was successfully purchased by <?php echo $purchased_by; ?> via credit card payment and the payment has been successfully received!
<?php echo $this->element('email_footer'); ?>
<br>
<?php if (isset($pathToRender) && $pathToRender != '') { ?>
    If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
<?php } ?>