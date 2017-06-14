<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
$this->title = 'Поставщики образовательных услуг';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel-registry">Реестр <span
                    class="badge"><?= $registryProvider->totalCount ?></span></a></li>
    <li><a data-toggle="tab" href="#panel-requests">Заявки <span
                    class="badge"><?= $requestProvider->totalCount ?></span></a></li>
</ul>
<br/>

<div class="tab-content">
    <div id="panel-registry" class="tab-pane fade in active">
        <?= Html::a('Добавить поставщика образовательных услуг', ['organization/create'], ['class' => 'btn btn-success']) ?>
        <div class="pull-right">
            <?= Html::a('Пересчитать лимиты', ['organization/alllimit'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Пересчитать рейтинги', ['organization/allraiting'], ['class' => 'btn btn-primary']) ?>
        </div>
        <br/><br/>
        <?= GridView::widget([
            'dataProvider' => $registryProvider,
            'filterModel' => $searchRegistry,
            'pjax' => true,
            'summary' => false,
            'columns' => [
                'name',
                'typeLabel',
                [
                    'label' => 'Число программ',
                    'attribute' => 'certprogram',
                ],
                'max_child',
                [
                    'label' => 'Число обучающихся',
                    'value' => function ($data) {
                        $cert = (new \yii\db\Query())
                            ->select(['certificate_id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->column();
                        $cert = array_unique($cert);
                        $cert = count($cert);

                        return $cert;
                    }
                ],
                'amount_child',
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

        <?= ExportMenu::widget([
            'dataProvider' => $registryProvider,
            'target' => '_self',
            'exportConfig' => [
                ExportMenu::FORMAT_EXCEL => false,
            ],
            'columns' => [
                'name',
                'typeLabel',
                'certprogram',
                'max_child',
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
                'amount_child',
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
            ],
        ]); ?>
    </div>
    <div id="panel-requests" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $requestProvider,
            'filterModel' => $searchRequest,
            'pjax' => true,
            'summary' => false,
            'columns' => [
                'name',
                'typeLabel',
                'max_child',
                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'organization',
                    'template' => '{update}',
                ],
            ],
        ]); ?>
    </div>
</div>