Am {$date} das Menü "{$name}" bestellen?
<br>

<form method="POST"
	action="index.php?section=order&order={$order_id}">
	<input type="submit" name="OK" value="Bestellen">
	
<form method="POST"
	action="index.php?section=order&order={$order_id}">
	<input type="submit" name="CANCEL" value="Abbrechen">
</form>