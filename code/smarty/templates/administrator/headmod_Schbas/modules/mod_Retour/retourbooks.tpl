{extends file=$retourParent}{block name=content}


<script language="javascript" type="text/javascript">
<!-- 
//influenced by www.tizag.com
function ajaxFunction(){
	var ajax;
	
	try{
		//others
		ajax = new XMLHttpRequest();
	} catch (e){
		// IE
		try{
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				alert("Browser wird nicht unterstuetzt!");
				return false;
			}
		}
	}
	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4){
			var ajaxDisplay = document.getElementById('booklist');
			ajaxDisplay.innerHTML = ajax.responseText;
		}
	}
	var barcode = document.getElementById('barcode').value;
	var queryString = "inventarnr=" + encodeURIComponent(barcode) + "&card_ID=" + {$cardid} + "&uid=" + {$uid} + "&ajax=1";
	ajax.open("GET", "http://{$adress}" + queryString, true);
	ajax.send(null); 
}

//-->
</script>


<script language="javascript" type="text/javascript">
<!-- 
//influenced by http://tommwilson.com
function enter_pressed(e){
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return false;
return (keycode == 13);
}

//-->
</script>

<form name='barcode_scan' onsubmit='return false;' />
Inventarnummer: <input type='text' id='barcode' onKeyPress='if(enter_pressed(event)) ajaxFunction() '/> <br />

</form>


<div id="booklist">	
	{foreach $data as $retourbook}
		{$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
{/block}