Hi <?php echo $fname;?>!;
<br>
<br>
Welcome to nDorse! 
<br>
<br>
nDorse is a mobile application that enables real time positive acknowledgement of your co-worker for actions that embody your institution’s mission statement and an excellent employee or colleague.
<br>
<br>
Your username: <?php echo $username;?> Your password: <?php echo $password;?> 
<br>
<br>
Feel free to reach out to the nDorse team for any questions or feedback at support@ndorse.net.
<?php echo $this->element('email_footer'); ?>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>