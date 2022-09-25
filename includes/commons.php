<?php

function get_all_categories(){
	$data = Category::getList();
	return $data['results'];
}
function get_user($username){
	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = 'SELECT password FROM users WHERE username = :username AND role = "admin"';
	$st = $conn->prepare( $sql );
	$st->bindValue( ":username", $username, PDO::PARAM_STR );
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	if ( $row ) return ( array ( "username" => $username, "password" => $row['password'] ) );
	return ( array ( "username" => $username, "password" => '' ) );
}
function is_login(){
	if(isset( $_SESSION['username'] )){
		return true;
	} else {
		return false;
	}
}
function show_logout(){
	if(is_login()){
		echo '<a href="/admin.php?action=logout"> Log out </a>';
	}
}
function get_permalink($type, $name){
	if($type == 'game'){
		if( PRETTY_URL ){
			return DOMAIN . 'game/' . $name;
		} else {
			return DOMAIN . 'index.php?viewpage=game&slug=' . $name;
		}
	} else if($type == 'archive'){
		if( PRETTY_URL ){
			return DOMAIN . 'archive/' . $name;
		} else {
			return DOMAIN . 'index.php?viewpage=archive&slug=' . $name;
		}
	} else if($type == 'search'){
		if( PRETTY_URL ){
			return DOMAIN . 'search/' . $name;
		} else {
			return DOMAIN . 'index.php?viewpage=search&key=' . $name;
		}
	} else if($type == 'category'){
		if( PRETTY_URL ){
			return DOMAIN . 'archive/' . strtolower($name);
		} else {
			return DOMAIN . 'index.php?viewpage=archive&slug=' . strtolower($name);
		}
	} else if($type == 'page'){
		if( PRETTY_URL ){
			return DOMAIN . 'page/' . $name;
		} else {
			return DOMAIN . 'index.php?viewpage=page&slug=' . $name;
		}
	}
}
function commas_to_array($str){
	return preg_split("/\,/", $str);
}
function html_purify($html_content){
	require_once '../vendor/HTMLPurifier/HTMLPurifier.auto.php';
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	$clean_html = $purifier->purify($html_content);
	return $clean_html;
}
function esc_string($str){
	return filter_var($str, FILTER_SANITIZE_STRING);
}
function esc_int($int){
	return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
}
function esc_url($str){
	return filter_var($str, FILTER_SANITIZE_URL);
}
?>