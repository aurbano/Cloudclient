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
		if(!$_POST['dir'] || !is_numeric($_POST['dir']) || $_POST['dir'] < 0) $_POST['dir'] = NULL;
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
		if(!is_numeric($_POST['elemID']) || $_POST['elemID'] < 1 || !is_numeric($_POST['destID']) || $_POST['destID'] < 1 || strlen($_POST['elemName'])<1 || strlen($_POST['destName'])<1){
			finish('400 Bad request');
		}
		// All good, let's move!
		try{
			$dp->move($_POST['elemID'],$_POST['elemName'],$_POST['destID'],$_POST['destName'],$_POST['path']);
			finish('',true);
		}catch(Exception $e){
			 finish($e->getMessage());
		}
		break;
	default:
		finish('400 Bad request');	
}