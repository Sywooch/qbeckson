<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SettingsSearchFilters */

$this->title = 'Создание фильтра';
$this->params['breadcrumbs'][] = ['label' => 'Все настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-search-filters-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
