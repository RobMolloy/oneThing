<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Log In');?>
</head>

<body>
	<?php echo getHeaderBarHtml(); ?>
	
	<main>
		<div class="wrapperMain" id="wrapperMain">
			<script>document.write(getLoginHtml({'useGet':true}));</script>
		</div>
	</main>
</body>
