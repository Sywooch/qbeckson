<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = $model->isMunicipalTask ? 'Создание муниципального задания' : 'Отправить программу на сертификацию';
$this->params['breadcrumbs'][] = ['label' => $model->isMunicipalTask ? 'Муниципальные задания' : 'Программы', 'url' => $model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'file' => $file,
        'modelsYears' => $modelsYears,
    ]) ?>
</div>
