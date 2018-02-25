<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo $this->render('header.html',NULL,get_defined_vars(),0); ?>
	</head>

	<body>
	    <!-- Navigation -->
    	<?php echo $this->render('navigation.html',NULL,get_defined_vars(),0); ?>

    	<!-- Page Content -->
    	<div class="container">
			<?php if ($SESSION['warning'] ==''): ?>
    			<?php else: ?>
						<div class="alert alert-warning" role="alert">
 							 <?= $SESSION['warning'].PHP_EOL ?>
						</div>
				
			<?php endif; ?>
			<?php if ($SESSION['info'] ==''): ?>
    			<?php else: ?>
						<div class="alert alert-info" role="alert">
 							 <?= $SESSION['info'].PHP_EOL ?>
						</div>
				
			<?php endif; ?>
			<?php if ($SESSION['danger'] ==''): ?>
    			<?php else: ?>
						<div class="alert alert-danger" role="alert">
 							 <?= $SESSION['danger'].PHP_EOL ?>
						</div>
				
			<?php endif; ?>
			<?php if ($SESSION['success'] ==''): ?>
    			<?php else: ?>
						<div class="alert alert-success" role="alert">
 							 <?= $SESSION['success'].PHP_EOL ?>
						</div>
				
			<?php endif; ?>
			<?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
		</div>
	</body>
</html>
