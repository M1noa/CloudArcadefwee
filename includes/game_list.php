<?php

function get_game_list($type, $amount, $page=0){
	$results = array();
	if($type == 'new'){
		$data = Game::getList( $amount, 'id DESC', $page );
		return $data;
	} elseif($type == 'random'){
		$data = Game::getList( $amount, 'RAND()', $page );
		return $data;
	} elseif($type == 'popular'){
		$data = Game::getList( $amount, 'views DESC', $page );
		return $data;
	}
}
function get_game_list_category($cat_name, $amount, $page=0){
	$results = array();
	$cat_id = Category::getIdByName( $cat_name );
	$data = Category::getListByCategory( $cat_id, $amount, $page );
	return $data;
}

?>