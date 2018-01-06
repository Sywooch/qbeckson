<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = 'Направить запрос на удаление договора';
$this->params['breadcrumbs'][] = ['label' => 'Список запросов на удаление', 'url' => ['contract']];
$this->params['breadcrumbs'][] = $this->title;
\app\assets\organizationDeleteContractAsset\OrganizationDeleteContractAsset::register($this);
?>
<div class="contract-delete-application-create  col-md-10 col-md-offset-1">
    <div class="organization-form">
        <?php $form = ActiveForm::begin(); ?>

        <p>
            <label>
                Договор №<?= ArrayHelper::getValue($model, ['contract', 'number']) ?> от
                <?= \Yii::$app->formatter->asDate(ArrayHelper::getValue($model, ['contract', 'date'])) ?>
            </label>
        </p>

        <?= $form->field($model, 'reason')->textarea() ?>

        <?= $form->field($model, 'confirmationFile')->widget(\trntv\filekit\widget\Upload::className(), [
            'url' => ['file-storage/contract-delete-application'],
            'maxFileSize' => 3 * 1024 * 1024,
            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(pdf|jpg|jpeg|gif|png)$/i'),
        ]); ?>

        <?= $form->field($model, 'isChecked')
            ->checkbox([
                'disabled' => true,
            ]) ?>

        <div class="modal fade" id="send-form-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Направить запрос на удаление</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        <?= Html::submitButton('Направить', ['class' => 'btn btn-primary']); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
