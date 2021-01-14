<html>
    <head>
    </head>
    <body>
        <?php if($type=="paid"){?>
        Hi <?php echo Ucfirst($fname);?>!
         <br/><br/>
        This is to notify you that your subscription has been successfully set up and activated by NDORSE LLC. Congratulations on joining the nDorse Family! 
         <br/><br/>
         Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
        <?php }else{?>
        <?php echo Ucfirst($fname);?>,
  
        <br/><br/>
        Congratulations! An nDorse Trial Subscription has been successfully created for your organization, <?php echo $org_name;?>.
        
        <br /><br />
        If you have not initiated that or it is not expected then please contact nDorse team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>. 
              <?php } ?>
        <?php echo $this->element('email_footer'); ?>
    </body>
</html>		
				