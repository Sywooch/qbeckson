<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'ДОГОВОР';
$this->params['breadcrumbs'][] = ['label' => 'Contracts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create">

    <h1 class="text-center"><?= Html::encode($this->title) ?></h1>
    <h3 class="text-center">об образовании на обучение по дополнительным оразовательным программам</h3>

     <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'test1')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
