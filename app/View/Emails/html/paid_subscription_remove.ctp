<html>
    <head>
    </head>
    <body>
        <?php echo Ucfirst($fname); ?>,
        <br/><br/>
        This is to notify you that the nDorse Subscription for your organization <?php echo $org_name; ?> has expired.
        To get access to nDorse App, Admin and Reporting platform, purchase a new subscription or contact NDORSE LLC at <a href="mailto:support@ndorse.net">support@ndorse.net</a> . 
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>		
