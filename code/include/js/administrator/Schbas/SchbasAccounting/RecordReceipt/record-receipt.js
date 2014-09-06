$(document).ready(function() {

	var activePage = 1;
	//Contains if the page should only show users with an missing (unpaid) amount
	var showOnlyMissing = false;
	var charCodeToSelector = {
		33: 1, 34: 2, 167: 3, 36: 4, 37: 5, 38: 6, 47: 7, 40: 8, 41: 9, 61: 0
	};

	dataFetch();
	var $pagination = $('#page-select');

	$pagination.bootpag({
		total: 1,
		page: activePage,
		maxVisible: 9
	}).on('page', activePageChange);

	$('#filter').enterKey(dataFetch);
	$('#search-submit').on('click', dataFetch);

	$('table#user-table').on('click', 'tbody tr', userClicked);

	$('#credits-change-form').on('submit', paidAmountChange);

	$('#credits-change-input').enterKey(function(event) {
		event.preventDefault();
		$('#credits-change-form').submit();
	});

	$('#credits-change-modal').on('shown.bs.modal', function(event) {
		$(this).find('input#credits-change-input').focus().select();
	});

	$('#credits-change-modal').on('hidden.bs.modal', function(event) {
		$('input#filter').focus().select();
	});

	$('#credits-change-modal').on('keypress', payFullAmount);

	$('body').on('keypress', executeSelectorKey);

	$('button.preset-credit-change').on('click', creditChangeByPreset);

	$('button#show-missing-amount-only').on('click', showMissingOnlyToggle);

	$('input#credits-add-input').enterKey(creditAddToByInput);

	function dataFetch() {

		var filter = $('#filter').val();
		$.postJSON(
			'index.php?module=administrator|Schbas|SchbasAccounting|RecordReceipt',
			{
				'filter': filter,
				'activePage': activePage,
				'showOnlyMissing': showOnlyMissing
			},
			success
		);

		function success(res, textStatus, jqXHR) {
			console.log(res);
			if(jqXHR.status == 200) {
				if(typeof res.value !== 'undefined' && res.value == 'error') {
					toastr.error('Ein Fehler ist aufgetreten!');
				}
				else {
					tableFill(res.users);
					pageSelectorUpdate(res.pagecount);
				}
			}
			else if(jqXHR.status == 204) {
				toastr.error('Keinen Benutzer gefunden!');
				$('#filter').focus().select();
			}
			else {
				toastr.error(
					'Fehler! Unbekannter Status ' + toString(jqXHR.status)
				);
			}
		};
	};

	function tableFill(users) {

		var html = microTmpl(
			$('#user-table-template').html(),
			{'users': users}
		);
		$('#user-table').html(html);
	};

	function pageSelectorUpdate(pagecount) {
		$pagination.bootpag({
			total: pagecount,
			page: activePage,
			maxVisible: 9
		});
	};

	function activePageChange(event, pagenum) {
		activePage = pagenum;
		dataFetch();
	};

	function userClicked(event) {

		var $row = $(event.target).closest('tr');
		var $modal = $('#credits-change-modal');
		var userId = $row.data('user-id');
		var username = $row.children('td.username').text();
		var paid = parseFloat($row.children('td.payment-payed').text());
		var toPay = parseFloat($row.children('td.payment-to-pay').text());
		$modal.find('.username').html(username);
		$modal.find('.credits-before').html(toPay.toFixed(2) + '€');
		var $input = $modal.find('input#credits-change-input');
		$input.val(paid.toFixed(2));
		$input.data('user-id', userId);
		$('#credits-change-modal').modal();
	};

	function paidAmountChange(event) {

		event.preventDefault();
		var $modal = $('#credits-change-modal');
		var $input = $modal.find('input#credits-change-input');
		var amount = $input.val().replace(",", ".");
		var userId = $input.data('user-id');

		$.ajax({
			'type': 'POST',
			'url': 'index.php?module=administrator|Schbas|SchbasAccounting|\
				RecordReceipt',
			'data': {
				"userId": userId,
				"amount": amount
			},
			'success': success,
			'error': error,
			'dataType': 'json'
		});

		//$.postJSON(
		//	'index.php?module=administrator|Babesk|Recharge|RechargeCard',
		//	{
		//		"userId": userId,
		//		"credits": credits
		//	},
		//	success
		//);

		function success(res) {

			var $row = $('table#user-table tbody')
				.find('tr[data-user-id=' + res.userId + ']');
			$row.find('td.payment-payed')
				.html(parseFloat(res.paid).toFixed(2) + ' €');
			$textCont = $row.find('td.payment-missing span')
			$textCont.html(parseFloat(res.missing).toFixed(2) + ' €');
			var col = '';
			if(res.missing > 0) {
				col = 'text-warning';
			} else if(res.missing == 0) {
				col = 'text-success';
			} else {
				col = 'text-danger';
				$textCont.prepend('Überschuss!');
			}
			$textCont.removeClass().addClass(col);
			toastr.success('Zahlungsbetrag erfolgreich verändert.');
			$modal.modal('hide');
		};

		function error(jqXHR) {

			console.log(jqXHR);
			if(jqXHR.status == 500) {
				if(typeof jqXHR.responseJSON !== 'undefined' &&
					typeof jqXHR.responseJSON.message !== 'undefined') {
					toastr.error(jqXHR.responseJSON.message, 'Fehler');
				}
				else {
					toastr.error('Ein Fehler ist beim Ändern aufgetreten');
				}
			}
			else {
				toastr.error('Konnte die Serverantwort nicht lesen!', 'Fehler');
			}
		};
	};

	function executeSelectorKey(event) {

		if(event.shiftKey == true) {
			if(charCodeToSelector[event.charCode] != undefined &&
				charCodeToSelector[event.charCode] != "undefined"
			) {
				event.preventDefault();
				var num = charCodeToSelector[event.charCode];
				$('table#user-table tbody td.selector[data-selector=' + num + ']')
					.closest('tr').click();
			}
		}
	};

	function creditChangeByPreset(event) {

		event.preventDefault();
		var $input = $('input#credits-change-input');
		var amount = parseInt($(this).text());
		var prevAmount = parseFloat($input.val().replace(",", "."));
		$input.val((prevAmount + amount).toFixed(2));
		$input.focus();
	};

	function creditAddToByInput(event) {

		//Dont let the enter-keypress bubble up to the form, so that the modal dont
		//closes
		//event.stopPropagation();
		event.preventDefault();
		var $input = $('input#credits-change-input');
		var addAmount = parseFloat($(this).val().replace(",", "."));
		var changeAmount = parseFloat($input.val().replace(",", "."));
		console.log((changeAmount + addAmount).toFixed(2));
		$input.val((changeAmount + addAmount).toFixed(2));
		$input.focus();
	};

	function showMissingOnlyToggle(event) {

		var $button = $(event.target);
		if($button.hasClass('active')) {
			$button.removeClass('active');
			showOnlyMissing = false;
			dataFetch();
		}
		else {
			$button.addClass('active');
			showOnlyMissing = true;
			dataFetch();
		}
	};

	function payFullAmount(event) {

		if(event.key == '+') {
			event.preventDefault();
			var toPay = parseFloat(
				$('#credits-change-modal').find('span.credits-before').text()
				).toFixed(2);
			$('input#credits-change-input').val(toPay);
		}
	}
});