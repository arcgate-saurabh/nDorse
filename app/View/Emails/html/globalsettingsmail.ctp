<html>
    <head>
    </head>
    <body>
        <!--		Hi,-->
        <?php //echo $name;?>
        <!--<br>-->
        <?php //echo $bodymsg;?>
        <br>
        <?php echo $msg; ?>
        <br>
        <?php echo $this->element('email_footer'); ?>				
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>