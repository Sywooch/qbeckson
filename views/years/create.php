<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProgrammeModule */

$this->title = Yii::t('app', 'Create ProgrammeModule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ProgrammeModule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="years-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
