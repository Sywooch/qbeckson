<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MunSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Муниципалитеты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mun-index">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
   
    <p>
        <?= Html::a(Yii::t('app', 'Добавить муниципалитет'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,
        'summary' => false,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute' => 'name',
                'label' => 'Наименование муниципального района (городского округа)',
            ],
            //'ground',
            //'nopc',
            //'pc',
            // 'zp',
            // 'dop',
            // 'uvel',
            // 'otch',
            // 'otpusk',
            // 'polezn',
            // 'stav',
            [
                'attribute' => 'deystv',
                'label' => 'Действующих сертификатов',
            ],
            [
                'attribute' => 'countdet',
                'label' => 'Детей, от 5-ти до 18-ти',
            ],
            [
                'attribute' => 'lastdeystv',
                'label' => 'Предыдущий год',
            ],
            

            ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}'],
        ],
    ]); ?>
</div>
