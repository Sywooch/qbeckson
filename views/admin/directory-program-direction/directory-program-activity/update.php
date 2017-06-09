<?php

/* @var $this yii\web\View */
/* @var $model app\models\statics\DirectoryProgramActivity */

$this->title = 'Редактировать вид деятельности: ' . $model->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Направленность: ' . $model->direction->name,
    'url' => ['admin/directory-program-direction/update', 'id' => $model->direction->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-activity-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
