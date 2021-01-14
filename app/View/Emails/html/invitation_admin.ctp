Hi <strong><?php echo $fname;?></strong>!
<br>
<br>
Welcome to nDorse! 
<br>
<br>
You have been invited to join <strong><?php echo $organization_name; ?></strong> Organization in the nDorse App.
<br>
<br>
nDorse is a mobile and web-based application that enables real time positive recognition of colleagues or teammates for their actions that embody your institution’s mission statements or core values.
<br>
<br>
You can access nDorse using our mobile app OR using your computer or laptop using our Web App. 
<br>
<br>
iPhone users can download the app from the Apple Store (<a href="https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8">https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8</a>) 
<br>
<br>
Android users can download the app from Google Play Store (<a href="https://play.google.com/store/apps/details?id=net.susco.ndorse&hl=en">https://play.google.com/store/apps/details?id=net.susco.ndorse&hl=en</a>) 
<br>
<br>
Link to Web App: <a href="www.ndorse.net/client/login">http://www.ndorse.net/client/login</a>
<br>
<br>
After downloading the app, log into the app with your Username and Password below; these credential will also be valid if you choose to use our Web App link to join nDorse.
<br>
<br>
Your username: <?php echo $username;?>
<br>
<br>
Your password: <?php echo $password;?>
<br>
<br>

If you have trouble logging in or just have a question regarding our app - reach out to us; the nDorse team is looking forward to hearing from you and is available to help at <a href="mailto:support@ndorse.net">support@ndorse.net</a>. 
<?php echo $this->element('email_footer'); ?>
<br>
<span style="font-style: italic;font-size:18px;font-color:#52286A;">Recognize in Real Time! Motivate through Praise!!</span>
<br>
<?php if(isset($pathToRender) && $pathToRender !=''){ ?>
        If you wish to unsubscribe from this notifications, Please click <a href="<?php echo $pathToRender;?>">here</a>.
<?php } ?>