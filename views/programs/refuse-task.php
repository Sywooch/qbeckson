<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\forms\ProgramSectionForm */

$this->title = 'Отказ';
$this->params['breadcrumbs'][] = ['label' => 'Муниципальные задания', 'url' => ['/personal/organization-municipal-task']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="programs-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'refuse_reason')->textarea() ?>
        <?php
        echo '<div class="form-group">';
        echo Html::submitButton('Отказать', ['class' => 'btn btn-warning']);
        echo '</div>';
        ?>
        <?php ActiveForm::end(); ?>
    </div>
