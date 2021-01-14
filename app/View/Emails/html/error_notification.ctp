<html>
				<head>
				</head>
				<body>
								Code
								<br>
								<?php echo $error . " (" . $code . ")"; ?>
								<br>
								<br>
								Description:
								<br>
								<?php echo $description; ?>
								<br>
								<br>
							File:
							<br>
							<?php echo $file; ?>
								<br>
								<br>
								Line:
							<br>
							<?php echo $line; ?>
								<br>
								<br>
								Context:
							<br>
							<?php echo "<pre>"; print_r($context);echo "</pre>"; ?>
								<br>
								<br>
							Session:
							<br>
							<?php echo "<pre>"; print_r($session);echo "</pre>"; ?>
								<br>
								<br>
							Server:
							<br>
							<?php echo "<pre>"; print_r($server);echo "</pre>"; ?>
								<br>
								<br>
							request:
							<br>
							<?php echo "<pre>"; print_r($request);echo "</pre>"; ?>
								<br>
								<br>
							Trace:
							<br>
							<?php echo "<pre>"; print_r($trace);echo "</pre>"; ?>
								<br>
								<br>

				</body>
</html>