{extends file=$inh_path}
{block name=content}

<script type="text/javascript" src="{$path_js}/ckeditor/ckeditor.js"></script>

<h2 class="module-header">Neue Vorlage</h2>

<form action="index.php?section=Schbas|SchbasMessages&amp;action=addTemplate" method="POST">
	<fieldset class="blockyField">
		<legend>Vorlagendaten</legend>
		<label>Titel:<input type="text" name="templateTitle" value=""></label><br /><br />
		<label>Text:<textarea class="ckeditor" name="templateText"></textarea></label>
	</fieldset>
	<input type="submit" value="Vorlage hinzufügen" />
</form>
{/block}