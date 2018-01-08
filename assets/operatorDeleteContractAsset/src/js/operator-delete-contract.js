$(function () {
    var modal = $('#form-modal');
    var confirmButton = $('.confirm-button');
    var modalCertificateNumber = $('#modal-certificate-number', modal);
    var modalContractNumber = $('#modal-contract-number', modal);
    var modalContractDate = $('#modal-contract-date', modal);
    var modalContractId = $('#modal-contract-id', modal);
    var modalAppId = $('#modal-app-id', modal);
    var modalDeleteReason = $('#modal-delete-reason', modal);
    var modalDeleteDocument = $('#modal-delete-document', modal);
    var modalConfirmButton = $('#modal-confirm-button', modal);
    var modalAlertMessage = $('#modal-alert-message', modal);
    var modalCaptchaInput = $('#contractdeleteapplicationform-captcha', modal);

    confirmButton.on('click', function (e) {
        e.preventDefault();
        var data = $(this).data();
        updateData(data);
        modal.modal('show');
    });

    modalDeleteDocument.on('click', function () {
        modalCaptchaInput.prop('disabled', false);
    });

    function updateData(data) {
        var countInvoices = parseInt(data['modalInvoicesCount'], 10);
        modalCaptchaInput.prop('disabled', true);
        if (countInvoices) {
            modalConfirmButton.prop('disabled', true);
            modalAlertMessage.text('Договор нельзя удалить, он включен, как минимум в один из выставленных счетов!');
        } else {
            modalConfirmButton.prop('disabled', false);
            modalAlertMessage.text('');
        }

        modalCertificateNumber.text(data['modalCertificateNumber']);
        modalContractNumber.text(data['modalContractNumber']);
        modalContractDate.text(data['modalContractDate']);
        modalDeleteReason.text(data['modalDeleteReason']);
        modalDeleteDocument.attr('href', data['modalDeleteDocument']);
        modalContractId.val(data['modalContractId']);
        modalAppId.val(data['modalAppId']);
    }

});