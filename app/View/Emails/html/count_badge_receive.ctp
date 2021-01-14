<?php echo $fname; ?>,
<br/>
<br/>
Congratulations! You have received nDorse badge/trophy for your your nDorse organization <?php echo $orgName; ?>.  The badge/trophy is attached.
<br/>
<br/>
<img src="<?php echo Router::url('/', true) . TROPHY_IMAGE_DIR .  $trophy_image; ?>">
<?php echo $this->element('email_footer'); ?>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>