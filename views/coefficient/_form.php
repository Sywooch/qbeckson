<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Coefficient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coefficient-form">

    <?php $form = ActiveForm::begin(); ?>

     <div class="panel panel-default">
        <div class="panel-heading">Расчет нормативной стоимости</div>
        <div class="panel-body">
           <div class="panel panel-default">
                <div class="panel-heading">Коэффициент учета квалификации персонала</div>
                <div class="panel-body">
                    <div class="panel panel-default">
                        <div class="panel-heading">Квалификация педагогического работника непосредственно осуществляющего реализацию образовательной программы в группе детей</div>
                        <div class="panel-body">
                            <?= $form->field($model, 'p21v')->textInput() ?>

                            <?= $form->field($model, 'p21s')->textInput() ?>

                            <?= $form->field($model, 'p21o')->textInput() ?>
                        </div>
                    </div>

                   <div class="panel panel-default">
                        <div class="panel-heading">Квалификация педагогического работника, дополнительно привлекаемого для совместной реализации образовательной программы в группе</div>
                        <div class="panel-body">
                            <?= $form->field($model, 'p22v')->textInput() ?>

                            <?= $form->field($model, 'p22s')->textInput() ?>

                            <?= $form->field($model, 'p22o')->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>

           <div class="panel panel-default">
                <div class="panel-heading">Коэффициент учета степени обеспечения оборудованием</div>
                <div class="panel-body">
                    <?= $form->field($model, 'p3v')->textInput() ?>

                    <?= $form->field($model, 'p3s')->textInput() ?>

                    <?= $form->field($model, 'p3n')->textInput() ?>
                </div>
            </div>

            <?= $form->field($model, 'weekyear')->textInput() ?>

            <?= $form->field($model, 'weekmonth')->textInput() ?>

            <?= $form->field($model, 'pk')->textInput() ?>

            <?= $form->field($model, 'norm')->textInput() ?>
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">Параметры для оценки лимита организации</div>
        <div class="panel-body">
            <?= $form->field($model, 'potenc')->textInput() ?>

            <?= $form->field($model, 'ngr', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'sgr', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'vgr', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'chr1', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'zmr1', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'chr2', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

            <?= $form->field($model, 'zmr2', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>
        </div>
    </div>

  
  <div class="panel panel-default">
        <div class="panel-heading">Параметры для оценки лимита программы</div>
        <div class="panel-body">
           <div class="panel panel-default">
                <div class="panel-heading">Базовый лимит зачисления на обучение по образовательной программе</div>
                <div class="panel-body">
                    <?= $form->field($model, 'blimrob')->textInput() ?>

                    <?= $form->field($model, 'blimtex')->textInput() ?>

                    <?= $form->field($model, 'blimest')->textInput() ?>

                    <?= $form->field($model, 'blimfiz')->textInput() ?>

                    <?= $form->field($model, 'blimxud')->textInput() ?>

                    <?= $form->field($model, 'blimtur')->textInput() ?>

                    <?= $form->field($model, 'blimsoc')->textInput() ?>
              </div>
            </div>

        <?= $form->field($model, 'ngrp', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'sgrp', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'vgrp', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'ppchr1', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'ppzm1', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'ppchr2', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>

        <?= $form->field($model, 'ppzm2', ['addon' => ['append' => ['content'=>'%']]])->textInput() ?>
     </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">Параметры для оценки рейтинга программы</div>
        <div class="panel-body">
           <?= $form->field($model, 'minraiting')->textInput() ?>
           
            <?= $form->field($model, 'ocsootv')->textInput() ?>

            <?= $form->field($model, 'ocku')->textInput() ?>

            <?= $form->field($model, 'ocmt')->textInput() ?>

            <?= $form->field($model, 'obsh')->textInput() ?>

            <?= $form->field($model, 'ktob')->textInput() ?>

            <?= $form->field($model, 'vgs')->textInput() ?>

            <?= $form->field($model, 'sgs')->textInput() ?>

            <?= $form->field($model, 'pchsrd')->textInput() ?>

            <?= $form->field($model, 'pzmsrd')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('Отменить', '/coefficient/update',['class' => 'btn btn-danger']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
