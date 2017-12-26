$(function () {
    var checkbox = $('#contractdeleteapplication-ischecked');
    var fileWrapper = $('.field-contractdeleteapplication-confirmationfile');
    var sendModal = $('#send-form-modal');

    fileWrapper.on('fileuploaddone', function() {
        checkbox.prop("disabled", false);
    });

    checkbox.on('change', function () {
        if($(this).is(':checked')) {
            sendModal.modal('show');
        }
    })
});