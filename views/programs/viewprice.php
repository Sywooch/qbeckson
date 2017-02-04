<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Нормативная стоимость';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/operator-programs']];

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-view">

<?php
    foreach ($year as $data) {
        echo '<h3>'.$data->year.' Год</h3>';
        
        echo DetailView::widget([
        'model' => $data,
        'attributes' => [
            'normative_price',
        ],
    ]);
    }
    
    
?>  
<?= Html::a('Назад', ['/programs/certificate', 'id' => $id],  ['class' => 'btn btn-danger']) ?>

<?= Html::a('Сертифицировать', Url::to(['/programs/certificateok', 'year' => $year]),  ['data-method' => 'post', 'class' => 'btn btn-primary']) ?>
</div>
