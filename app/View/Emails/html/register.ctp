Hi <?php echo $first_name; ?>!
<br>
<br>
Welcome to nDorse! 
<br>
<br>
Starting now you will be able to participate in motivational activities with your team/organization by nDorsing each other.
<br>
<br>
Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>
