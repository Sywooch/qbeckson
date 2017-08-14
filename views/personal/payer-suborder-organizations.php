<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;


$this->title = 'Подведомственные организации';
   $this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<h1><?= $this->title ?></h1>

<?= Html::a('Добавить организацию', ['payer-all-organizations'], ['class' => 'btn btn-success']) ?><br /><br />

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => false,
    'columns' => [
        'name',
        [
            'attribute' => 'type',
            'value' => function ($model) {
                /** @var \app\models\Organization $model */
                return $model::types()[$model->type];
            },
        ],
        [
            'label' => 'Число программ',
            'value' => function ($data) {
                $programs = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('programs')
                    ->where(['organization_id' => $data->id])
                    ->andWhere(['verification' => 2])
                    ->count();

                return $programs;
            }
        ],
        'max_child',
        [
            'label' => 'Число обучающихся',
            'value' => function ($data) {
                $payer = Yii::$app->user->identity->payer;

                $cert = (new \yii\db\Query())
                    ->select(['certificate_id'])
                    ->from('contracts')
                    ->where(['organization_id' => $data->id])
                    ->andWhere(['status' => 1])
                    ->andWhere(['payer_id' => $payer])
                    ->column();

                $cert = array_unique($cert);
                $cert = count($cert);

                return $cert;
            }
        ],
        [
            'label' => 'Число договоров',
            'value' => function ($data) {
                $payer = Yii::$app->user->identity->payer;

                $cert = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['organization_id' => $data->id])
                    ->andWhere(['status' => 1])
                    ->andWhere(['payer_id' => $payer])
                    ->column();
                $cert = array_unique($cert);
                $cert = count($cert);

                return $cert;
            }
        ],
        'raiting',
        ['attribute' => 'actual',
            'value' => function ($data) {
                if ($data->actual == 0) {
                    return '-';
                } else {
                    return '+';
                }
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'certificate_accounting_limit',
            'pageSummary' => false,
            'editableOptions' => [
                'asPopover' => false,
                'submitButton' => [
                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                    'class' => 'btn btn-sm btn-success',
                ],
            ],
        ],
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'organization',
            'template' => '{view}',
        ],
    ],
]); ?>

