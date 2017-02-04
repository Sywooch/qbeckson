<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\Payers;
use app\models\Mun;
use app\models\Cooperate;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PayersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Выбор плательщиков';

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if ($roles['organizations']) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
            'columns' => [
               // ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'user_id',
                'name',
                //'mun',
                 [
                    'attribute'=>'mun',
                    'filter'=>ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
                     'value' => function ($data) { 
                        $mun = (new \yii\db\Query())
                            ->select(['name'])
                            ->from('mun')
                            ->where(['id' => $data->mun])
                            ->one();
                         return $mun['name'];
                     },
                ],
                //'OGRN',
                //'INN',
                // 'KPP',
                // 'OKPO',
                // 'address_legal',
                // 'address_actual',
                 'phone',
                 'email:email',
                // 'position',
                 'fio',
                 'directionality',
                // 'directionality_1_count',
                // 'directionality_2_count',
                // 'directionality_3_count',
                // 'directionality_4_count',
                // 'directionality_5_count',
                // 'directionality_6_count',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
             ],
        ],
    ]); ?>

    <?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    if ($roles['organizations']) {
        echo Html::a('Назад', '/personal/organization-payers', ['class' => 'btn btn-primary']);
    } ?>
</div>
