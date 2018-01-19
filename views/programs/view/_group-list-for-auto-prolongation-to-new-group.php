<?php

/**
 * часть страницы для отображения списка групп для перевода при автопролонгации в новую группу
 *
 * @var $this View
 * @var $moduleNameList array
 * @var $group Groups
 */

use app\models\Groups;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

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
    <?= Html::button('Создать группу', [
        'class' => 'btn btn-primary group-create-button',
        'disabled' => true,
        'data' => [
            'url' => Url::to(['/groups/group-create'])
        ],
    ]) ?>
    <br>
    <br>
    <div class="group-info"></div>
</div>
