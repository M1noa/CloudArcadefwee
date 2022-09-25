<?php
session_start();
require( 'config.php' );
require( 'includes/game_list.php' );
require( 'includes/commons.php' );
require( TEMPLATE_PATH . '/functions.php' );
$action = isset( $_GET['viewpage'] ) ? $_GET['viewpage'] : '';

switch ( $action ) {
	case 'archive':
		archive();
		break;
	case 'search':
		search();
		break;
	case 'game':
		game();
		break;
	case 'page':
		page();
		break;
	case '404':
		err404();
		break;
	default:
		homepage();
}
function archive() {
	$cur_page = 1;
	if(isset($_GET['page'])){
		$cur_page = htmlspecialchars($_GET['page']);
		if(!is_numeric($cur_page)){
			$cur_page = 1;
		}
	}
	$category = Category::getBySlug($_GET['slug']);
	if($category){
		$data = get_game_list_category($category->name, 24, 24*($cur_page-1));
		$games = $data['results'];
		$total_games = $data['totalRows'];
		$total_page = $data['totalPages'];
		$meta_description = 'Play '.$category->name.' Games | '.SITE_DESCRIPTION;
		$archive_title = $category->name;
		$page_title = $category->name . ' Games | '.SITE_DESCRIPTION;
		require( TEMPLATE_PATH . '/archive.php' );
	} else {
		err404();
	}
}
function search() {
	$_GET['slug'] = htmlspecialchars($_GET['slug']);
	$cur_page = 1;
	if(isset($_GET['page'])){
		$cur_page = htmlspecialchars($_GET['page']);
		if(!is_numeric($cur_page)){
			$cur_page = 1;
		}
	}
	$data = Game::searchGame($_GET['slug'], 24, 24*($cur_page-1));
	$games = $data['results'];
	$total_games = $data['totalRows'];
	$total_page = $data['totalPages'];
	$meta_description = 'Search "'.$_GET['slug'].'" Games | '.SITE_DESCRIPTION;
	$archive_title = 'Search "'.$_GET['slug'].'"';
	$page_title = 'Search "'.$_GET['slug'].'" Games | '.SITE_DESCRIPTION;
	require( TEMPLATE_PATH . '/search.php' );
}
function game() {
	if ( !isset($_GET['slug']) || !$_GET['slug'] ) {
		homepage();
		return;
	}
	$_GET['slug'] = htmlspecialchars($_GET['slug']);
	Game::update_views( $_GET['slug'] );
	$game = Game::getBySlug( $_GET['slug'] );
	if($game){
		$page_title = $game->title . ' | '.SITE_DESCRIPTION;
		$meta_description = htmlspecialchars(strip_tags($game->description));
		require( TEMPLATE_PATH . '/game.php' );
	} else {
		err404();
	}
}
function page() {
	if ( !isset($_GET['slug']) || !$_GET['slug'] ) {
		homepage();
		return;
	}
	$_GET['slug'] = htmlspecialchars($_GET['slug']);
	$page = Page::getBySlug( $_GET['slug'] );
	if($page){
		$page_title = $page->title . ' | '.SITE_TITLE;
		$meta_description = htmlspecialchars(strip_tags($page->content));
		require( TEMPLATE_PATH . '/page.php' );
	} else {
		err404();
	}
}
function homepage() {
	$page_title = SITE_TITLE . ' | '.SITE_DESCRIPTION;
	$meta_description = META_DESCRIPTION;
	require( TEMPLATE_PATH . '/home.php' );
}
function err404() {
	$page_title = '404 - Page not found | '.SITE_TITLE;
	$meta_description = 'Page not found';
	require( TEMPLATE_PATH . '/404.php' );
}

?>