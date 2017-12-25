<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Mun */

$isOperator = Yii::$app->user->can('operators');
$isApplication = $model->type === $model::TYPE_APPLICATION;
$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Муниципалитеты'),
    'url' => $isOperator ? ['index'] : null
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mun-view col-md-offset-1 col-md-10">

    <h1>
        <?php if ($isApplication) { ?>
            <small>Заявка на изменение муниципалитета</small>
            <br>
        <?php } ?>
        <?= Html::encode($this->title) ?>
    </h1>
    <?php if ($isApplication) {
        echo $this->render('_application_table', ['model' => $model]);
    } else {
        echo $this->render('_base_table', ['model' => $model]);
    }

    if ($model->file) {
        $fileTag = Html::tag('span', '', ['class' => 'glyphicon glyphicon-download-alt']);
        $link = Html::a($fileTag . ' Файл-подтвержение', $model->getFileUrl());
        echo Html::tag('h4', $link);
    } ?>
    <br>
    <?php if ($isOperator) { ?>
        <?= Html::a('Назад', Url::to(['/mun/index']), ['class' => 'btn btn-primary']); ?>
    <?php } ?>

    <?php if ($isApplication) { ?>
        <?php if ($isOperator) { ?>
            <?= Html::a('Одобрить', Url::to(['/mun/confirm', 'id' => $model->id]),
                ['class' => 'btn btn-success']); ?>
            <?= Html::a('Отказать', Url::to(['/mun/reject', 'id' => $model->id]),
                ['class' => 'btn btn-danger']); ?>
        <?php } elseif ($model->user_id == Yii::$app->user->id) { ?>
            <?= Html::a('Редактировать', Url::to(['/mun/update', 'id' => $model->mun_id]),
                ['class' => 'btn btn-primary']); ?>
        <?php } ?>
    <?php } else { ?>
        <?= Html::a('Редактировать', Url::to(['/mun/update', 'id' => $model->id]),
            ['class' => 'btn btn-primary']); ?>
        <?php if ($isOperator) { ?>
            <?= Html::a('Удалить', Url::to(['/mun/delete', 'id' => $model->id]), [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот муниципалитет?',
                    'method' => 'post'
                ]
            ]); ?>

        <?php } ?>
    <?php } ?>

</div>
