<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;
use app\models\Certificates;

/* @var $this yii\web\View */
/* @var $OrganizationProvider \yii\data\ActiveDataProvider */
$this->title = 'Организации';
$this->params['breadcrumbs'][] = 'Организации';
?>
<?php if (Yii::$app->user->can('certificate')) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= $this->render('../common/_select-municipality-modal') ?>
            </div>
        </div>
    </div>
    <br>
<?php endif; ?>

<?= GridView::widget([
    'dataProvider' => $OrganizationProvider,
    'filterModel' => $searchOrganization,
    'pjax' => true,
    'rowOptions' => function ($model, $index, $widget, $grid) {
        if ($model) {
            $certificates = new Certificates();
            $certificate = $certificates->getCertificates();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('cooperate')
                ->where(['payer_id' => $certificate['payer_id']])
                ->andWhere(['organization_id' => $model['id']])
                ->andWhere(['status' => 1])
                ->count();

            if ($rows == 0) {
                return ['class' => 'danger'];
            }
        }
    },
    'summary' => false,
    'columns' => [
        'name',
        ['attribute' => 'type',
            'value' => function ($data) {
                if ($data->type == 1) {
                    return 'Образовательная организация';
                }
                if ($data->type == 2) {
                    return 'Организация, осуществляющая обучение';
                }
                if ($data->type == 3) {
                    return 'Индивидуальный предприниматель (с наймом)';
                }
                if ($data->type == 4) {
                    return 'Индивидуальный предприниматель (без найма)';
                }
            }
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
        [
            'label' => 'Число обучающихся',
            'value' => function ($data) {
                $cert = (new \yii\db\Query())
                    ->select(['certificate_id'])
                    ->from('contracts')
                    ->where(['organization_id' => $data->id])
                    ->andWhere(['status' => 1])
                    ->all();
                $cert = array_unique($cert);
                $cert = count($cert);

                return $cert;
            }
        ],
        'max_child',
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
            'label' => 'Соглашение',
            'value' => function ($data) {
                $certificates = new Certificates();
                $certificate = $certificates->getCertificates();

                $rows = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('cooperate')
                    ->where(['payer_id' => $certificate['payer_id']])
                    ->andWhere(['organization_id' => $data['id']])
                    ->andWhere(['status' => 1])
                    ->count();

                if ($rows == 0) {
                    return 'Нет';
                } else {
                    return 'Да';
                }
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'organization',
            'template' => '{view}',
        ],
    ],
]); ?>

