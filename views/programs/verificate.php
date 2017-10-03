<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if ($roles['operators']) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
}
$this->params['breadcrumbs'][] = 'Сертификация - 1 шаг';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-view col-md-8 col-md-offset-2">

    <h1>1 шаг. Сертификация: <?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'organization.name',
                'label' => 'Организация заявитель',
                'format' => 'raw',
                'value' => Html::a($model->organization->name, Url::to(['/organization/view', 'id' => $model->organization->id]), ['class' => 'blue']),
            ],
            'name',
            'directivity',
            [
                'attribute' => 'activities',
                'value' => function ($model) {
                    /** @var \app\models\Programs $model */
                    if ($model->activities) {
                        return implode(', ', ArrayHelper::getColumn($model->activities, 'name'));
                    }

                    return $model->vid;
                }
            ],
            [
                'attribute' => 'mun',
                'value' => $model->munName($model->mun),
            ],
            //'ground',
            [
                'attribute' => 'ground',
                'value' => $model->ground == 1 ? 'Городская' : 'Сельская',
            ],
            'annotation',
            'task',
            [
                'attribute' => 'link',
                'format' => 'raw',
                'value' => Html::a('<span class="glyphicon glyphicon-download-alt"></span>', '/' . $model->link),
            ],
            'age_group_min',
            'age_group_max',
            'illnessesList',
            'norm_providing',
        ],
    ]) ?>

    <?php
    foreach ($years as $value) {
        echo "<h3>$value->year модуль</h3>";
        echo DetailView::widget([
            'model' => $value,
            'attributes' => [
                'name',
                'month',
                'hours',
                'kvfirst',
                'hoursindivid',
                'hoursdop',
                'kvdop',
                'minchild',
                'maxchild',
                'results:ntext',
            ],
        ]);
    }
    ?>

    <?php
    if ($roles['operators']) {
        echo '<div class="pull-right">';
        echo Html::a('Отказать в сертификации', Url::to(['decertificate', 'id' => $model->id]), ['class' => 'btn btn-danger']);
        echo '</div>';
        echo '&nbsp;';
        echo Html::a('Назад', Url::to(['/personal/operator-programs']), ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Продолжить сертификацию', Url::to(['certificate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
    }
    ?>
</div>
