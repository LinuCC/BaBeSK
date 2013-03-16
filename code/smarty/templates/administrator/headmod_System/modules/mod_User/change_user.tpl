{extends file=$UserParent}{block name=content}
{literal}<!-- No Smarty-Variables wanted in JavaScript -->
<script type="text/javascript">

function displayChangeCardId() {
	document.getElementById("cardnumber").disabled = false;
	document.getElementById("cardnumber").focus();
	document.getElementById("showccid").style.display = 'none';
	document.getElementById("hideccid").style.display = 'inline';//show button to abandon changing cardID
	old_cardnumber = document.getElementById("cardnumber").value;
	document.getElementById("cardnumber").value = "";
}

function resetCardId() {
	document.getElementById("cardnumber").value = old_cardnumber;
	document.getElementById("hideccid").style.display = 'none';
	document.getElementById("showccid").style.display = 'inline';//show button to change cardID
	document.getElementById("cardnumber").disabled = true;
	document.getElementById("cardiderror").style.display = 'none';
}

function checkCardId() {
	is_okay = document.getElementById("cardnumber").value.search(/[a-z0-9]{10}/);
	if(is_okay != -1) {
		document.getElementById("cardiderror").style.display = 'none';
	}
	else {
		document.getElementById("cardiderror").style.display = 'inline';
	}
}
</script>
{/literal}

<form action="index.php?section=System|User&action=4&ID={$user.ID}"
	method="post" onsubmit="submit()">
	<fieldset>
		<legend>Persönliche Daten</legend>
		<label>ID des Users:<input type="text" name="id" maxlength="10"
			width="10" value={$user.ID}>
		</label><br> <br> <label>Kartennummer des Users:<input id="cardnumber"
			type="text" name="cardnumber" maxlength="10" width="10"
			value={$cardnumber} onblur="checkCardId()" disabled>
			<button id="showccid" type="button" onclick="displayChangeCardId()">KartenID verändern</button>
			<button id="hideccid" type="button" onclick="resetCardId()" style="display:none">KartenID doch nicht verändern</button>
			<p id="cardiderror" class="error" style="display:none;">Die KartenID ist nicht richtig eingegeben worden.</p>
		</label> <br> <br> <label>Vorname:<input type="text" name="forename"
			value="{$user.forename}" />
		</label><br> <br> <label>Name:<input type="text" name="name"
			value="{$user.name}" />
		</label><br> <br> <label>Benutzername:<input type="text"
			name="username" value="{$user.username}" />
		</label><br> <br> <label>Passwort ändern:<input type="password"
			name="passwd" />
		</label><br> <br> <label>Passwortänderung wiederholen:<input
			type="password" name="passwd_repeat" />
		</label><br> <br> Geburtstag : {html_select_date
		time="{$user.birthday}" start_year="-100"}<br> <br> <label>Konto
			sperren:<input type="checkbox" name="lockAccount" value="1" {if $user.locked}checked{/if}/>
		</label><br> <br> <label>Teilhabepaket:<input type="checkbox" name="soliAccount" value="1" {if $user.soli}checked{/if}/>
		</label>
	</fieldset>
	<br>
	<fieldset>
		<legend>Identitätsinformationen</legend>
		<br> <br> <label>Klasse:<input type="text" name="class"
			value="{$user.class}" />
		<br> <br> <select name="gid"> {html_options values=$gid
			output=$g_names selected="{$user.GID}"}
		</select> <label>Guthaben:<input type="text" name="credits" size="5"
			maxlength="5" value="{$user.credit}" />
		</label>
	</fieldset>
	<br> <input id="submit" onclick="submit()" type="submit" value="Submit" />
</form>
<div align="right"><form action="index.php?section=System|User&action=3&ID={$user.ID}" method="post"><input type='submit' value='löschen'></form></div>

{/block}