<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Informs */
/* @var $form yii\widgets\ActiveForm */
/* @var $informs \app\models\Informs */
$this->title = 'Отказ в сертификации ';

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];
    $this->params['breadcrumbs'][] = ['label' => 'Сертификация - 1 шаг: '.$model->name , 'url' => ['/programs/verificate', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = $this->title;
}

?>


<div class="informs-form col-md-10 col-md-offset-1">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($informs, 'dop')->textarea(['rows' => 4, 'placeholder' => 'Укажите причину, по которой Вы отклоняете заявку']) ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
