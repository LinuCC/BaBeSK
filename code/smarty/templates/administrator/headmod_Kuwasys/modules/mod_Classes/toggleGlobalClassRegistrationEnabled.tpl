{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">Erlauben der Kursregistrierungen</h2>

<form action="index.php?module=administrator|Kuwasys|Classes|GlobalClassRegistration&amp;toggleFormSend" method="post">
	<label>Kursregistrierungen erlauben: <input type="checkbox" name="toggle"
			{if $enabled}checked="checked"{/if}
			></label>
	<input type="submit" value="Absenden">
</form>

{/block}
