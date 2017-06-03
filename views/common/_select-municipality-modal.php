<?php

use app\models\Mun;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$municipalityItems = [];
foreach (Mun::findAllRecords('id, name') as $key => $record) {
    $municipalityItems[$key]['label'] = $record->name;
    $municipalityItems[$key]['url'] = ['personal/update-municipality', 'munId' => $record->id];
    $municipalityItems[$key]['options'] = ['data-method' => 'post'];
}

/** @var $this \yii\web\View */
/** @var \app\models\UserIdentity $user */
$user = Yii::$app->user->getIdentity();
?>
<?php
Modal::begin([
    'header' => '<h2>Выберите муниципалитет</h2>',
    'toggleButton' => [
        'label' => !empty($user->municipality) ? $user->municipality->name : 'Все муниципалитеты',
        'style' => 'cursor: pointer',
        'class' => 'btn btn-success'
    ],
]);
?>
<div class="row">
    <div class="col-md-6">
        <?= Html::a('Все муниципалитеты', ['personal/update-municipality'], ['data-method' => 'post']) ?>
        <br>
    </div>
    <?php foreach ($municipalityItems as $item) : ?>
        <div class="col-md-6">
            <?= Html::a($item['label'], $item['url'], $item['options']) ?>
            <br>
        </div>
    <?php endforeach; ?>
</div>
<?php Modal::end(); ?>
