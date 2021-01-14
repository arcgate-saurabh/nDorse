<html>
    <head>
    </head>
    <body>
        <?php echo Ucfirst($fname); ?>,
        <br/><br/>
        <strong>This is to notify you that the nDorse Subscription for your organization <?php echo $org_name; ?> has ended.</strong>
        Your subscription will be active for another 7 days prior to becoming inactive.
        <br /><br />
        To maintain access to nDorse App, you can renew your subscription by contacting NDORSE Team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>  
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>

    </body>
</html>		
