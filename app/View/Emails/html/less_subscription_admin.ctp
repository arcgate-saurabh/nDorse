Hi,
<br>
<br>
<?php echo $user['fname'] . " " . $user['lname'];?> has tried to join <?php echo $org_name; ?> and not enough subscriptions are there.
<br>
<br>
Please purchase subscription.
<br>
<br>
Please feel free to contact us at <a href="mailto:support@ndorse.net">support@ndorse.net</a> for any questions or comments.
<?php echo $this->element('email_footer'); ?>	
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>
