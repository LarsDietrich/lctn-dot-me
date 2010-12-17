<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>lctn.me | Contact Us</title>
		<link href="css/contact.css" type="text/css" rel="stylesheet" media="screen,projection" />
		<script type="text/javascript">
			$(document).ready(function(){
				$("#submit").click(function(){					   				   
					var emailTo = "rick@tonoli.co.za";
					var subject = "An email from lctn.me's contact form";
					var hasError = false;
					var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

					var messageVal = $("#message").val();
					if(messageVal == '') {
						$("#error").val("You have not entered a message.");
						$("#message").focus()
						hasError = true;
					}

					var emailFromVal = $("#emailFrom").val();
					if(emailFromVal == '') {
						$("#error").val("Please supply an email address");
						$("#emailFrom").focus()
						hasError = true;
					} else if(!emailReg.test(emailFromVal)) {	
						$("#error").val("Please supply a valid email address");
						$("#emailFrom").focus()
						hasError = true;
					}
					
					if(hasError == false) {
						$(this).hide();
						$("#sendEmail buttons").append('<img src="/images/loading.gif" alt="Loading" id="loading" />');
						alert(emailTo);
						alert(emailFromVal);
						alert(messageVal);
						alert(subject);

						$.post("contact/email.php",
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
			<?php include('verify.php'); ?>
			<form action="/" method="post" id="sendEmail">
				<p class="contact-text">I'd love to hear from you, if you would like to contact me, please fill in the fields below and click send. You email address will not be shared with anyone, the only reason I need it is so that I can contact you back.</p>
				<div class="contact-text">Your email address</div>
				<div>
					<input style="width:91.6%; font-size: large; background-color: transparent; color: #000" type="text" name="emailFrom" id="emailFrom" value="<?= $_POST['emailFrom']; ?>" />
				</div>
				<div class="contact-text">Your message</div>
				<div>
					<textarea style="width: 90%; font-size: large; background-color: transparent; color: #000" name="message" id="message"><?= $_POST['message']; ?></textarea>
				</div>
				<div>
					<button class="buttons" type="submit" id="submit">Send Email</button>
					<input type="hidden" name="submitted" id="submitted" value="true"/>
					<input style="background-color: transparent; border: none;" type="text" id="error" class="contact-error" disabled="disabled"/>
				</div>
			</form>
			<div class="clearing"></div>
	</body>
</html>
