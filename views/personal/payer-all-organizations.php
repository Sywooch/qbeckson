<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;


$this->title = 'Выберите организацию';
   $this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<h1><?= $this->title ?></h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => false,
    'columns' => [
        'name',
        'typeLabel',
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
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'organization',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/organization/view-subordered', 'id' => $model->id]);
                },
            ],
        ],
    ],
]); ?>

