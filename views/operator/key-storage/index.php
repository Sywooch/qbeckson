<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\KeyStorageItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Параметры системы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="key-storage-item-index">
    <p>
        <?php echo Html::a('Добавить новый параметр системы', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'grid-view table-responsive'
        ],
        'columns' => [
            'comment',
            'key',
            'value',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return $model::types()[$model->type];
                }
            ],
            [
                'class' => ActionColumn::class,
                'template'=>'{update} {delete}'
            ],
        ],
    ]); ?>
</div>
