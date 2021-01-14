<html>
    <body>
        <strong>Dear nDorse User,</strong>
        <br>
        <br>
        An administrator at <?php echo $org_name; ?> has posted new content on the nDorse Live Feed.  
        <br>
        <br>
        Post Title: <?php echo $post_title; ?>
        <br>
        <br>
        Message: <?php echo $post_message; ?>
        <br>
        <br>
        Log into your nDorse account to view the full Post and attachments; attachments may include a PowerPoint, PDF or images, and can only be viewed by accessing your account.
        <br>
        <br>
        Unsure of your username or password? Email us at <a href="mailto:support@ndorse.net">support@ndorse.net</a> and one of our representatives will assist you as soon as possible.
        <br>
        <br>
        This message was sent on behalf of the Administrators at <strong><?php echo $org_name; ?></strong>.
        <br>
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>