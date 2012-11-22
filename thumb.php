<?php
include('lib/php/session.php');
if(!isset($_GET['file'])){
	header('HTTP/1.0 400 Bad request');
    die();
}
try{
	$fileinfo = $dp->dp->getMetaData($_GET['file'],false);
	$thumb = $dp->dp->getThumbnail($_GET['file'],'m');
}catch(Exception $e){
	header('HTTP/1.0 404 Not Found');
    die();
}
// Cache
header('Content-Type: '.$fileinfo['mime_type']);
header('Last-Modified: '.$fileinfo['modified']);
header("Pragma: cache");
header("Cache-Control: max-age=86400");
echo $thumb;