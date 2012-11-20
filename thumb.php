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
$headers = getallheaders();
if(isset($headers['If-Modified-Since']) && $headers['If-Modified-Since'] == $fileinfo['modified']) {
	header('HTTP/1.1 304 Not Modified');
	exit();
}
header('Content-Type: '.$fileinfo['mime_type']);
header('Last-Modified: '.$fileinfo['modified']);
header("Pragma: cache");
header("Cache-Control: max-age=86400");
echo $thumb;