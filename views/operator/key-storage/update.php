<?php

/* @var $this yii\web\View */
/* @var $model app\models\KeyStorageItem */

$this->title = 'Обновить параметр системы: ' . ' ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => 'Параметры системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="key-storage-item-update">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
