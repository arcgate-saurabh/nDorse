<html>
    <head>
    </head>
    <body>
<?php echo Ucfirst($fname);?>,
<br/><br/>
<?php if($option==1){
	if($type=="paid"){
	?>
This is to notify you that the nDorse Subscription for your organization <?php echo $org_name;?> has ended.
<?php }elseif($type=="trial"){?>
This is to notify you that the free trial of nDorse Subscription for your organization <?php echo $org_name;?> is terminated by NDORSE.
<?php } ?>
<br/><br/>
You can purchase a new subscription by contacting NDORSE Team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>. 
<?php }else{ ?>
This is to notify you that the nDorse Subscription for your organization <?php echo $org_name;?> has been terminated; effective after current billing cycle.
<Br/>You can re-activate your subscription prior to the end of this current billing cycle. After termination is effective, access to nDorse App will require you to purchase a new subscription.
<br/><br/>Please contact NDORSE LLC at <a href="mailto:support@ndorse.net">support@ndorse.net</a> for questions or to purchase a new subscription. 
<?php } ?>
       
       <?php echo $this->element('email_footer'); ?>
    </body>
</html>		
				