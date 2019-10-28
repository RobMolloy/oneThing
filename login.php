<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Log In');?>
</head>

<body onload="
    <?php 
        if(userIsLoggedIn()){echo 'showThingListPanels();';}
        else{echo 'appendToWrapperMain(getLoginHtml({\'useGet\':true}));';}
    ?>
">
	<?php echo getHeaderBarHtml(); ?>
	
	<main>
		<div class="wrapperMain" id="wrapperMain">
		</div>
	</main>
</body>
