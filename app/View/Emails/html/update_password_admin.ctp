<html>
    <head>
    </head>
    <body>
        <?php echo ucfirst($fname); ?>,
        <br>
        <br>
        Your <?php echo $org_name; ?> administrator has successfully reset your password. Your new password is <?php echo $password; ?>.
        <br>
        <br>
        Please feel free to contact us at <a href="mailto:support@ndorse.net">support@ndorse.net</a> for any questions or comments.
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>