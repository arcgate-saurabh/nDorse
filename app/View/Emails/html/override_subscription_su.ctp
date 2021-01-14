<html>
    <head>
    </head>
    <body>
      Hi <?php echo Ucfirst($fname);?>!
         <br/><br/>
        This is to notify you that your auto renewable subscription has been overridden by NDORSE LLC.The new subscription is applicable from <?php echo $start_date;?>. And this subscription will not auto renew.
		 <br /><br />
		 Feel free to reach out to the nDorse team for any questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
		 <?php echo $this->element('email_footer'); ?>
    </body>
</html>		
