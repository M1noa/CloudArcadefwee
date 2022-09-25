<?php
session_start();
require "../config.php";
require( "../includes/game_list.php" );
require( "../includes/commons.php" );
$action = isset( $_POST['action'] ) ? $_POST['action'] : "";
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";

if ( $action != "login" && $action != "logout" && !$username ) {
	exit('logout');
}

$pages = array (
	array("Dashboard",'dashboard'),
	array("Game list",'gamelist'),
	array("Add game",'addgame'),
	array("Categories",'categories'),
	array("Pages",'pages'),
	array("Settings",'settings'),
	array("Style editor",'styleeditor'),
	array("Layout",'layout'),
);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<link rel="stylesheet" type="text/css" href="/<?php echo TEMPLATE_PATH; ?>/style/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="style/admin.css">
	<script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
</head>
<body>
<div class="admin-bar">
	<div class="quicklinks"><a href="/" target="_blank">Visit site</a></div>
	<div class="quicklinks"><?php show_logout() ?></div>
</div>
<div class="admin-container">
	<div class="sidebar">
		<div class="admin-menu">
			<ul id="menu-list">
				<li><img src="../images/logo.png" class="logo"></li>
				<?php
				$page_name = 'Dashboard';
				$page_slug = 'dashboard';
				if(isset($_GET['viewpage'])){
					$page_slug = htmlspecialchars($_GET['viewpage']);
				}
				foreach ($pages as $item) {
					$active = '';
					if($item[1] == $page_slug){
						$page_name = esc_string($item[0]);
						$page_slug = esc_string($item[1]);
						$active = 'class="active"';
					}
					echo '<li '.$active.'>';
					echo '<a href="?viewpage='.$item[1].'">';
					echo '<div class="li-list" name="dashboard">';
					echo esc_string($item[0]);
					echo '</div></a></li>';
				}
				?>
			</ul>
		</div>
	</div>
	<div class="content">
		<?php if( ADMIN_DEMO ){ echo '<div class="alert alert-warning" role="alert">(Admin Demo) Note: All actions are not saved.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'; } ?>
		<h3 class="page-title"><?php echo esc_string($page_name); ?></h3>
		<?php include 'core/'.$page_slug.'.php'; ?>
	</div>
</div>

				<a  style="position:absolute;right:10px;;top:55px;" href="https://gamemonetize.com" target="_blank" aria-label="GameMonetize.com" style="display: block;text-align: center;padding-top: 20px;">
                <img src="https://api.gamemonetize.com/powered_by_gamemonetize.png" alt="GameMonetize.com" style="width:250px;text-align: center;margin: 0 auto;">
            </a>

<script type="text/javascript" src="/js/script.js?v=gamemonetize"></script>
<script type="text/javascript" src="/js/wikiquote.js"></script>
<script type="text/javascript">
	<?php if($page_slug == 'dashboard'){
		?>
		$(function() {
		    getQuote();
		});
		<?php
	} ?>
</script>
</body>
</html>