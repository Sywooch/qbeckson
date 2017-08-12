<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MonitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уполномоченные организации';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-identity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить организацию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>

    <i>В данном разделе Вы можете создать личные кабинеты с ограниченными правами доступа к функционалу Вашего личного кабинета. Обязательно сделайте отдельный личный кабинет для уполномоченной организации (к чему дать доступ - решите самостоятельно).</i>
</div>
