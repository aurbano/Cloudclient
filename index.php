<?php
include('lib/php/session.php');
if($sess->logged()){
	include('client.php');
	die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cloudclient | Dropbox web client</title>
<style type="text/css" media="all">
html,body{
	padding:0;
	margin:0;
	width:100%;
	height:100%;
	font-family: "Century Gothic","Avant Garde Gothic",sans-serif;
	overflow:hidden;
	font-size:18px;	
}
body{
	background:#0a4a8e url(img/body/bg.png) no-repeat bottom left;
}
a, a:visited{
	color:inherit;
	text-decoration:none;
}
a:hover{
	text-decoration:underline;	
}
#box{
	position:relative;
	background:rgba(255,255,255,0.3);
	padding:20px;
	margin:20% 0 0 0;
	text-align:center;
	color:#FFF;
	box-shadow:#004264 0 0 20px;
}
#box h1{
	position:absolute;
	font-weight:normal;
	left:0;
	font-size:60px;
	top:-10px;
	margin:0;
	padding:0;
	color:white;
	color:rgba(255,255,255,0.5);	
}
#box:hover h1{
	color:rgba(255,255,255,0.8);
}
#box a:hover{
	color:#FFC;
	text-decoration:underline;	
}
#nav{
	color:#74b2f5;
	position:absolute;
	top:5px;
	right:5px;
}
</style>
<link rel="stylesheet" type="text/css" href="/lib/jqueryUI/css/redmond/jquery-ui-1.9.1.custom.min.css" media="all" />
</head>

<body>
<div id="nav"><a href="/reset">Reset</a></div>
<div id="box">
	<h1>Cloudclient</h1>
	<div id="login" class="hideOnAction"><a href="<?php echo $dp->loginLink(); ?>" class="externalLogin">Log in via Dropbox</a></div>
</div>
<script type="text/javascript" language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="/lib/jqueryUI/js/jquery-ui-1.9.1.custom.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.externalLogin').click(function(event){
		event.preventDefault();
		$('.hideOnAction').hide().after('<div style="text-align: center;"><img src="/img/load/index2.gif" alt="Cargando..." /><p>Conectando</p></div>');
		var url = $(this).attr('href');
		//var oauthWindow = window.open(url,'Conectando',"height=500,width=700,scrollTo,resizable=0,scrollbars=0,location=0");
		var oauthWindow = window.open(url,'Conectando',"height="+$(window).height()+",width="+$(window).width()+",scrollTo,resizable=0,scrollbars=0,location=0");
		var oauthInterval = window.setInterval(function(){
			if (oauthWindow.closed) {
				window.clearInterval(oauthInterval);
				window.location.reload();
			}
		}, 500);	
	});
	$('#box').draggable();
});
</script>
</body>
</html>