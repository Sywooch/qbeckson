<?php

/**
 * часть страницы с модульным окном для автопролонгации в новую группу
 *
 * @var $this View
 * @var $group Groups
 * @var $certificatesDataProvider ActiveDataProvider
 * @var $moduleNameList array
 */

use app\models\Groups;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

$js = <<<js
var countToAutoProlong = 0;
setHooks();
$('#group-id').on('depdrop:change', function(event, id, value, count) {
    changeDisabledStatus(true);
    
    countToAutoProlong = 0;
    updateContractList();

    if (value != '') {
        $('.group-create-button').prop('disabled', false);
    } else {
        $('.group-create-button').prop('disabled', true);
    }
    
    if (count == 0 && value != '') {
        $('.group-info').html('Для перевода детей в выбранный модуль необходимо создать в нем соответствующую группу.');
    } else {
        $('.group-info').html('');
    }
});
$('#group-id').on('change', function() {
    var url = $(this).data('group-info-url'), group = $(this);
    
    countToAutoProlong = 0;
    changeDisabledStatus(true);

    $.ajax({
        url: url,
        method: 'POST',
        data: {groupId: $(this).val()},
        success: function(data) {
            if (data != null) {
                countToAutoProlong = data.countToAutoProlong;
                
                $('.auto-prolongation-info').html('Вы планируете перевести в группу ' + data.group.name + ' модуля ' + data.group.moduleFullName + ' образовательной программы "' + data.group.programName + '" с заключением договоров ' +
                 'с ' + data.group.dateStart + ' по ' + data.group.dateStop + ' обучающихся:');
            }

            if (group.val() == '') {
                $('.group-info').html('');

                return;
            }
            
            $('.group-info').html('Количество свободных мест: ' + countToAutoProlong);
            if (countToAutoProlong > 0) {
                changeDisabledStatus(false);
            }
        }
    });
});
$('.auto-prolong-confirmation-button').on('click', function() {
    var count = 1;

    $('.certificate-list-table-content').html('');

    $('.change-auto-prolongation-checkbox').each(function() {
        if ($(this).prop('checked') == true) {
            $('.certificate-list-table-content').append('' +
             '<tr>' +
              '<td>' + count++ + '</td>' +
              '<td>' + $('tr[data-key="' + $(this).val() + '"]').find('td:nth-child(2)').html() + '</td>' +
              '<td>' + $('tr[data-key="' + $(this).val() + '"]').find('td:nth-child(1)').html() + '</td>' +
             '</tr>');
        }
    });
    
    $("#auto-prolong-confirmation-block").html($('.auto-prolong-confirmation-content').html());
    $('.auto-prolong-confirmation-content').remove();
    
    $("#auto-prolong-confirmation-modal").modal();
});
$('.group-create-button').on('click', function() {
    var url = $(this).data('url') + '?id=' + $('#module-id').val();

    $('#group-create-modal').modal();

    $.ajax({
        url: url,
        success: function(data) {
            $('.group-create-block').html(data.page);
        }
    });
});
$('.group-save-button').on('click', function() {
    var oldGroups = [],
        groupSaveButton = $(this), 
        form = $('#group-create-form'),
        url = $('.group-create-button').data('url') + '?id=' + $('#module-id').val();

    groupSaveButton.prop('disabled', true);
    setTimeout(function() {
        groupSaveButton.prop('disabled', false);   
    }, 500);
    
    $('#group-id option').each(function() {
        oldGroups.push($(this).val());
    });
    
    $.ajax({
        url: url,
        method: 'POST',
        data: form.serialize(),
        success: function(data) {
            var newGroups = [];

            $('.group-create-block').html(data.page);

            if (data.groupCreated === true) {
                $('#module-id').trigger('change');
                $('.group-create-message').hide();

                setTimeout(function() {
                    $('#group-id option').each(function() {
                        newGroups.push($(this).val());
                    });

                    newGroups.filter(function(groupId) {
                        if ($.inArray(groupId, oldGroups) == -1) {
                            $('#group-id').val(groupId);
                        }
                    });
                }, 500);

                $(groupSaveButton).parents('.modal').first().modal('hide');
            }

            if (data.groupCreated === false) {
                $('.group-create-message').show();
                $('.group-create-message').html(data.message)
            }
        }
    });
});
function setHooks() {
    $('#change-all-auto-prolongation-checkboxes').on('click', function() {
        if ($(this).prop('checked') == true) {
            changeCheckedStatus(true);
            $('.auto-prolong-confirmation-button').prop('disabled', false);
        } else {
            changeCheckedStatus(false);
            $('.auto-prolong-confirmation-button').prop('disabled', true);
        }
    });
    $('.change-auto-prolongation-checkbox').on('change', function() {
        var checkedCount = $('.change-auto-prolongation-checkbox:checkbox:checked').length;

        $('.group-info').html('Количество свободных мест: ' + (countToAutoProlong - checkedCount));

        if (checkedCount == 0) {
            $('.auto-prolong-confirmation-button').prop('disabled', true);
        } else {
            $('.auto-prolong-confirmation-button').prop('disabled', false);
        }

        if (checkedCount >= countToAutoProlong) {
            changeUnchecked(true);
        } else {
            changeUnchecked(false);
        }
    });
}
function changeUnchecked(disabled) {
    $('#change-all-auto-prolongation-checkboxes').prop('checked', disabled);
    $('.change-auto-prolongation-checkbox').each(function() {
        if ($(this).prop('checked') != true) {
            $(this).prop('disabled', disabled);
        }
    });
}
function changeCheckedStatus(checked, all) {
    var checkbox = $('.change-auto-prolongation-checkbox');

    if (all) {
        $('#change-all-auto-prolongation-checkboxes').prop('checked', checked);
    }
    
    if (!checked) {
        checkbox.prop('disabled', false);
        checkbox.prop('checked', false);

        $('.group-info').html('Количество свободных мест: ' + countToAutoProlong);

        $('.auto-prolong-confirmation-button').prop('disabled', true);
    } else {
        checkbox.each(function() {
            var checkedCount = $('.change-auto-prolongation-checkbox:checkbox:checked').length;

            if (all || (checkedCount < countToAutoProlong)) {
                $(this).prop('checked', true);
                $('.group-info').html('Количество свободных мест: ' + (countToAutoProlong - checkedCount - 1));
            } else {
                if ($(this).prop('checked') == false) {
                    $(this).prop('disabled', true);
                }
            }
        })
    }
}
function changeDisabledStatus(disabled) {
    var checkboxForAllCheckboxes = $('#change-all-auto-prolongation-checkboxes');

    if (disabled) {
        changeCheckedStatus(false, true);
    }
    
    checkboxForAllCheckboxes.prop('disabled', disabled);

    $('.change-auto-prolongation-checkbox').prop('disabled', disabled);
}
function updateContractList() {
    var fromGroupId = '{$group->id}';

    $.ajax({
        url: '/programs/contract-list-for-auto-prolongation-to-new-group',
        data: {autoProlongFromGroupId: fromGroupId, autoProlongToYearId: $('#module-id').val()},
        method: 'POST',
        success: function(data) {
            $('.contract-list-for-auto-prolongation-to-new-group-block').html(data);
            setHooks();
            $('#group-id').trigger('change');
        }
    });
}
js;
$this->registerJs($js);

