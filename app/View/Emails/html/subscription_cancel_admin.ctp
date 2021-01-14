<?php echo $fname; ?>,
<br/>
<br/>
This is to notify you that the subscription for <?php echo $organization; ?> has been canceled by <?php echo $canceled_by; ?>; effective after current billing cycle. 
<br/>
<br/>
After cancellation is effective, access to nDorse App will require you to purchase a new subscription.
<br/>
<br/>
Please feel free to contact us with questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if (isset($pathToRender) && $pathToRender != '') { ?>
    If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
<?php } ?>