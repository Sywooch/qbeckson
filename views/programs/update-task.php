<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\forms\ProgramSectionForm */

$this->title = 'Редактирование';
$this->params['breadcrumbs'][] = ['label' => 'Программы по муниципальному заданию', 'url' => ['/personal/organization-municipal-task']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="programs-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'section')->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\MunicipalTaskMatrix::find()->all(), 'id', 'name')) ?>
        <?php
        echo '<div class="form-group">';
        echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
        echo '</div>';
        ?>
        <?php ActiveForm::end(); ?>
    </div>
