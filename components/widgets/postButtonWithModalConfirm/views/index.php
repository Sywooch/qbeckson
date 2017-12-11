<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $toggleButton array */
/* @var $confirm string */
/* @var $url string */


?>
<?php Modal::begin([
    'header'       => '<h4>' . Html::encode($title) . '</h4>',
    'toggleButton' => $toggleButton,
]); ?>
<?php Alert::begin([
    'options' => [
        'class' => 'alert-danger',
    ],
]);
echo $confirm;
Alert::end(); ?>

<?php if ($askPassword): ?>
<?php $form = ActiveForm::begin(['action' => $url]); ?>

<?= $form->field($model, 'confirm')->passwordInput() ?>

<?= Html::submitButton('Подтвердить', ['class' => 'btn btn-danger']) ?>

<?php ActiveForm::end(); ?>
<?php else: ?>
    <?= Html::a('Продолжить', $url, ['class' => 'btn btn-success']) ?>
    <?= Html::a('Отмена', 'javascript:void(0);', ['class' => 'btn btn-theme', 'data-dismiss' => 'modal']) ?>
<?php endif; ?>

<?php Modal::end(); ?>