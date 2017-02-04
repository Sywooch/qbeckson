<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Certificates;
use app\models\Organization;
use yii\jui\AutoComplete;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Создать договор';
    $this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="programs-view  col-md-10 col-md-offset-1">

   <h1><?= Html::encode($this->title) ?></h1>
     
     <?php 
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    
    if ($organization->max_child > $organization->amount_child) {
     
    ?>

     <?php $form = ActiveForm::begin(); ?>
    
<?= $form->field($model, 'number')->textInput(['maxlength' => true])
?>
  
  <?php /*
$fio_child=Certificates::find()
    ->select(['fio_child as value', 'fio_child as label'])
    ->asArray()
    ->all(); */
    
?>
    
   

<?= $form->field($model, 'soname')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'phname')->textInput(['maxlength' => true]) ?>


   <?php
    if (isset($display)) { echo '<h3>'.$display.'</h3>';}
    ?>
    
    <div class="form-group">
        <?= Html::submitButton('Проверить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php 
    }
       else {
           echo '<h3 class="well">Записано максимальное количество детей</h3>';
       }
    ?>
</div>
