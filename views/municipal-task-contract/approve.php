<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MunicipalTaskContract */

$this->title = 'Данные для заявления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="municipal-task-contract-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="municipal-task-contract-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'number')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Создать договор', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
