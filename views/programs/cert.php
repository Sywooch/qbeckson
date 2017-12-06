<?php

use kartik\editable\Editable;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */
/* @var $modelsYears \app\models\ProgrammeModule[] */

$this->title = 'Редактировать программу: ' . $model->name;
if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
} elseif (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
}
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>

<div class="programs-form" ng-app>

    <?php
    $data = [1 => 'Высокое обеспечение', 2 => 'Среднее обеспечение', 3 => 'Низкое обеспечение'];
    echo Html::label($model->getAttributeLabel('p3z') . ': ');
    echo Editable::widget([
        'model' => $model,
        'additionalData' => ['id' => $model->id],
        'inputType' => Editable::INPUT_DROPDOWN_LIST,
        'format' => Editable::FORMAT_BUTTON,
        'data' => $data,
        'displayValueConfig' => $data,
        'attribute' => 'p3z',
        'formOptions' => [
            'action' => Url::to(['programs/normpricesave']),
        ],
    ]); ?>

    <?php \yii\widgets\Pjax::begin() ?>
    <div class="container-items"><!-- widgetContainer -->
        <?php foreach ($modelsYears as $i => $modelYears): ?>
            <div class="item panel panel-default"><!-- widgetBody -->
                <div class="panel-heading">
                    <h3 class="panel-title pull-left"><?= $i + 1 ?> модуль</h3>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            $data = [1 => 'Выше среднего', 2 => 'Средняя', 3 => 'Ниже среднего'];
                            echo Html::label($modelYears->getAttributeLabel('p21z') . ': ');
                            echo Editable::widget([
                                'model' => $modelYears,
                                'additionalData' => ['id' => $modelYears->id],
                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                'data' => $data,
                                'options' => ['id' => $modelYears->formName() . '-' . $i . '-p21z',],
                                'displayValueConfig' => $data,
                                'format' => Editable::FORMAT_BUTTON,
                                'attribute' => "p21z",
                                'formOptions' => [
                                    'action' => Url::to(['programs/normpricesave']),
                                ],
                            ]);
                            ?><?php
                            echo Html::label($modelYears->getAttributeLabel('p22z') . ': ');
                            echo Editable::widget([
                                'model' => $modelYears,
                                'additionalData' => ['id' => $modelYears->id],
                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                'options' => ['id' => $modelYears->formName() . '-' . $i . '-p22z',],
                                'data' => $data,
                                'displayValueConfig' => $data,
                                'format' => Editable::FORMAT_BUTTON,
                                'attribute' => "p22z",
                                'formOptions' => [
                                    'action' => Url::to(['programs/normpricesave']),
                                ],
                            ]);
                            ?>
                            <?php
                            echo Html::label($modelYears->getAttributeLabel('normative_price') . ': ');
                            echo Editable::widget([
                                'model' => $modelYears,
                                'additionalData' => ['id' => $modelYears->id],
                                'options' => ['id' => $modelYears->formName() . '-' . $i . '-normative_price',],
                                'attribute' => "normative_price",
                                'format' => Editable::FORMAT_BUTTON,
                                'formOptions' => [
                                    'action' => Url::to(['programs/normpricesave']),
                                ],
                            ]);
                            ?>
                        </div>
                    </div><!-- .row -->
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    echo Html::a('Назад', Url::to(['/programs/verificate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    echo '&nbsp;';
    echo Html::a(
        'Пересчитать нормативную стоимость',
        Url::to(['/programs/certificate', 'id' => $model->id]),
        [
            'class' => 'btn btn-primary',
            'data' => [
                'method' => 'post',
            ],
        ]
    );
    \yii\widgets\Pjax::end();
    echo '&nbsp';
    echo Html::a(
        'Cертифицировать',
        Url::to(['save', 'id' => $model->id]),
        ['class' => 'btn btn-primary', 'data' => ['method' => 'post']]
    );
    ?>

</div>
