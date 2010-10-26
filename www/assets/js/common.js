_status_update = function() {
    if (Ajax.activeRequestCount > 0) {
        $('status_loading').show();
        $('status_complete').hide();
    } else {
        $('status_complete').show();
        $('status_loading').hide();
    }
}
Ajax.Responders.register({
    onCreate: _status_update,
    onComplete: _status_update
});
