<?php
// Dropbox login
// http://local.cloudclient.es/client.php?uid=57833018&oauth_token=q7w8jw8xkhp9sls
if(isset($_GET['uid']) && isset($_GET['oauth_token'])){
	// Get access tokens and go back
	include('lib/php/session.php');
	$dp = new Dropbox();
	$dp->login(); // Session is called from there
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cloudclient.es</title>
<script type="text/javascript" language="javascript">
<!--
window.close();
-->
</script>
</head>

<body>
Procesando... En breves serás redirigido a la aplicacion principal.
<small>Si no ocurre nada cierra esta ventana.</small>
</body>
</html>