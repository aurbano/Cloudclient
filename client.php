<?php
if(!($sess instanceof Session)) die('Forbidden');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/lib/entypo/entypo.css" type="text/css">
<title>Cloudclient | Dropbox web client</title>
<style type="text/css" media="all">
html,body{
	background:#0a4a8e url(img/body/bg.png) no-repeat bottom left;
	padding:0;
	margin:0;
	font-family: "Century Gothic","Avant Garde Gothic",sans-serif;
	overflow:hidden;
	height:100%;
	font-size:1em;
}
a, a:visited, a:focus, a:link{
	outline:none;
	text-decoration:none;
	color:inherit;
}
#header{
	color:white;
	width:90%;
	margin:2em 5%;
	z-index:9;
	position:relative;
}
#header, .item .name{
	text-shadow:#069 0 1px 1px;
}
#header h1{
	padding:0;
	font-weight:100;
	font-size:300%;
	display:inline-block;
	margin:0;
}
#breadcrumbs{
	color:#96c3f5;
	display:block;
	position:absolute;
	left:20em;	
	top:1.3em;
}
#breadcrumbs a{
	color:#FFF;
	margin:0 5px;	
}
#breadcrumbs a:hover{
	color:#FF9;
}
#nav{
	color:#96c3f5;	
	width:40%;
	position:absolute;
	top:1em;
	right:6em;
	display:inline-block;
}
#nav ul{
	margin:0;
	padding:0;
	list-style:none;
	font-size:21px;	
}
#nav ul li{
	display:inline-block;
	width:32%;
	text-align:center;
}
#nav a:hover{
	color:#fff;	
}
#mainOpts a span{
	position:absolute;
	top:-3px;
	left:3px;
	width:11px;
	height:3px;
	background:inherit;
}
#mainOpts{
	position:absolute;
	right:1em;
	top:1.2em;	
}
#mainOpts a span{
	position:absolute;
	top:-3px;
	left:3px;
	width:11px;
	height:3px;
	background:inherit;
}
#sidebar{
	z-index:9;	
	width:60px;
	color:white;
	position:absolute;
}
#back{
	color: #54e8f9;
    position: absolute;
    top: 430px;
    left: 20px;
    font-size: 100px;
    font-family: courier;
	display:none;
	z-index:99;
}
#back a:hover{
	color:#FFF;	
}
#viewport{
	/*hey! take care of the sidebar too*/
	margin-left:60px;
	overflow:hidden;
}
.view{
	overflow:auto;
	position:absolute;
}
.view>.content{
	padding:20px;	
	line-height:30px;
}
.item{
	float:left;
	color:#FFF;
	border:transparent 1px solid;
	border-radius:15px;
}
.view .under{
	border:#96C3F5 1px solid;
	background:rgba(255,255,255,0.2);
}
.item a{
	display:block;
	text-align:center;
	margin:10px 20px;	
	position:relative;
	color:inherit;
}
.item a .icon{
	width:95%;
	height:65%;
	border-radius:4px;
	position:relative;
}
.item a .folder{
	background:#f9dd63;	 /* f7e07c */
}
.item a > .folder{
	box-shadow:#555 0 0 10px;	
}
.item a .file{
	background:#FFF;
	box-shadow:inset #ccc 0 0 20px;
}
.item a .zip{
	background:#f7e07c url(img/icons/zip.png) no-repeat 50%;
	box-shadow:inset #e7d174 0 0 20px;
}
.item a:hover .zip{
	background:#f8d745 url(img/icons/zip.png) no-repeat 50%;
}
.item a .file .preview{
	color:#CCC;
	font-size:9px;
	text-align:left;
	padding:7px 15px;
	overflow:hidden;
}
.item a .icon .folder{
	position:absolute;
	width:30%;
	height:20px;
	left:10px;
	border-radius:2px;
	top:-5px;
}
.item a .icon .type{
	position:absolute;
	bottom:0px;
	right:0px;
	width:43px;
	height:43px;
	overflow:hidden;	
}
.item a .icon .format{
	position:absolute;
	background:#eb0000;
	box-shadow:inset #666 0 0 7px;
	top:30px;
	right:-7px;
	width:53px;
	height:23px;
	overflow:hidden;	
}
.item a .icon .loading{
	position:absolute;
	top:44%;
	width:100%;
	text-align:center;
	display:none;
}
.item a .name{
	position:absolute;
	width:100%;
	left:0;
	top:75%;
	word-wrap: break-word;
}
.item a:hover .folder, .item .selected, .item .selected .folder{
	background:#f8d745 !important;
}
.helper{
	background:#09F;
	color:#FFF;
	padding:5px 10px;
	border-radius:20px;	
}
#loading{
	width:100%;
	text-align:center;
	margin-top:100px;
	color:#FFF;
}
#infoBox{
	position:absolute;
	top:140px;
	width:100%;
	left:0;
	display:none;	
}
#infoBox h2{
	text-align:center;	
}
#infoBox a{
	color:#FFF;	
}
#infoBox .close{
	position:absolute;
	top:-26px;
	right:-26px;	
}
#infoBox > div{
	width:800px;
	background:rgba(255,255,255,0.6);
	color:#0A4A8E;
	padding:20px 40px;
	margin:0 auto;
	box-shadow:#0A4A8E 0 0 15px;
	position:relative;	
}
.iconSize{
	border:#999 solid 1px;
	background:#0a498e;
	padding:3px;
	margin-left:5px;	
}
.iconSize:hover{
	border:#fff solid 1px;
}


