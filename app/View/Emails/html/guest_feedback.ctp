<html>
    <head>
    </head>
    <body>
        <strong>Dear <?php echo $first_name; ?> <?php echo $last_name; ?>,</strong>
        <br>
        <br>
        Thank you using the <?php echo $org_name; ?>'s Guest Portal for Recognition powered by nDorse! Your feedback is valuable to us!  
        <br>
        <br>
        This is an email confirmation that your feedback has been received by a member at <?php echo $org_name; ?>
        <br>
        <br>
        <br>
        Sincerely, 
        <br>
        <br>
        nDorse Support Team<br>
        on behalf of <?php echo $org_name; ?>
        <br>
        <br>
        <a href="http://www.ndorse.net/">www.nDorse.net</a>
        <br>
        Download the free iOS or Android app and nDorse away.
        <br>
        <br>
        <a href="https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8"><img alt='Get it on itune store' src="<?php echo Router::url('/', true) . "img/App_store.png" ?>" width="120"></a>
        <a href='https://play.google.com/store/apps/details?id=net.susco.ndorse&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1'><img alt='Get it on Google Play' src="<?php echo Router::url('/', true) . "img/google_play.png" ?>" width="105" style="margin-left: 10px"></a>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>

    </body>
</html>