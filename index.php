<?php require ("includes/functions.php")?>
<?php if (!(isset($_POST["shorten"]))) { ?>
<form id="urlShortener" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<strong> Url:</strong>
	<input id="url" name="url" size="45" type="text">
	<input id="Submit" name="shorten" value="shorten" type="submit">
</form>
<?php } else { ?>

<?php 

echo "http://lcnt.me/" . getShortUrl($_POST["url"]) 

?>

<?php } ?>