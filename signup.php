<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Sign Up');?>
</head>

<body>
	<?php echo getHeaderBarHtml(); ?>
	
	<main>
		<div class="wrapperMain" id="wrapperMain">
			<script>document.write(getSignupHtml({'useGet':true}));</script>
		</div>
	</main>
</body>
