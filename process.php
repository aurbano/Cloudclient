<?php
// Process AJAX requests
function finish($msg,$done=false,$info=null){
	$ret = array('done'=>$done,'msg'=>$msg);
	if(is_array($info)) array_merge($ret,$info);
	die(json_encode($ret));	
}
if(!$_POST['type'] || strlen($_POST['type'])<1){
	finish('400 Bad request');
}
include('lib/php/session.php');
if(!$sess->logged()) finish('500 Not authorised');
if(!$dp->logged()) finish('500 Not authorised (Dropbox)');
switch($_POST['type']){
	case 'list':
		// User is logged in
		if(!$_POST['dir'] || strlen($_POST['dir'])<1) $_POST['dir'] = false;
		// List files
		try{
			$files = $dp->dir($_POST['dir']);
		}catch(Exception $e){
			 finish($e->getMessage());
		}
		if(!$files) finish('Nothing returned from Dropbox');
		die(json_encode(array('done'=>true,'msg'=>'','contents'=>$files)));	
		break;
	case 'root':
		die(json_encode(array('done'=>true,'msg'=>'','id'=>$dp->root())));	
		break;
	case 'move':
		if(strlen($_POST['elem'])<1 || strlen($_POST['dest'])<1){
			finish('400 Bad request');
		}
		// All good, let's move!
		try{
			$dp->move($_POST['elem'],$_POST['dest'],$_POST['path']);
			finish('',true);
		}catch(Exception $e){
			 finish($e->getMessage());
		}
		break;
	case 'addFolder':
		if(strlen($_POST['name'])<1){
			finish('400 Bad request');
		}
		// All good, let's move!
		try{
			$dp->createFolder($_POST['name'],$_POST['path']);
			finish('',true);
		}catch(Exception $e){
			 finish($e->getMessage());
		}
		break;
	case 'rename':
		if(strlen($_POST['name'])<1 || strlen($_POST['elem'])<1){
			finish('400 Bad request');
		}
		try{
			$dp->rename($_POST['elem'],$_POST['name'],$_POST['path']);
			finish('',true);
		}catch(Exception $e){
			 finish($e->getMessage());
		}
		break;
	default:
		finish('400 Bad request');	
}