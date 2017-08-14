<?php

use app\models\KeyStorageItem;
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
            [
                'attribute' => 'comment',
                'value' => function ($model) {
                    return $model::names()[$model->comment];
                }
            ],
            [
                'attribute' => 'value',
                'value' => function ($model) {
                    /** @var KeyStorageItem $model */
                    if (KeyStorageItem::TYPE_FILE === $model->type) {
                        return Html::a('Скачать файл', $model->getFileUrl());
                    }

                    return $model->value;
                },
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'template'=>'{update} {delete}'
            ],
        ],
    ]); ?>
</div>
