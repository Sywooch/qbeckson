<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\models\Mun;

//use kartik\grid\GridView;

$this->title = 'Плательщики';
   $this->params['breadcrumbs'][] = 'Плательщики';
/* @var $this yii\web\View */
?>

<?php /* if ($InformsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $InformsProvider,
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
<?php }  */
$col = [
//['class' => 'yii\grid\SerialColumn'],
    //'id',
    //'user.username',
    'name',
    //'OGRN',
    //'INN',
    //'KPP',
    //'OKPO',
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
    //'address_legal',
    // 'address_actual',
    'phone',
    'email:email',
     //'position',
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
    ],
];
?>

<p>
   
    <!-- <div class="btn-group">
      <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="glyphicon glyphicon-cog"></span> <span class="caret"></span></button>
        <ul class="dropdown-menu">
        <?php 
        //    $form = ActiveForm::begin();
            
          /*  foreach ($col as $value => $label) {    
                echo '<li>'. Html::checkbox($label, ['data-key' => $value]).' '.$label.'</li>';
                
            } */
            
          //  ActiveForm::end();
        ?>
       </ul>
    </div> -->
   
    <?= Html::a('Добавить плательщика', ['payers/create'], ['class' => 'btn btn-success']) ?>
</p>
 


 <?= GridView::widget([
    'dataProvider' => $PayersProvider,
    'filterModel' => $searchPayers,
    'pjax'=>true,
    'summary' => false,
    'columns' => $col,
]); ?>

<?= ExportMenu::widget([
    'dataProvider' => $PayersProvider,
    'target' => '_self',
    'exportConfig' => [
        ExportMenu::FORMAT_EXCEL => false,
    ],
    'columns' => $col,
]); ?>
