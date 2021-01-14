<?php
$admin_name = $admin['id'] == $invited_by['id'] ? "" : "(" . $invited_by['fname'] . " " . $invited_by['lname'] . ")";

echo $admin['first_name'];?>,
<br>
<br>
This is to notify you that an nDorse administrator <?php echo $admin_name; ?> of <?php echo $org_name; ?> has invited 
<?php if(count($invited_users) > 1) { ?>
 following users to join it.
<br>
<br>
<?php
$count = 1;

foreach($invited_users as $invited_user) {

echo $count++ . "." . $invited_user . "<br>";
 }
} else {?>
<?php echo $invited_users[0]; ?> to join it.
<br>
<?php } ?>
<br>
If you have not initiated this or it is not expected then please contact nDorse team at <a href="mailto:support@ndorse.net">support@ndorse.net</a>.
<?php echo $this->element('email_footer'); ?>
