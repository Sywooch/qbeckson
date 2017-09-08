<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Обучение в текущем году</a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие подтверждения <span
                    class="badge"><?= $programnocertProvider->getTotalCount() ?></span></a></li>
</ul>
<br>

<div class="tab-content">
    <p>
        <?= Html::a('Поиск программы', ['programs/index'], ['class' => 'btn btn-success']) ?>
    </p>
    <div id="panel1" class="tab-pane fade in active">

        <?= GridView::widget([
            'dataProvider' => $programcertProvider,
            'filterModel' => $programcertModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'program_id',
                //'organization_id',
                //'verification',
                'organization.name',
                'name',
                'year',
                //'open',
                // 'normative_price',
                //'price',
                //'rating',
                //'limit',
                // 'study',
                // 'open',
                // 'goal:ntext',
                // 'task:ntext',
                // 'annotation:ntext',
                // 'hours',
                // 'ovz',
                // 'quality_control',
                // 'link',
                // 'certification_date',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $programnocertProvider,
            'filterModel' => $programnocertModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'program_id',
                //'organization_id',
                //'verification',
                'organization.name',
                'name',
                'year',
                //'open',
                // 'normative_price',
                //'price',
                //'rating',
                //'limit',
                // 'study',
                // 'open',
                // 'goal:ntext',
                // 'task:ntext',
                // 'annotation:ntext',
                // 'hours',
                // 'ovz',
                // 'quality_control',
                // 'link',
                // 'certification_date',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'controller' => 'programs',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
</div>
