<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \app\models\programs\ProgramViewDecorator */
/* @var $modelYears \app\models\ProgrammeModule[] */

$this->title = 'Редактировать программу: ' . $model->name;
$this->params['breadcrumbs'][] = [
    'label' => $model->isMunicipalTask ? 'Программы по муниципальному заданию' : 'Программы',
    'url' => $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs']
];
$this->params['breadcrumbs'][] = [
    'label' => $model->name,
    'url' => ['view' . ($model->isMunicipalTask ? '-task' : ''), 'id' => $model->id]
];
$this->params['breadcrumbs'][] = 'Редактировать';
echo $model->getAlert();
?>
<div class="programs-update col-md-10 col-md-offset-1">
    <div class="modal fade modal-auto-popup">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Внимание!</h4>
                </div>
                <div class="modal-body">
                    После редактирования программы, она отправляется на повторную сертификацию.
                </div>
            </div>
        </div>
    </div>
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'file' => $file,
        'modelsYears' => $modelYears
    ]) ?>
</div>
