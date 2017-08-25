<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="clearfix">
            <?php
            if ($model->raiting) {
                echo '<h3 class="pull-right">' . $model->raiting . '%</h3>';
            } else {
                echo '<h3 class="pull-right">Рейтинга нет</h3>';
            }
            ?>
        </div>
        <div class="well">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'full_name',
                    [
                        'attribute'=>'type',
                        'value' => $model::types()[$model->type],
                    ],
                    'address_actual',
                    [
                        'attribute' => 'mun',
                        'label' => 'Основной район (округ)',
                        'value' => $model->municipality->name,
                        'format' => 'raw',
                    ],
                    'phone',
                    [
                        'attribute' => 'email',
                        'format' => 'email',
                    ],
                    [
                        'attribute' => 'site',
                        'format' => 'url',
                    ],
                    'fio_contact',
                    [
                        'label' => 'Лицензия',
                        'value' => 'Лицензия от ' .
                            date('d.m.Y', strtotime($model->license_date)) .
                            ' №' . $model->license_number . ' выдана ' . $model->license_issued . '.',
                    ],
                    [
                        'attribute'=>'actual',
                        'value' => $model->actual === 1 ?
                            'Осуществляет деятельность в рамках системы' : 'Деятельность в рамках системы приостановлена',
                    ],
                ],
            ])
            ?>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'max_child',
                        [
                            'label' => 'Число обучающихся',
                            'value' =>  $model->getContracts()
                                ->select(['certificate_id'])
                                ->andWhere(['status' => [0, 1, 2, 3]])
                                ->distinct()
                                ->count(),
                        ],
                        [
                            'label' => 'Сертифицированных программ',
                            'value' => $model->getCertprogram(),
                        ],
                    ],
                ]); ?>
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'about:ntext',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
