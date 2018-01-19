<?php

use app\models\groups\GroupCreator;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\web\View;

/**
 * @var $this View
 * @var $groupCreator GroupCreator
 */

$this->title = 'Добавить группу';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="groups-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $this->render('_group-create', ['form' => $form, 'groupCreator' => $groupCreator]) ?>

    <div class="form-group">
        <?= Html::a('Назад', Url::to(['programs/view', 'id' => $groupCreator->group->program_id]), ['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
