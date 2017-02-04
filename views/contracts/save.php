<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Ввести реквизиты договора';
 $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>
    

     <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'number')->textInput() ?>
    
    <?= $form->field($model, 'date')->widget(DateControl::classname(), [
                                    'type'=>DateControl::FORMAT_DATE,
                                    'ajaxConversion'=>false,
                                    'options' => [
                                        'pluginOptions' => [
                                            'autoclose' => true
                                        ]
                                    ]
                                ]) ?>


    <div class="form-group">
       <?= Html::a('Назад', Url::to(['/contracts/verificate', 'id' => $model->id]), ['class' => 'btn btn-primary']); ?>
       &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
