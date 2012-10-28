{extends file=$booklistParent}{block name=content}

<form action="index.php?section=Schbas|Booklist&action=2&ID={$bookdata.id}"
	method="post">
	<fieldset>
		<legend>Buchdaten</legend>
		<label>ID des Exemplars: {$bookdata.id}</label><br> <br> 
		<label>Fach: <input type="text" name="subject" maxlength="10" value="{$bookdata.subject}"/></label> <br> <br> 
		<label>Klasse: <input type="text" name="class" maxlength="10" value="{$bookdata.class}"/></label><br> <br> 
		<label>Titel: <input type="text" name="title" maxlength="10" value="{$bookdata.title}"/></label><br> <br> 
		<label>Autor: <input type="text" name="author" maxlength="10" value="{$bookdata.author}"/></label><br> <br> 
		<label>Verlag:<input type="text" name="publisher" maxlength="10" value="{$bookdata.publisher}"/></label><br> <br> 
		<label>ISBN: <input type="text" name="isbn" maxlength="10" value="{$bookdata.isbn}"/></label><br> <br> 
		<label>Preis: <input type="text" name="price" maxlength="10" value="{$bookdata.price}"/></label><br> <br>
		<label>Bundle: <input type="text" name="bundle" maxlength="10" value="{$bookdata.bundle}"/></label><br> <br> 
	</fieldset>
	<input id="submit" type="submit" value="Abschicken" />
</form>

{/block}