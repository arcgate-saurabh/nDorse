<html>
    <head>
    </head>
    <body>
        <?php echo Ucfirst($fname); ?>,
        <br/><br/>
        This is to notify you that the nDorse App Subscription for your organization, <?php echo $org_name; ?> will be ending in 7 days.To keep your nDorse subscription to avoid termination of service.
        <br/><br/>
        To renew subscription, contact nDorse Team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>. 
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>		
