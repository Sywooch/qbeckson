<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\MunicipalTaskPayerMatrixAssignment;

/* @var $this yii\web\View */
/* @var $model app\models\UserIdentity */

$this->title = 'Настройка муниципальных заданий';
$this->params['breadcrumbs'][] = ['label' => 'Муниципальные задания', 'url' => ['/personal/payer-municipal-task']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-identity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $array_by_type = \app\helpers\ArrayHelper::groupByValue($model->matrix, 'certificate_type');
    foreach ($array_by_type as $i => $models) {
        echo '<div class="well"><h4>' . MunicipalTaskPayerMatrixAssignment::getTypes()[$i] . ' могут использовать:</h4>';
        foreach ($models as $index => $item) {
            $param_choose = 'can_choose_' . MunicipalTaskPayerMatrixAssignment::getPrefixes()[$i];
            $param_set_numbers = 'can_set_numbers_' . MunicipalTaskPayerMatrixAssignment::getPrefixes()[$i];
            echo '<br />';
            if ($item->matrix->$param_choose > 0) {
                echo $form->field($item, "[$index]can_be_chosen")->checkbox()->label($item->matrix->name);
                echo $form->field($item, "[$index]number");
            } elseif ($item->matrix->$param_set_numbers > 0) {
                echo $form->field($item, "[$index]number")->label($item->matrix->name);
                echo $form->field($item, "[$index]number_type")->dropDownList(MunicipalTaskPayerMatrixAssignment::getNumberTypes())->label(false);
            }
        }
        echo '</div>';
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
