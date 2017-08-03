<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */

$this->title = 'Создать группу';
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/personal/organization-groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-create col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'groupClasses' => $groupClasses,
        'model' => $model,
    ]) ?>
</div>
