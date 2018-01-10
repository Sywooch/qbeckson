<?php

/**
 * страница загружаемая через ajax для автопролонгации в новую группу
 *
 * @var $this View
 * @var $certificatesDataProvider ActiveDataProvider
 * @var $moduleNameList array
 * @var $group Groups
 */

use app\models\Contracts;
use app\models\Groups;
use kartik\widgets\DepDrop;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$js = <<<js
var countToAutoProlong = 0;

$('#group-id').on('depdrop:change', function(event, id, value, count) {
    changeDisabledStatus(true);

    if (count == 0 && value != '') {
        $('.group-info').html('Для перевода детей в выбранный модуль необходимо создать в нем соответствующую группу.');
    } else {
        $('.group-info').html('');
    }
});
$('#group-id').on('change', function() {
    var url = $(this).data('group-info-url'), group = $(this);
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
            } else {
            }
        }
    });
});
$('.auto-prolong-confirmation-button').on('click', function() {
    var count = 1;
    
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
            
            $('.group-info').html('Количество свободных мест: ' + (countToAutoProlong - checkedCount));

            if (all || (checkedCount < countToAutoProlong)) {
                $(this).prop('checked', true);
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
js;
$this->registerJs($js);

?>

<div class="row">
    <div class="col-xs-6">
        <div class="panel panel-default">
            <?= GridView::widget([
                'dataProvider' => $certificatesDataProvider,
                'summary' => '',
                'columns' => [
                    [
                        'class' => \yii\grid\DataColumn::className(),
                        'header' => 'Номер сертификата',
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'content' => function ($contract) {
                            /** @var $contract Contracts */
                            return $contract->certificate->number;
                        },
                    ],
                    [
                        'class' => \yii\grid\DataColumn::className(),
                        'header' => 'ФИО',
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'content' => function ($contract) {
                            /** @var $contract Contracts */
                            return $contract->certificate->fio_child;
                        },
                    ],
                    [
                        'class' => ActionColumn::className(),
                        'header' => 'Выбрать все<br>' . Html::checkbox('', false, [
                                'id' => 'change-all-auto-prolongation-checkboxes',
                                'title' => 'Выберите группу для перевода',
                                'data' => ['url' => Url::to('change-auto-prolongation-for-contract')],
                                'disabled' => true,
                            ]),
                        'headerOptions' => [
                            'class' => 'text-center'
                        ],
                        'template' => '{checkbox}',
                        'buttons' => [
                            'checkbox' => function ($url, $contract, $key) use ($group) {
                                /** @var $contract Contracts */
                                return Html::checkbox('contract-for-auto-prolong-to-new-group', false, [
                                    'value' => $contract->id,
                                    'class' => $contract->parentExists() ? '' : 'change-auto-prolongation-checkbox',
                                    'title' => 'Выберите группу для перевода',
                                    'disabled' => true
                                ]);
                            }
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <div class="col-xs-6">
        <div class="panel panel-default">
            <label>Модули программы для перевода</label>
            <?= Html::dropDownList('module-id', null, $moduleNameList, [
                'class' => 'form-control',
                'id' => 'module-id',
                'prompt' => '-- Не выбран --',
            ]) ?>
            <br>
            <?php if ([] != $moduleNameList): ?>
                <?= DepDrop::widget([
                    'name' => 'group-id',
                    'options' => [
                        'id' => 'group-id',
                        'data' => [
                            'group-info-url' => '/programs/get-group-info-for-auto-prolongation',
                        ]
                    ],
                    'pluginOptions' => [
                        'depends' => ['module-id'],
                        'placeholder' => '-- Не выбран --',
                        'url' => Url::to(['programs/get-group-list-for-auto-prolongation?groupId=' . $group->id])
                    ]
                ]) ?>
            <?php endif; ?>
            <br>
            <div class="group-info">

            </div>
        </div>
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
            <?= Html::button('да, выполнить перевод', [
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
                $(\'.new-group-auto-prolongation-button[data-group-id="' . $group->id . '"]\').trigger("click");'
            ]) ?>
        </div>
        <div class="col-xs-6 text-right">
            <?= Html::button('отмена', ['class' => 'btn btn-danger', 'onClick' => '$(".modal").modal("hide");']) ?>
        </div>
    </div>
</div>
