Dear <?php echo $first_name; ?>,
<br>
<br>
<?php
switch ($for) {
    case "user" :
        if (isset($type) && $type == 'first') {
            $msg = "Congratulations! " . $endorser_name . " has nDorsed you recently.";
            if (!empty($core_values)) {
                $msg .= " for core values: ";
                $total_core_values = count($core_values);
                $count = 1;
                foreach ($core_values as $core_value) {
                    if ($count == $total_core_values) {
                        $msg .= $core_value;
                    } else if ($count == $total_core_values - 1) {
                        //$msg = rtrim($msg, ", ");
                        $msg .= $core_value . " and ";
                    } else {
                        $msg .= $core_value . ", ";
                    }
                    $count++;
                }
            }


//            $msg .= ".  Please log on to see your nDorse application to see more details on the nDorsement.";

            echo $msg;
        } else {
            echo "Congratulations! " . $endorser_name . " has nDorsed you recently.";
        }

        break;

    case "department" :
        echo $endorser_name . " has nDorsed  " . $endorsed_name . " department recently.";
        break;

    case "entity" :
        echo $endorser_name . " has nDorsed  " . $endorsed_name . " department recently.";
        break;
}

if ($sso == 1) { //Case for SSO User notification 
    ?>
    You can see more details of your nDorsements by logging into the nDorse app on your mobile device or via our web-based login at <a href="http://www.ndorse.net/sso/LGH">http://www.ndorse.net/sso/LGH</a>.
    <br/><br/>
    If you are logging in via the nDorse Mobile App, please follow the login instructions below:
    <br/>
    1. Click on the Single Sign-On button
    <br/>
    2. Type in org code "LGH"
    <br/>
    3. Login with your organization provided login credentials
<?php } else { ?>
    You can see more details of your nDorsements by logging into the nDorse app on your mobile device or via our web-based login at <a href="https://ndorse.net/client/login"> https://ndorse.net/client/login</a>.
    <br>
    <br>
    Username: <?php echo $username; ?>
    <br>
    <br>
    If you forgot your password, you can click on the "Forgot Password" link at the login page.
<?php } ?>    
<br>
<br>
Please feel free to contact us at <a href="mailto:support@ndorse.net">support@ndorse.net</a> for any questions or comments.
<?php echo $this->element('email_footer'); ?>				
