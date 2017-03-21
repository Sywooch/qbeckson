<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Programs */

$this->title = 'Отправить программу на сертификацию';
$this->params['breadcrumbs'][] = ['label' => 'Программы', 'url' => ['/personal/organization-programs']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="programs-create col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'file' => $file,
        'modelsYears' => $modelsYears
    ]) ?>
</div>
