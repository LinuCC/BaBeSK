$('#barcodeInput').on('keyup', function(event) {
	if(event.keyCode == 13) {
		sendUserReturnedBarcode($(this).val());
	}
});

function sendUserReturnedBarcode(barcode) {
	$.ajax({
		'type': 'POST',
		'url': 'index.php?section=Schbas|SchbasAccounting&action=userSetReturnedFormByBarcodeAjax',
		data: {
			'barcode': barcode
		},
		success: function(data) {
			if(data == 'error') {
				toastr.error('Der Barcode ist nicht vollständig');
			}
			else if(data == 'entryNotFound') {
				toastr.error('Der Link zwischen Nachricht und Benutzer konnte nicht gefunden werden');
			}
			else if(data == 'notValid') {
				toastr.error('Der Barcode enthält inkorrekte Zeichen');
			}
			else if(data == 'dupe') {
				toastr.error(unescape('Formular wurde bereits eingescannt. Bei %C4nderungen bitte zuerst l%F6schen!'));
			}
			else {
				toastr.success('Einscannen erfolgreich!');
				location.reload();
			}
		},
		error: function(data) {
			toastr.error('Ein Fehler ist beim Senden des Barcodes aufgetreten!');
		}
	});
}
