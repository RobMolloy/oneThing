<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Sign Up');?>
</head>

<body onload="
    <?php 
        if(userIsLoggedIn()){echo 'goto(\'index.php\');';}
        else{echo 'appendToWrapperMain(getSignupHtml({\'useGet\':true}));';}
    ?>
">
	<?php echo getHeaderBarHtml(); ?>
	
	<main>
		<div class="wrapperMain" id="wrapperMain">
		</div>
	</main>
    <div id="responseLogIcon" onclick="toggleResponseLog()"></div>
</body>
