<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;
use app\models\Mun;
use yii\helpers\ArrayHelper;

$this->title = 'Плательщики';
   $this->params['breadcrumbs'][] = 'Плательщики';
/* @var $this yii\web\View */
?>

<?php /* if ($informsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $informsProvider,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                    // 'id',
                    // 'contract_id',
                    // 'from',
                    'date',
                    'text:ntext',
                    'program_id',
                    // 'read',

                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{permit} {view}',
                         'buttons' =>
                             [
                                 'permit' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/informs/read', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отметить как прочитанное'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                                'view' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/programs/view', 'id' => $model->program_id]), [
                                         'title' => Yii::t('yii', 'Просмотреть программу'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } */ ?>


<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие соглашения</a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидается подтверждение <span class="badge"><?= $PayersWaitProvider->getTotalCount() ?></span></a></li>
</ul>
<br>
<?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    if ($roles['organizations'] and $organization['actual'] != 0) {
        echo "<p>";
        echo Html::a('Зарегистрировать новое соглашение', ['payers/index'], ['class' => 'btn btn-success']); 
        echo "</p>";
    }
    ?>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $PayersProvider,
            'filterModel' => $searchPayers,
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
                'controller' => 'payers',
                 'template' => '{view}',
                 'buttons' => [
                        'terminate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/cooperate/decooperate', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Расторгнуть соглашение')
                         ]); },
                    ],
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $PayersWaitProvider,
            'filterModel' => $searchPayersWait,
            'columns' => [
               // ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'user_id',
                'name',
                'mun',
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
                'controller' => 'payers',
                 'template' => '{view}',
                 'buttons' => [
                        'terminate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/cooperate/decooperate', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Расторгнуть соглашение')
                         ]); },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
