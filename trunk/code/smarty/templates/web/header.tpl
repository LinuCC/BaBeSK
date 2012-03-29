<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>{$title|default:'BaBeSK'}</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="../smarty/templates/web/css/general.css" type="text/css" />
	{literal}
<script type="text/javascript">
var oldDiv = '';

function switchInfo(divName) {

	if(oldDiv == divName) {
		if(document.getElementById(divName).style.display == 'inline')
			document.getElementById(divName).style.display = 'none';
		else
			document.getElementById(divName).style.display = 'inline';
	}
	else {
		document.getElementById(divName).style.display = 'inline';
		if(oldDiv != '') {
			document.getElementById(oldDiv).style.display = 'none';
		}
	}
	oldDiv = divName;
}

</script>
{/literal}
<!-- ------------------------------------------------------------ -->
<!-- --------------------JAVASCRIPT ENDS HERE-------------------- -->
<!-- ------------------------------------------------------------ -->
</head>
<body>
<div id="header">
    <div id="top">
    <div id="top_left">
       <p>Name: {$username}</p>
       <p>Guthaben: {$credit} Euro</p>
       
       <a href="javascript:switchInfo('account')">Kontoeinstellungen</a><br />
    
    <div id="account" style="display: none;">
	
		
			<a href="index.php?section=account">Karte sperren</a>
			<br>
			<a href="index.php?section=change_password">Passwort &auml;ndern</a>
	
	
	</div>   
       
    
    
    </div>
    <div id="top_right">
       <a href="index.php?section=help">Hilfe</a><br />
       <a href="index.php?action=logout">Ausloggen</a>
    </div>
    <div id="heading">
       <a href="index.php"><h1>LeGeria Online</h1></a>
    </div>
  </div>
</div>

<div id="info">
    <p>Wilkommen auf der Seite der LeGeria! :)</p>
</div>
<div id="main">
<div id="content">
{if $status != ''}
    <h4>{$status}</h4>
{/if}