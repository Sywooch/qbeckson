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

    confirmButton.on('click', function (e) {
        e.preventDefault();
        var data = $(this).data();
        console.log(data);
        updateData(data);
        modal.modal('show');
    });

    function updateData(data) {
        modalCertificateNumber.text(data['modalCertificateNumber']);
        modalContractNumber.text(data['modalContractNumber']);
        modalContractDate.text(data['modalContractDate']);
        modalDeleteReason.text(data['modalDeleteReason']);
        modalDeleteDocument.attr('href', data['modalDeleteDocument']);
        modalContractId.val(data['modalContractId']);
        modalAppId.val(data['modalAppId']);
    }

});