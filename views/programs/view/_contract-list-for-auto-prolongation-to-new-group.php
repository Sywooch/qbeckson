<?php

/**
 * часть страницы со списком контрактов для автопролонгации в новую группу
 *
 * @var View $this
 * @var ActiveDataProvider $certificatesDataProvider
 */

use app\models\Contracts;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\View;

?>

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
                    'checkbox' => function ($url, $contract, $key) {
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