?>

<div class="row">
    <div class="col-xs-6 contract-list-for-auto-prolongation-to-new-group-block">
        <?= $this->render('_contract-list-for-auto-prolongation-to-new-group', [
            'certificatesDataProvider' => $certificatesDataProvider
        ]) ?>
    </div>
    <div class="col-xs-6 group-list-for-auto-prolongation-to-new-group-block">
        <?= $this->renderAjax('_group-list-for-auto-prolongation-to-new-group', [
            'moduleNameList' => $moduleNameList,
            'group' => $group,
        ]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <?= Html::button('продолжить', ['disabled' => 'true', 'title' => 'Укажите хотя бы один сертификат для продолжения', 'class' => 'btn btn-success auto-prolong-confirmation-button']) ?>
    </div>
    <div class="col-xs-6 text-right">
        <?= Html::button('отмена', ['class' => 'btn btn-danger', 'onClick' => '$(".modal").modal("hide");']) ?>
    </div>
</div>

<div class="auto-prolong-confirmation-content" style="display: none;">
    <p class="auto-prolongation-info"></p>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th class="text-center">№</th>
            <th class="text-center">ФИО</th>
            <th class="text-center">Номер сертификата</th>
        </tr>
        </thead>
        <tbody class="certificate-list-table-content">
        </tbody>
    </table>
    <p>Для выбранных сертификатов будут созданы новые договоры (вступление в силу которых предстоит ещё подтвердить). Действительно перевести детей на следующий модуль?</p>

    <div class="row">
        <div class="col-xs-6">
            <?= Html::button('Да, выполнить перевод', [
                'class' => 'btn btn-success',
                'onClick' => '$.ajax({
                    url: \'/programs/auto-prolongation-to-new-group-init\',
                    method: \'POST\',
                    data: {
                        fromGroupId: ' . $group->id . ',
                        toGroupId: $(\'#group-id\').val(),
                        contractIdList: $(\'.change-auto-prolongation-checkbox\').serializeArray()
                    }
                });
                $("#auto-prolong-confirmation-modal").modal("hide");
                updateContractList();'
            ]) ?>
        </div>
        <div class="col-xs-6 text-right">
            <?= Html::button('Отмена', ['class' => 'btn btn-danger', 'onClick' => '$(".modal").modal("hide");']) ?>
        </div>
    </div>
</div>

