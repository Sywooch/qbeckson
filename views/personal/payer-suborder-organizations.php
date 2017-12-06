<?php

use app\models\Organization;
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
        [
            'attribute' => 'name',
            'label' => 'Наименование',
        ],
        [
            'label' => 'Число программ по МЗ',
            'value' => function ($data) {
                $programs = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('programs')
                    ->where(['organization_id' => $data->id])
                    ->andWhere([
                        'verification' => 2,
                        'is_municipal_task' => 1,
                    ])
                    ->count();

                return $programs;
            }
        ],
        [
            'label' => 'Кол-во услуг',
            'value' => function ($data) {
                $payer = Yii::$app->user->identity->payer;

                $organization = Organization::findOne($data->id);

                return $cert = $organization ? $organization->getChildrenCountMunicipalTask($payer) : null;
            }
        ],
        'raiting',
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
            'template' => '{view-subordered}',
            'buttons' => [
                'view-subordered' => function ($url, $model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        ['organization/view-subordered', 'id' => $model->id]
                    );
                },
            ],
        ],
    ],
]); ?>

