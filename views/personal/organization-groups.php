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
