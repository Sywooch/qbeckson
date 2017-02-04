<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Сертифицировать программу';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
        
        <?= $form->field($model, 'p3z')->dropDownList([1 => 'Высокое обеспечение', 2 => 'Среднее обеспечение', 3 => 'Низкое обеспечение']) ?> 
        
         <?php 
            $i = 0;
            foreach ($year as $value) {
                $y = 1 + $i;
                echo "
                <div class='panel panel-default'>
                    <div class='panel-heading'>$y Год</div>
                    <div class='panel-body'>            
                ";
                
                echo $form->field($year[$i], 'p21z')->dropDownList([1 => 'Высшая', 2 => 'Первая', 3 => 'Иная']);
                echo $form->field($year[$i], 'p22z')->dropDownList([1 => 'Высшая', 2 => 'Первая', 3 => 'Иная']);
               
                echo DetailView::widget([
                    'model' => $year[$i],
                    'attributes' => [
                        'normative_price',
                    ],
                ]);
                echo "
                    </div>
                </div>
                ";
                $i++;
        }
    
            echo Html::a('Назад', Url::to(['/programs/verificate', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::submitButton('Пересчитать нормативную стоимость', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            echo '&nbsp';
            echo Html::a('Cертифицировать', Url::to(['save', 'id' => $model->id]), ['class' => 'btn btn-primary']);
        
        ?>

    <?php ActiveForm::end(); ?>
</div>
