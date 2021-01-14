<html>
	<head>
	</head>
	<body>
		Hi,
		<br>
		You are invited for the organization <?php echo $organization_name;?>
		<br>
		Your Information is given below:-
		<br>
		Username:- <?php echo $username; 
                if(!empty($password)){?>
                <br>
		Password:- <?php echo $password; ?> 
                <?php }?>
        </body>
</html>