@media screen and (max-width: 960px) {
	#nav{
		position:static;
		width:100%;
		margin:1em 0;
	}
}
@media screen and (max-width: 768px) {

}
@media screen and (max-width: 640px) {
	html,body{
		font-size:95%;
	}
	
	#breadcrumbs{
		margin:1em 0;
		position:static;
	}
}
@media screen and (max-width: 480px) {
	#nav ul li{
		width:100%;
		display:block;
		margin:0.5em 0;
	}
}
@media screen and (max-width: 320px) {
	html, body{
		font-size:90%;
	}
}

#context-menu{
    z-index:1000;
    position: absolute;
    background-color:#fff;
    padding: 0px;
	display:none;
	box-shadow:#555 0 0 10px;
	font-family:Tahoma, Geneva, sans-serif;
	padding:0 1px 1px 0;
}
#context-menu ul{
	margin:0;
	padding:0;
	list-style:none;
}

#context-menu ul li,#context-menu ul li{
	display:inline-block;
	text-align:center;
	margin:1px 0 0 1px;
}
#context-menu ul li,#context-menu ul li a{
	display:inline-block;
	text-decoration:none;
	background:#0a498e;
	color:#fff;
	width:98px;
	height:98px;
	font-size:90%;
}
#context-menu ul li a.red{background:#C00;}
#context-menu ul li a:hover{
	background:#009;
}
#context-menu ul li a i{
	font-size:5.25em;
	display:block;
	width:auto;
	margin-right:0;
}
#bgLoader{
	position: absolute;
	top: 10px;
	left: 40%;
	display:none;
}
</style>
<link rel="stylesheet" type="text/css" href="/lib/jqueryUI/css/redmond/jquery-ui-1.9.1.custom.min.css" media="all" />
<link rel="stylesheet" type="text/css" href="lib/lightbox/css/jquery.lightbox-0.5.css" media="screen" />
</head>

<body>
<div id="header">
    <h1>Cloudclient</h1>
	<div id="nav">
		<ul>
			<li><a href="#about" rel="info" title="More information">About</a></li>
			<li><a href="#contact" rel="info" title="Show contact options">Contact us</a></li>
			<li><a href="/reset" title="Close current session">Logout</a></li>
		</ul>
	</div>
    <div id="breadcrumbs">Start</div>
    <div id="mainOpts">
	    <a href="#addFolder" class="iconSize" style="padding:3px 11px; background:#f9dd63; position:relative; color:#0a498e" title="Add folder"><span></span>+</a>
    	<a href="#icons" rel="6" class="iconSize" style="padding:3px 9px;" title="Bigger icons">+</a><a href="#icons" rel="-6" class="iconSize" style="padding: 1px 6px; font-size: 9px;" title="Smaller icons">-</a>
    </div>
</div>
<div id="back"><a href="#back" rel="1">&lt;</a></div>
<div id="sidebar"></div>
<div id="viewport">
	<div id="loading"><img src="/img/load/client.gif" alt="Cargando..." style="margin-right:15px;" /> Loading...</div>
</div>
<div id="infoBox">
	<div id="about">
		<h2>How?</h2>
		<p>The frontend is entirely built in JavaScript, using jQuery and jQuery UI. There are two "classes", Client and UI. Client handles all the data, and UI makes sure it's nicely displayed.</p>
		<p>In order to safely interact with the server we use a PHP "proxy" that talks with the services' APIs</p>
		<p>The icons are actually pure HTML, except for the nice zipper in compressed files.</p>
		<p>Check out the source code in <a href="https://github.com/aurbano/Cloudclient">Github</a>!</p>
		<h2>Who?</h2>
		<p>The entire client was developed by me, <a href="http://urbanoalvarez.es">Alejandro U. Alvarez</a> and my friend <a href="http://jaicab.com">Jaime Caballero</a></p>
		<a href="#close" class="close"><img src="/img/close.png" /></a>
	</div>
	<div id="contact">
		<h2>Get in touch</h2>
		<p><a href="https://twitter.com/alalex">Twitter</a> &bull; <a href="http://www.facebook.com/a.urbano.a">Facebook</a> &bull; <a href="http://www.linkedin.com/in/aurbano">LinkedIn</a></p>
		<p>Or through my website <a href="http://urbanoalvarez.es">UrbanoAlvarez.es</a></p>
		<a href="#close" class="close"><img src="/img/close.png" /></a>
	</div>
</div>
<div id="context-menu">
<ul><span id="fileOps" style="display:none;">
<li><a href="#fileOps" rel="rename"><i class="icon-pencil"></i>Rename</a></li><li><a href="#fileOps" rel="copy"><i class="icon-floppy"></i>Copy</a></li><li><a href="#fileOps" rel="cut"><i class="icon-popup"></i>Cut</a></li><br /><li><a href="#fileOps" rel="delete" class="red"><i class="icon-trash"></i>Delete</a></li></span><li id="pasteOp" style="display:none"><a href="#fileOps" rel="paste"><i class="icon-docs"></i>Paste</a></li><li><a href="#addFolder"><i class="icon-folder"></i>New folder</a></li><li><a href="#about" rel="info"><i class="icon-help"></i>Help</a></li></ul></div>
<div id="bgLoader"><img src="/img/load/horizontal.gif" width="220" height="20" alt="Loading..." /></div>
<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<script type="text/javascript" language="javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript" src="/lib/jqueryUI/js/jquery-ui-1.9.1.custom.min.js"></script>
<script src="/lib/punch/touch_punch.min-0.2.2.js"></script>
<script type="text/javascript" src="/lib/lightbox/js/jquery.lightbox-0.5.min.js"></script>
<script type="text/javascript" language="javascript" src="lib/js/UIview.js"></script>
<script type="text/javascript" language="javascript" src="lib/js/client.js"></script>
</body>
</html>