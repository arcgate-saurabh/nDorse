<html>
    <head>
    </head>
    <body>

        <?php echo Ucfirst($fname); ?>!
        <br/><br/>
        This is to notify you that the nDorse Trial Subscription for your organization <?php echo $org_name; ?> is ending in 7 days!
        Purchase a subscription or contact NDORSE LLC at <a href="mailto:support@ndorse.net">support@ndorse.net</a> to keep your nDorse App subscription active. 
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>		

<!--This is to notify you that the nDorse Trial Subscription for your organization <Org name> is ending in 7 days!-->
