<?php require_once('includes/common.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<?php echo getHeadTags('Home');?>
</head>

<body>
	<?php echo getHeaderBarHtml(); ?>
	<main>
		<div class="wrapperMain" id="wrapperMain">
            <div class="panel">
			
            <?php
                $welcomeText = (userIsLoggedIn() 
                                    ? '<div><h1>Welcome!</h1></div><div>You have logged in successfully!</div>'
                                    : 'Log in or sign up to get started!'
                                );
                echo $welcomeText;
            ?>
            
            </div>
		</div>
	</main>
</body>
