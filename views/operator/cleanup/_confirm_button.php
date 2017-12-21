<?php

use app\models\ContractDeleteApplication;
use app\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model ContractDeleteApplication */
?>
<a class="btn btn-warning confirm-button" href="#" title="Подтвердить удаление договора"
   data-modal-app-id="<?= $model->id ?>"
   data-modal-contract-id="<?= $model->contract_id ?>"
   data-modal-contract-number="<?= ArrayHelper::getValue($model, ['contract', 'number']) ?>"
   data-modal-contract-date="<?= \Yii::$app->formatter->asDate(ArrayHelper::getValue($model, ['contract', 'date'])) ?>"
   data-modal-certificate-number="<?= ArrayHelper::getValue($model, ['contract', 'certificate', 'number']) ?>"
   data-modal-delete-reason="<?= Html::encode($model->reason) ?>"
   data-modal-delete-document="<?= $model->getFileUrl() ?>"
>
    <i class="glyphicon glyphicon-trash"></i>
</a>
