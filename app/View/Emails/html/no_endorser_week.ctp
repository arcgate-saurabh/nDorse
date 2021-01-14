<html>
    <head>
    </head>
    <body>
        <?php echo $fname; ?>,
        <br/><br/>
        we noticed that you have not nDorsed a colleague/friend in <?php echo $organization_name; ?> from some time -  nDorse someone now! Motivate with Praise!!
        <br>
        <br>
        Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>
