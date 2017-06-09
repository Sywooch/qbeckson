<?php

/* @var $this yii\web\View */
/* @var $model app\models\statics\DirectoryProgramDirection */

$this->title = 'Создать новую направленность';
$this->params['breadcrumbs'][] = ['label' => 'Направленности программ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-direction-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
