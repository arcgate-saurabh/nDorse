<?php echo $fname; ?>,
<br/>
<br/>
This is to notify you that the subscription for <?php echo $organization; ?> has been successfully <?php echo $action; ?>(<?php echo $user_count; ?> users) by <?php echo $updated_by; ?>.
<br/>
<br/>
Please feel free to contact us with questions or feedback at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>