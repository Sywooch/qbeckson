<?php

use app\models\groups\GroupCreator;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $groupCreator GroupCreator
 */

$this->title = 'Создать группу';
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/personal/organization-groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-create col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="groups-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $this->render('_group-create', ['form' => $form, 'groupCreator' => $groupCreator]) ?>

        <div class="form-group">
            <?= Html::a('Назад', ['personal/organization-groups'], ['class' => 'btn btn-danger']) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
