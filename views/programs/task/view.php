<?php

/* @var $this yii\web\View */
/* @var $model ProgramViewDecorator */
$this->title = $model->name;

if (Yii::$app->user->can('operators')) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
} elseif (Yii::$app->user->can('organizations')) {
    $this->params['breadcrumbs'][] = ['label' => $model->isMunicipalTask ? 'Муниципальные задания' : 'Программы', 'url' => $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs']];
}
$headTemplate = '_base_head';
if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
    $headTemplate = '_organisation_head';
} elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR)) {
    $headTemplate = '_operator_head';
} elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
    $headTemplate = '_certificate_head';
}
echo $model->getAlert();
$this->params['breadcrumbs'][] = $this->title;

if ($model->isMunicipalTask && $model->verification == app\models\Programs::VERIFICATION_DENIED && !empty($model->refuse_reason)) {
    echo '<div class="alert alert-danger">Муниципальное задание отклонено. Причина: ' . nl2br($model->refuse_reason) . '</div>';
}
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
