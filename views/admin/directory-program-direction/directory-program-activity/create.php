<?php


/* @var $this yii\web\View */
/* @var $model app\models\statics\DirectoryProgramActivity */
/* @var $direction app\models\statics\DirectoryProgramDirection */

$this->title = 'Создать новый вид деятельности';
$this->params['breadcrumbs'][] = [
    'label' => 'Направленность: ' . $direction->name,
    'url' => ['admin/directory-program-direction/update', 'id' => $direction->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-activity-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
