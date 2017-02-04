<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Coefficient */

$this->title = 'Общие параметры персонифицированного финансирования дополнительного образования детей.';
$this->params['breadcrumbs'][] = Yii::t('app', 'Коэффициенты');
?>
<div class="coefficient-update col-md-offset-1 col-md-10">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
