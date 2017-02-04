<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Informs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="informs-form">

    <?php $form = ActiveForm::begin(); ?>

  <?php
    
    if ($model->cert_dol != 0) {
        $cause = [
            'Просрочка оплаты стоимости платных образовательных услуг со стороны Заказчика' => 'Просрочка оплаты стоимости платных образовательных услуг со стороны Заказчика',
            'Применение к обучающемуся, достигшему возраста 15 лет, отчисления как меры дисциплинарного взыскания' => 'Применение к обучающемуся, достигшему возраста 15 лет, отчисления как меры дисциплинарного взыскания', 
            'Установление нарушения порядка приема в осуществляющую образовательную деятельность организацию, повлекшего по вине обучающегося его незаконное зачисление в эту образовательную организацию' => 'Установление нарушения порядка приема в осуществляющую образовательную деятельность организацию, повлекшего по вине обучающегося его незаконное зачисление в эту образовательную организацию', 
            'Невозможность надлежащего исполнения обязательств по оказанию платных образовательных услуг вследствие действий (бездействия) обучающегося' => 'Невозможность надлежащего исполнения обязательств по оказанию платных образовательных услуг вследствие действий (бездействия) обучающегося',
            'По обстоятельствам, не зависящим от воли организации' => 'По обстоятельствам, не зависящим от воли организации',
            'По иным обстоятельства, предусмотренным законодательством' => 'По иным обстоятельства, предусмотренным законодательством',
    ];
    } else {
        $cause = [
            'Применение к обучающемуся, достигшему возраста 15 лет, отчисления как меры дисциплинарного взыскания' => 'Применение к обучающемуся, достигшему возраста 15 лет, отчисления как меры дисциплинарного взыскания', 
            'Установление нарушения порядка приема в осуществляющую образовательную деятельность организацию, повлекшего по вине обучающегося его незаконное зачисление в эту образовательную организацию' => 'Установление нарушения порядка приема в осуществляющую образовательную деятельность организацию, повлекшего по вине обучающегося его незаконное зачисление в эту образовательную организацию', 
            'Невозможность надлежащего исполнения обязательств по оказанию платных образовательных услуг вследствие действий (бездействия) обучающегося' => 'Невозможность надлежащего исполнения обязательств по оказанию платных образовательных услуг вследствие действий (бездействия) обучающегося',
            'По обстоятельствам, не зависящим от воли организации' => 'По обстоятельствам, не зависящим от воли организации',
            'По иным обстоятельства, предусмотренным законодательством' => 'По иным обстоятельства, предусмотренным законодательством',
    ];
    }
      
        echo $form->field($informs, 'dop')->dropDownList($cause);
 ?>

    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
