<html>
    <head>
    </head>
    <body>
        <?php echo Ucfirst($fname); ?>,
        <br/><br/>
        This to notify you that the administrator (<?php echo $admin_name; ?>)  of <?php echo $org_name; ?> has <?php echo $status; ?> your login with Username: <?php echo $username; ?>.
        <br /><br />
        Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>. 
        <?php echo $this->element('email_footer'); ?>
        <br>
        <?php if (isset($pathToRender) && $pathToRender != '') { ?>
            If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender; ?>">here</a>.
        <?php } ?>
    </body>
</html>