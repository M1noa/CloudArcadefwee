<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once( "../config.php" );
require_once( "../includes/commons.php" );

if(count($_POST) == 0){
	$_POST = $_GET;
}

$action = isset( $_POST['action'] ) ? $_POST['action'] : "";
$username = isset( $_SESSION['username'] ) ? $_SESSION['username'] : "";

if ( $action != "login" && $action != "logout" && !$username ) {
	exit('logout');
}
if(isset($_POST['redirect'])){
	$_POST['redirect'] = esc_url($_POST['redirect']);
}
if( ADMIN_DEMO ){
	if($action !== 'getPageData'){
		if(isset($_POST['redirect'])){
			header('Location: '.$_POST['redirect']);
		}
		exit();
	}
}

switch ( $action ) {
	case 'deleteGame':
		$game = Game::getById( (int)$_POST['id'] );
  		$game->delete();
  		if(isset($_POST['redirect'])){
			header('Location: '.$_POST['redirect'].'&status=deleted');
		}
		break;
	case 'newPage':
		$_POST['content'] = html_purify($_POST['content']);
		$page = new Page;
    	$page->storeFormValues( $_POST );
    	$page->insert();
		break;
	case 'deletePage':
		$page = Page::getById( (int)$_POST['id'] );
		$page->delete();
		break;
	case 'getPageData':
		$page = Page::getById( (int)$_POST['id'] );
		$json = json_encode($page);
		echo $json;
		break;
	case 'editPage':
		$_POST['content'] = html_purify($_POST['content']);
		$page = Page::getById( (int)$_POST['id'] );
		$page->storeFormValues( $_POST );
		$page->update();
		break;
	case 'deleteCategory':
		$category = Category::getById( (int)$_GET['id'] );
		$category->delete();
		$data = Category::getListByCategory((int)$_GET['id'], 10000);
		$games = $data['results'];
		foreach ($games as $game) {
			$gm = Game::getById((int)$game->id);
			$gm->delete();
		}
		if(isset($_POST['redirect'])){
			header('Location: '.$_POST['redirect'].'&status=deleted');
		}
		break;
	case 'newCategory':
		$_POST['name'] = htmlspecialchars($_POST['name']);
		$category = new Category;
		$exist = $category->isCategoryExist( $_POST['name'] );
		if($exist){
		  echo 'Category already exist ';
		} else {
		  $category->storeFormValues( $_POST );
		  $category->insert();
		}
		if(isset($_POST['redirect'])){
			if($exist){
				header('Location: '.$_POST['redirect'].'&status=exist');
			} else {
				header('Location: '.$_POST['redirect'].'&status=added');
			}
		}
		break;
	case 'addGame':
		add_game();
		break;
	case 'updateLogo':
		upload_logo();
		break;
	case 'updateStyle':
		update_style();
		break;
	case 'updateLayout':
		update_layout();
		break;
	case 'siteSettings':
		site_settings();
		break;
	case 'set_save_thumbs':
		set_save_thumbs();
		break;
	case 'updater':
		updater();
		break;
	case 'check_update':
		check_update();
		break;
	default:
		exit;
	}

