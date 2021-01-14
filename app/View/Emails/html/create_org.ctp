<html>
    <head>
    </head>
    <body>
        <?php echo $fname; ?>,
        <br/><br/> 
        Congratulations on setting up your <?php echo $org_name; ?> organization! We would like to welcome you to our family!
        <br/><br/>
        nDorse is mobile and web based app that allows real time recognition/feedback and tags these to your organizations core values or performance metrics. nDorse can be personalized to your needs and also comes with access to our Admin Portal via <a href="www.ndorse.net">www.ndorse.net</a> 
        <!--Congratulations! You have successfully created nDorse organization, <?php //echo $org_name;  ?>.-->
        <br /><br />
        The Admin Portal which is responsive to your login credentials allows you to access to reports, manage users, send mobile alerts/announcements, post and distribute information using PDFs, PowerPoint, or Word documents, among some other cool features. 
        <br /><br />
        Let us help you get set up so you can maximize the use of your nDorse Organization to help achieve your goals. 
        <br /><br />
        Contact us via <a href="mailto:support@ndorse.net">support@ndorse.net</a> and one of our team members will reach out to you and help you set up your nDorse organization and also assist with any questions you may have with respect to features, access, admin functions, and subscriptions.
        <br /><br />
        We are looking forward to hearing from you and will be glad to assist you in any way.
        <!--If you have not initiated that or it is not expected then please contact nDorse team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.--> 
        <?php echo $this->element('email_footer'); ?>
        <br /><br />
        ** Disclaimer: A member of the nDorse Support team may contact you in regards to your newly created organization to offer assistance and information about the nDorse Recognition platform with the contact information that you have provided. **
        <br/><br />
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>