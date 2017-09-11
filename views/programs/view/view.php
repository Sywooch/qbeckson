<?php

/* @var $this yii\web\View */
/* @var $model app\models\Programs */
$this->title = $model->name;

if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
} elseif (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = ['label' => $model->isMunicipalTask ? 'Муниципальные задания' : 'Программы', 'url' => $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs']];
}
$headTemplate = '_base_head';
if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
    $headTemplate = '_organisation_head';
}


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <?= $this->render($headTemplate, ['model' => $model]) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= $this->render('_base_controls', ['model' => $model]); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= $this->render('_base_body', ['model' => $model, 'cooperate' => $cooperate]) ?>
    </div>
</div>
