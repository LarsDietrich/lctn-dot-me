<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>jQuery Ajax Forms | Trevor Davis</title>
		<link href="css/contact.css" type="text/css" rel="stylesheet" media="screen,projection" />
		
		<script type="text/javascript">
		
		$(document).ready(function(){
			$("#submit").click(function(){					   				   
				$("#error").hide();
				var hasError = false;
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				var errorColor = "#FF0000";
				var emailFromVal = $("#emailFrom").val();
				$('#emailFrom').css("background-color", "white");
				if(emailFromVal == '') {
					$('#emailFrom').css("background-color", errorColor);
					hasError = true;
				} else if(!emailReg.test(emailFromVal)) {	
					$('#emailFrom').css("background-color", errorColor);
					hasError = true;
				}
				
				var messageVal = $("#message").val();
				$('#message').css("background-color", "white");
				if(messageVal == '') {
					$('#message').css("background-color", errorColor);
					hasError = true;
				}
				
				
				if(hasError == false) {
					$(this).hide();
					$("#sendEmail li.buttons").append('<img src="/images/loading.gif" alt="Loading" id="loading" />');
					
					$.post("email.php",
		   				{ emailTo: "rick@tonoli.co.za", emailFrom: emailFromVal, subject: "Contact from lctn.me", message: messageVal },
		   					function(data){
								$("#sendEmail").slideUp("normal", function() {				   
									$("#sendEmail").before('<h1>Success</h1><p>Your email was sent.</p>');											
								});
		   					}
						 );
				}
				
				return false;
			});						   
		});
		
		</script>
	
	</head>
	
	<body>
	
		<div id="container">
		<?php include('verify.php'); ?>
			<form action="/" method="post" id="sendEmail">
				<p>If you find something that doesn't work, or think you have a cool idea, please let me know!</p>
				<ol class="forms">
					<li><label for="emailFrom">Email</label><input onclick="$('#emailFrom').css('background-color', 'white');" class="title" type="text" name="emailFrom" id="emailFrom" value="<?= $_POST['emailFrom']; ?>" /></li>
					<li><label for="message">Message</label><textarea onclick="$('#message').css('background-color', 'white');" class="title" name="message" id="message"><?= $_POST['message']; ?></textarea></li>
					<li class="buttons"><button type="submit" id="submit">Send Email</button><input type="hidden" name="submitted" id="submitted" value="true" /></li>
				</ol>
			</form>
			<div class="clearing"></div>
		</div>
	</body>
</html>
