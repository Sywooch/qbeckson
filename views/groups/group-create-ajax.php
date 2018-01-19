<?php

use app\models\groups\GroupCreator;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $groupCreator GroupCreator
 */

?>

<div class="groups-form">
    <?php $form = ActiveForm::begin(['id' => 'group-create-form']); ?>
    <?= $this->render('_group-create', ['form' => $form, 'groupCreator' => $groupCreator]) ?>
    <?php $form::end(); ?>
</div>
