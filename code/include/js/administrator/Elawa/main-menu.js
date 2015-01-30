// Generated by CoffeeScript 1.8.0
$(document).ready(function() {
  var $selectionsSwitch, changeSelections, displaySelectHostGroup;
  changeSelections = function($btnSwitch, state) {
    var onSuccess;
    onSuccess = function(data, statusText, jqXHR) {
      var val;
      val = data === 'true' ? 'freigegeben' : 'nicht freigegeben';
      if (jqXHR.status === 204) {
        return toastr.success("Die Wahlen sind nun " + val, 'Wahl-Erlaubnis erfolgreich verändert');
      } else if (jqXHR.status === 201) {
        return toastr.info("Es wurde nichts verändert, die Wahlen sind " + val, 'Die Wahlen sind bereits so gesetzt');
      }
    };
    return $.ajax({
      type: 'POST',
      url: 'index.php?module=administrator|Elawa|SetSelectionsEnabled',
      data: {
        areSelectionsEnabled: state
      },
      success: onSuccess,
      error: function(jqXHR, textStatus, errorThrown) {
        toastr.error('Ein Fehler ist beim Bearbeiten aufgetreten.');
        return console.log(jqXHR);
      }
    });
  };
  displaySelectHostGroup = function() {
    var form, hosts;
    hosts = ["abc", "cde", "zdf"];
    form = "<div class='row'> <div class='col-md-4'> <label for='host-group-select'>Gruppe auswählen:</label> </div> <div class='col-md-8'> <select id='host-group-select' name='host-group-select'> </select> </div> </div>";
    return bootbox.dialog({
      title: "Ändern der Gruppe der Lehrer",
      message: form,
      "buttons": {
        success: {
          label: "Gruppe ändern",
          className: "btn-success",
          callback: function() {
            return alert('ToDo');
          }
        }
      }
    });
  };
  $selectionsSwitch = $('#enable-selections');
  $selectionsSwitch.bootstrapSwitch();
  $selectionsSwitch.on('switchChange.bootstrapSwitch', function(event, state) {
    var $btnSwitch, stateText;
    stateText = state ? "aktivieren" : "deaktivieren";
    $btnSwitch = $(this);
    return bootbox.confirm("Wollen sie die Wahlen für die Schüler wirklich " + stateText + "?", function(res) {
      if (res) {
        return changeSelections($btnSwitch, state);
      } else {
        return $btnSwitch.bootstrapSwitch('state', !state, true);
      }
    });
  });
  return $('a#select-host-group-button').on('click', function(event) {
    return displaySelectHostGroup();
  });
});