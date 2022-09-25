<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<title><?php echo htmlspecialchars( $page_title )?></title>
		<meta name="description" content="<?php echo esc_string($meta_description) ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN . TEMPLATE_PATH; ?>/style/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN . TEMPLATE_PATH; ?>/style/style.css" />
		<!-- Font Awesome icons (free version)-->
		<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
		<!-- Google fonts-->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
		<link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
		<?php include  TEMPLATE_PATH . "/parts/head.php" ?>
	</head>
	<body id="page-top">
		<!-- Navigation-->
		<nav class="navbar navbar-expand-lg navbar-dark top-nav" id="mainNav">
			<div class="container">
				<a class="navbar-brand js-scroll-trigger" href="/"><img src="<?php echo DOMAIN .SITE_LOGO ?>" class="site-logo"></a>
				<?php include  TEMPLATE_PATH . "/parts/navigation-top.php" ?>
			</div>
		</nav>
		<div class="nav-categories">
			<div class="container">
				<?php include  TEMPLATE_PATH . "/parts/navigation-categories.php" ?>
			</div>
		</div>