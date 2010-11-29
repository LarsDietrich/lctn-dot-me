<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>lctn.me | Contact Us</title>
		<link href="css/contact.css" type="text/css" rel="stylesheet" media="screen,projection" />
		<script type="text/javascript">
			$(document).ready(function(){
				$("#submit").click(function(){					   				   
					$("#error").hide();
					var emailTo = "rick@tonoli.co.za";
					var subject = "An email from lctn.me's contact form";
					var hasError = false;
					var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
					var errorColor = "1px solid red";
					var normalColor = "1px solid white";
					var emailFromVal = $("#emailFrom").val();
					$('#emailFrom').css("border", normalColor);
					if(emailFromVal == '') {
						$('#emailFrom').css("border", errorColor);
						hasError = true;
					} else if(!emailReg.test(emailFromVal)) {	
						$('#emailFrom').css("border", errorColor);
						hasError = true;
					}
					
					var messageVal = $("#message").val();
					$('#message').css("border", normalColor);
					if(messageVal == '') {
						$('#message').css("border", errorColor);
						hasError = true;
					}
					
					
					if(hasError == false) {
						$(this).hide();
						$("#sendEmail li.buttons").append('<img src="/images/loading.gif" alt="Loading" id="loading" />');
						
						$.post("email.php",
			   				{ emailTo: emailTo, emailFrom: emailFromVal, subject: subject, message: messageVal },
			   					function(data){
									$("#sendEmail").slideUp("normal", function() {				   
										$("#sendEmail").before('<p>Thank you for your feedback!<br><br><i> Close this window by either pressing ESC or clicking on the close button to continue.</i></p>');											
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
				<p>I'd love to hear from you, if you would like to contact me, please fill in the fields below and click send. Your email address will not be shared with anyone, the only reason I need it is so that I can contact you back.</p>
				<div class="contact-text">Your email address</div>
				<div><input onclick="$('#emailFrom').css('border', '1px solid white');" style="width:84%" type="text" name="emailFrom" id="emailFrom" value="<?= $_POST['emailFrom']; ?>" /></div>
				<div class="contact-text">Your message</div>
				<div><textarea onclick="$('#message').css('border', '1px solid white');" name="message" id="message"><?= $_POST['message']; ?></textarea></div>
				<div><button class="buttons" type="submit" id="submit">Send Email</button><input type="hidden" name="submitted" id="submitted" value="true"/></div>
			</form>
			<div class="clearing"></div>
		</div>
	</body>
</html>
