<?php echo $fname?>, 
<br/>
<br/>

Congratulations! This is to notify that the subscription for <?php echo $organization;  ?> was successfully renewed! 
<br/>
<br/>
The credit card payment was successful! 
<br/>
<br/>
Please feel free to contact us with questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>