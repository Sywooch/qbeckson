<?php

use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DirectoryProgramDirectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Направленности программ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-direction-index">
    <p><?= Html::a('Создать новую направленность', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],
            'name',
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
