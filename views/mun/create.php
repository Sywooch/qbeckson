<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Mun */

$this->title = Yii::t('app', 'Добавить муниципалитет');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Муниципалитеты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mun-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
