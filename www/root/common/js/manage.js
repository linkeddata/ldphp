var create_name = '';
function check() {
    create_name = $F('create_name');
    var parameters = $('create').serialize(true);
    $('create').disable();
    $('check_true').hide();
    $('check_false').hide();
    new Ajax.Request('/json/'+create_name, {
        parameters: parameters,
        onComplete: function(r) {
            $('check_'+r.responseJSON.available).show();
            $('create_name').enable();
            $('create_check').enable();
            if (r.responseJSON.available) {
                $('create').enable();
            }
            $('create_name').focus();
        }
    });
}
$('create_name').observe('keyup', function(e) {
    if (create_name != $F('create_name')) {
        $('check_true').hide();
        $('check_false').hide();
    }
});
$('create_name').observe('change', check);
$('create_check').observe('click', check);
$('create').reset();
$('create').disable();
$('create_name').enable();
$('create_check').enable();
$('create_name').focus();
