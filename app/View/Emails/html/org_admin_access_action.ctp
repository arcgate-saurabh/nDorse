<html>
    <head>
    </head>
    <body>
        <?php echo Ucfirst($fname); ?>,
        <br/><br/>
        <?php echo $message; ?>

        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>