<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Thing List');?>
</head>

<body onload="getThingList();">
	<?php echo getHeaderBarHtml();?>
    
	<main>
		<div class="wrapperMain" id="wrapperMain"></div>
	</main>
    
    <div id="responseLogIcon" onclick="toggleResponseLog()"></div>
</body>
