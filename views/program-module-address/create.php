<?php

/* @var $this yii\web\View */
/* @var $programModuleModel \app\models\ProgrammeModule */

$this->title = 'Добавить адресы модулю';
$this->params['breadcrumbs'][] = ['label' => 'Программа', 'url' => ['programs/view', 'id' => $programModuleModel->program_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-module-address-create">
    <?= $this->render('_form', [
        'addressModels' => $addressModels,
        'programModuleModel' => $programModuleModel,
    ]) ?>
</div>