function add_game(){
	$_POST['description'] = html_purify($_POST['description']);
	$_POST['instruction'] = html_purify($_POST['instruction']);
	$redirect = 0;
	if(isset($_POST['redirect'])){
		$redirect = $_POST['redirect'];
	}
	$game = new Game;
	$check=$game->getByTitle($_POST['title']);
	if(is_null($check)){
		if(IMPORT_THUMB){
			import_thumb($_POST['thumb_2']);
			$name = basename($_POST['thumb_2']);
			$_POST['thumb_2'] = '/thumbs/'.$name;
			//
			import_thumb($_POST['thumb_1']);
			$name = basename($_POST['thumb_1']);
			$_POST['thumb_1'] = '/thumbs/'.$name;
		}
		$game->storeFormValues( $_POST );
		$game->insert();
		$status='added';
		//
		$cats = commas_to_array($_POST['category']);
		if(is_array($cats)){ //Add new category if not exist
			$length = count($cats);
			for($i = 0; $i < $length; $i++){
				$_POST['name'] = $cats[$i];
				$category = new Category;
				$exist = $category->isCategoryExist($_POST['name']);
				if($exist){
				  //
				} else {
				  $category->storeFormValues( $_POST );
				  $category->insert();
				}
				$category->addToCategory($game->id, $category->id);
			}
		}
	}
	else{
		$status='already';
	}
	if(isset($_POST['source'])) {
		echo $status;
	}
	if($redirect){
		header('Location: '.$redirect.'&status='.$status);
	}
}
function upload_logo(){
	$redirect = 0;
	if(isset($_POST['redirect'])){
		$redirect = $_POST['redirect'];
	}
	$target_dir = "../images/";
	$file_name = strtolower(str_replace(' ', '-', basename($_FILES["logofile"]["name"])));
	$target_file = $target_dir . $file_name;
	$uploadOk = 1;
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	if(isset($_POST["submit"])) {
	  $check = getimagesize($_FILES["logofile"]["tmp_name"]);
	  if($check !== false) {
	    echo "File is an image - " . $check["mime"] . ".";
	    $uploadOk = 1;
	  } else {
	    echo "File is not an image.";
	    $uploadOk = 0;
	  }
	}
	if ($_FILES["logofile"]["size"] > 500000) {
	  echo "Sorry, your file is too large.";
	  $uploadOk = 0;
	}
	if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
	&& $fileType != "gif" ) {
	  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	  $uploadOk = 0;
	}
	if ($uploadOk == 0) {
	  echo "Sorry, your file was not uploaded.";
	} else {
	  if (move_uploaded_file($_FILES["logofile"]["tmp_name"], $target_file)) {
	    echo "The file ". basename( $_FILES["logofile"]["name"]). " has been uploaded.";
	    $filecontent=file_get_contents('../site-settings.php');
		$filecontent = str_replace('"SITE_LOGO", "'.SITE_LOGO.'"', '"SITE_LOGO", "images/'.$file_name.'"', $filecontent);
		file_put_contents("../site-settings.php", $filecontent);
	  } else {
	    echo "Sorry, there was an error uploading your file.";
	  }
	}
	if($redirect){
		header('Location: '.$redirect);
	}
}
function update_style(){
	file_put_contents('../'. TEMPLATE_PATH . '/style/style.css', $_POST['style']);
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'&status=saved');
	}
}
function update_layout(){
	foreach ($_POST as $item => $value) {
		if(substr($item, -3) == 'php'){
			$path = str_replace("_",".",$item);
			file_put_contents('../'. TEMPLATE_PATH . '/'.$path, $value);
		}
	}
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'&status=saved');
	}
}
function site_settings(){
	$filecontent=file_get_contents('../site-settings.php');
	$filecontent = str_replace('"SITE_TITLE", "'.SITE_TITLE.'"', '"SITE_TITLE", "'.htmlspecialchars($_POST['title']).'"', $filecontent);
	$filecontent = str_replace('"SITE_DESCRIPTION", "'.SITE_DESCRIPTION.'"', '"SITE_DESCRIPTION", "'.htmlspecialchars($_POST['description']).'"', $filecontent);
	$filecontent = str_replace('"META_DESCRIPTION", "'.META_DESCRIPTION.'"', '"META_DESCRIPTION", "'.htmlspecialchars($_POST['meta_description']).'"', $filecontent);
	$filecontent = str_replace('"THEME_NAME", "'.THEME_NAME.'"', '"THEME_NAME", "'.$_POST['theme'].'"', $filecontent);
	file_put_contents("../site-settings.php", $filecontent);
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'&status=saved');
	}
}
function set_save_thumbs(){
	$bool = 'false';
	if(IMPORT_THUMB){
		$bool = 'true';
	}
	$val = 'false';
	if(isset($_POST['save_thumbs'])){
		$val = 'true';
	}
	$filecontent = file_get_contents('../site-settings.php');
	$filecontent = str_replace('"IMPORT_THUMB", '.$bool, '"IMPORT_THUMB", '.$val, $filecontent);
	file_put_contents("../site-settings.php", $filecontent);
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'&status=saved');
	}
}
function upload_thumb($url){
	if($url) {
		$data = file_get_contents($url);
		$name = basename($url);
		$new = '../thumbs/'.$name;
		file_put_contents($new, $data);
	}
}
function import_thumb($url){
	if($url) {
		$name = basename($url);
		$new = '../thumbs/'.$name;
		compressImage($url, $new , COMPRESSION_LEVEL);
	}
}
function compressImage($source, $destination, $quality) {
  $info = getimagesize($source);
  if ($info['mime'] == 'image/jpeg') 
    $image = imagecreatefromjpeg($source);
  elseif ($info['mime'] == 'image/gif') 
    $image = imagecreatefromgif($source);
  elseif ($info['mime'] == 'image/png') 
    $image = imagecreatefrompng($source);
  imagejpeg($image, $destination, $quality);
}
function check_update(){
	$ch = curl_init(DOMAIN.'verify/verify.php?action=latest');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curl = curl_exec($ch);
	$data = json_decode($curl, true);
	if(isset($data['log'])){
		echo(json_encode($data));
	}
	curl_close($ch);
}
function updater(){
	$status = 'null';
	$info_data = '';
	$code = esc_string($_POST['code']);
	if(true){
		$ch = curl_init('https://api.cloudarcade.net/verify/verify.php?code='.$code.'&v='.VERSION);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$curl = curl_exec($ch);
		$data = json_decode($curl, true);
		if(isset($data['log'])){
			backup();
			if(isset($data['content'])){
				$path = $data['path'];
				file_put_contents("rf_execute.php", htmlspecialchars_decode($data['content']));
				include 'rf_execute.php';
				unlink('rf_execute.php');
			}
			$status = 'updated';
		} elseif(isset($data['error'])) {
			$status = 'error';
			$info_data = $data['description'];
		} else {
			$status = 'error';
			$info_data = json_encode($data);
		}
		$info = curl_getinfo($ch);
		curl_close($ch);
	}
	if(isset($_POST['redirect'])){
		header('Location: '.$_POST['redirect'].'&status='.$status.'&info='.$info_data);
	}
}
function backup(){
	$rootPath = realpath('../');
	$zipname = '../admin/backups/'.$_SESSION['username'].'-cloudarcade-backup-'.time().'.zip';
	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	if($zip){
		$filter = array('\verify', '\vendor', '\thumbs', '\games', '\backups');
		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
			RecursiveIteratorIterator::LEAVES_ONLY,
		);

		foreach ($files as $name => $file)
		{
			if (!blacklist($file, $filter)) {
			   // Is this a directory?
				if (!$file->isDir())
				{
					// Get real and relative path for current file

					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
				else {
					$end2 = substr($file,-2);
					if ($end2 == "/.") {
						$folder = substr($file, 0, -2);
						$zip->addEmptyDir($folder);
					}
				}
			}
		}
		$zip->close();
	}
}
function blacklist($str, $filter){
	foreach ($filter as $key) {
		if (strpos($str, $key) == true) {
			return true;
		}
	}
	return false;
}
?>