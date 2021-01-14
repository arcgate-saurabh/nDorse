Hi <?php echo $fname;?>!;
<br>
<br>
Welcome to nDorse! 
<br>
<br>
nDorse is a mobile application that enables real time positive acknowledgement of your co-worker for actions that embody your institution’s mission statement and an excellent employee or colleague.
<br>
<br>
We invite you to participate in this new initiative and download the nDorse App. Once you download the app using the links provided below, you can enter your username which will be the email address where you received this invitation. The password will be a generic password that you can change at your first login. 
<br>
<br>
Your username: <?php echo $username;?> Your password: <?php echo $password;?> 
<br>
<br>
<br>
<br>
After login you can join the <?php echo $organization_name; ?> organization by entering the code below:
<br>
<br>
<?php echo $organization_code; ?>
<br>
<br>
Feel free to reach out to the nDorse team for any questions or feedback at support@ndorse.net.
<?php echo $this->element('email_footer'); ?>