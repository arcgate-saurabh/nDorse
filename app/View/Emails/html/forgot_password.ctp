<?php echo $first_name; ?>,
<br>
<br>
We received a notification that you need to reset your nDorse password.
<br>
<br>
Please use the link below to reset your password.
<br>
<br>
Link : <a href="<?php echo $verification_link;?>"><?php echo $verification_link; ?></a>
<br>
<br>
<br>
Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>