{extends file=$UserParent}{block name=content}
Der Benutzer wurde verändert:<br><br>
ID: {$id}<br>
Gruppen-ID: {$gid}<br>
Vorname: {$forename}<br>
Name: {$name}<br>
Benutzername: {$username}<br>
Geburtstag: {$birthday}<br>
Klasse: {$class}<br>
Guthaben: {$credits}<br>
Gesperrt: {if $locked}ja{else}nein{/if}<br>
Teilhabepaket: {if $soli}ja{else}nein{/if}<br>
Kartenverluste: {$cardChanges}
{/block}