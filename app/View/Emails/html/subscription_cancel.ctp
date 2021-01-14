<?php echo $fname; ?>,
<br/>
<br/>
This is to notify you that the nDorse Subscription for your organization <?php echo $organization; ?> could not be activated because the credit card payment was denied again. Your subscription has been terminated. 
<br/>
<br/>
We are sad to see you leave the nDorse Family! Thank you for your business!
<br/>
<br/>
Purchase a new subscription by contacting NDORSE Team at <a href="mailto:support@ndorse.net">support@ndorse.net</a> – we would love to have you back.
<br/>
<br/>
Please feel free to contact us with questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if (isset($pathToRender) && $pathToRender != '') { ?>
    If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
<?php } ?>