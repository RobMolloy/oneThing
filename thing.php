<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Thing');?>
</head>

<body onload="addBlankThingFormToWrapperMain();getThingList();">
	<?php echo getHeaderBarHtml(); ?>
    
	<main>
		<div class="wrapperMain" id="wrapperMain"></div>
	</main>
    
    <div id="responseLogIcon" onclick="toggleResponseLog()"></div>
</body>
