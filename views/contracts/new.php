<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use kartik\datecontrol\DateControl;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Записаться';
$this->params['breadcrumbs'][] = ['label' => 'Поиск программ', 'url' => ['/programs/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create  col-md-10 col-md-offset-1">

     <?php $form = ActiveForm::begin(); ?>

    <?php
if (!isset($display)) {
/* echo $form->field($model, 'start_edu_contract')->widget(DateControl::classname(), [
                                    'type'=>DateControl::FORMAT_DATE,
                                    'ajaxConversion'=>false,
                                    'options' => [
                                        'pluginOptions' => [
                                            'autoclose' => true
                                        ]
                                    ]
                                ])->label('Выберите дату начала обучения'); */
    
    $month_start = explode('-',$model->month_start_edu_contract);
    $month = date('m');
    $year = date('Y') + 1;
    $yearlast = date('Y') - 1;
    
    if ($month >= 9) {
        $col = [
             '09-'.date('Y') => 'Сентябрь '.date('Y'),
             '10-'.date('Y') => 'Октябрь  '.date('Y'),
             '11-'.date('Y') => 'Ноябрь '.date('Y'),
             '12-'.date('Y') => 'Декабрь '.date('Y'),
            '01-'.$year => 'Январь '.$year,
            '02-'.$year => 'Февраль '.$year,
             '03-'.$year => 'Март '.$year,
             '04-'.$year => 'Апрель '.$year,
             '05-'.$year => 'Май '.$year,
             '06-'.$year => 'Июнь '.$year,
             '07-'.$year => 'Июль '.$year,
             '08-'.$year => 'Август '.$year,
        ];
    }
    else {
        $col = [
             '09-'.$yearlast => 'Сентябрь '.$yearlast,
             '10-'.$yearlast => 'Октябрь  '.$yearlast,
             '11-'.$yearlast => 'Ноябрь'.$yearlast,
             '12-'.$yearlast => 'Декабрь '.$yearlast,
            '01-'.date('Y') => 'Январь '.date('Y'),
            '02-'.date('Y') => 'Февраль '.date('Y'),
             '03-'.date('Y') => 'Март '.date('Y'),
             '04-'.date('Y') => 'Апрель '.date('Y'),
             '05-'.date('Y') => 'Май '.date('Y'),
             '06-'.date('Y') => 'Июнь '.date('Y'),
             '07-'.date('Y') => 'Июль '.date('Y'),
             '08-'.date('Y') => 'Август '.date('Y'),
        ];
    }
        
    
     echo $form->field($model, 'month_start_edu_contract')->dropDownList($col);
}
 ?>
    <?php                            
    if (!isset($error) && isset($display)) {
     echo DetailView::widget([
        'model' => $display,
        'attributes' => [
            [
                'label' => 'Дата начала обучения',
                'format' => 'date',
                'value' => $model->start_edu_contract,
            ],
            [
                'attribute'=>'balance',
                'label'=> 'У Вас есть доступных средств на сертификате',
            ],
            [
                'attribute'=>'userprice',
                'label'=> 'Полная стоимость программы',
            ],
            [
                'attribute'=>'pay',
                'label'=> 'Стоимость программы, покрываемая Вашим сертификатом',
            ],
            [
                'attribute'=>'dop',
                'label'=> 'Необходимость доплаты',
            ],
            [
                'attribute'=>'ost',
                'label'=> 'После выбора программы на Вашем сертификате останется',
            ],   
           /*  [
                'attribute'=>'cert_dol',
                'label'=> 'Доля сертификата',
            ],
            [
                'attribute'=>'payer_dol',
                'label'=> 'Доля плательщика',
            ],
            [
                'attribute'=>'first_m_price',
                'label'=> 'Первый месяц',
            ],
            [
                'attribute'=>'other_m_price',
                'label'=> 'Остальные месяцы',
            ],   */
        ],
    ]);
      /*  
        if ($model->first_m_price &&  $model->other_m_price && $model->first_m_nprice && $model->other_m_nprice) {
            echo $model->first_m_price.' - '.$model->other_m_price.' - '.$model->first_m_nprice.' - '.$model->other_m_nprice;
        } */
    }
    else {
        if (isset($error)) {
            echo "<h3>".$error."</h3><br>";
        }
    }?>
    
     
    
    <div class="form-group">
     
        <?php
            if(!$model->isNewRecord && !isset($error)) {
                echo Html::a('Отменить', Url::to(['/contracts/cancel', 'id' => $model->id]), ['class' => 'btn btn-danger']);
                echo '&nbsp;';
                 echo Html::a('Продолжить', Url::to(['/contracts/good', 'id' => $model->id]), ['class' => 'btn btn-success']);
            }
        else {
            echo Html::a('Отменить', Url::to(['/programs/view', 'id' => $model->program_id]), ['class' => 'btn btn-danger']);
        }
        ?>
       <?php
if (!isset($display)) {
echo Html::submitButton('Рассчитать', ['class' => 'btn btn-primary']);
}
else {
 echo Html::a('Изменить дату', Url::to(['/contracts/back', 'id' => $model->id]), ['class' => 'btn btn-primary']);
}
 ?>
    
    </div>

    <?php ActiveForm::end(); ?>

</div>
