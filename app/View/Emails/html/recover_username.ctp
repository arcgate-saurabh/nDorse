<html>
    <head>
    </head>
    <body>
        Hi <?php echo $fname; ?>,<Br /><Br />
        This is your Username : <?php echo $username; ?><Br /><Br />
        Its case specific i.e. for log in to work,<Br /><Br />
        it must be typed into log in details as it appears.<Br /><Br />
        Please feel free to contact us at <a href="mailto:support@ndorse.net">support@ndorse.net</a> for any questions or comments.
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>