<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;


$this->title = 'Группы';
   $this->params['breadcrumbs'][] = 'Группы';
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

<?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    if ($roles['organizations'] and $organization['actual'] != 0) {
        echo "<p>";
        echo Html::a('Добавить группу', ['/groups/create'], ['class' => 'btn btn-success']); 
        echo "</p>";
    }
    ?>
<?= GridView::widget([
    'dataProvider' => $GroupsProvider,
    'filterModel' => $searchGroups,
    'summary' => false,
    'columns' => [
       // ['class' => 'yii\grid\SerialColumn'],
                                'name',
                                'program.name',
                                'address',
                                'schedule',
                                
                                [
                                    'attribute' => 'datestart',
                                    'format' => 'date',
                                    'label' => 'Начало',
                                ],
                                [
                                    'attribute' => 'datestop',
                                    'format' => 'date',
                                    'label' => 'Конец',
                                ],
        
                                [
                                    'label' => 'Обучающихся',
                                    'value'=> function ($model) {

                                        $contract = (new \yii\db\Query())
                                            ->select(['id'])
                                            ->from('contracts')
                                            ->where(['status' => 1])
                                            ->andWhere(['group_id' => $model->id])
                                            ->count();

                                        $years = (new \yii\db\Query())
                                            ->select(['maxchild'])
                                            ->from('years')
                                            ->where(['id' => $model->year_id])
                                            ->one();

                                    return $contract;
                                    }
                                ],
        
                            [
                                    'label' => 'Заявок',
                                    'value'=> function ($model) {

                                        $contract = (new \yii\db\Query())
                                            ->select(['id'])
                                            ->from('contracts')
                                            ->where(['status' => [0,3]])
                                            ->andWhere(['group_id' => $model->id])
                                            ->count();

                                        $years = (new \yii\db\Query())
                                            ->select(['maxchild'])
                                            ->from('years')
                                            ->where(['id' => $model->year_id])
                                            ->one();

                                    return $contract;
                                    }
                                ],
                                [
                                    'label' => 'Мест',
                                    'value'=> function ($model) {

                                        $contract = (new \yii\db\Query())
                                            ->select(['id'])
                                            ->from('contracts')
                                            ->where(['status' => [0,1,3]])
                                            ->andWhere(['group_id' => $model->id])
                                            ->count();

                                        $years = (new \yii\db\Query())
                                            ->select(['maxchild'])
                                            ->from('years')
                                            ->where(['id' => $model->year_id])
                                            ->one();

                                    return $years['maxchild'] - $contract;
                                    }
                                ],

        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'groups',
            'template' => '{contracts}',
            'buttons' => [
                'contracts' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/groups/contracts', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Просмотреть договоры'),
                                        //'data-toggle' => 'tooltip',
                                        //'data-placement' => 'top'
                                     ]); },
                
                'completeness' => function ($url, $model) {
                    return Html::a('Полнота оказанных услуг', Url::to(['/completeness/create', 'id' => $model->id]), ['title' => Yii::t('yii', 'Полнота оказанных услуг')]);
                },
            ]
        ],
    ],
]); ?>
