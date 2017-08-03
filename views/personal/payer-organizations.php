<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Payers;


$this->title = 'Организации';
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие</a></li>
    <li><a data-toggle="tab" href="#panel2">Ожидающие подтверждения <span
                    class="badge"><?= $Organization0Provider->getTotalCount() ?></span></a></li>
</ul>
<br>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Organization1Provider,
            'filterModel' => $searchOrganization1,
            'pjax' => true,
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
                'max_child',
                [
                    'label' => 'Число обучающихся',
                    'value' => function ($data) {
                        $payers = new Payers();
                        $payer = $payers->getPayer();

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
                //'amount_child',
                [
                    'label' => 'Число договоров',
                    'value' => function ($data) {
                        $payers = new Payers();
                        $payer = $payers->getPayer();

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
                ],
            ],
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Organization0Provider,
            'filterModel' => $searchOrganization0,
            'pjax' => true,
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
                        $payers = new Payers();
                        $payer = $payers->getPayer();

                        $cert = (new \yii\db\Query())
                            ->select(['certificate_id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->andWhere(['payer_id' => $payer])
                            ->all();
                        $cert = array_unique($cert);
                        $cert = count($cert);

                        return $cert;
                    }
                ],

                [
                    'label' => 'Число договоров',
                    'value' => function ($data) {
                        $payers = new Payers();
                        $payer = $payers->getPayer();

                        $cert = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->andWhere(['payer_id' => $payer])
                            ->all();
                        $cert = array_unique($cert);
                        $cert = count($cert);

                        return $cert;
                    }
                ],
                //'amount_child',
                //'inn',
                //'okopo',
                'raiting',
                // 'ground',
                //'user.id',
                //'user.username',
                //'user.password',
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
                ],
            ],
        ]); ?>
    </div>
</div>
