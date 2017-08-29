<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */

$this->title = $model->name;
?>
<div class="site-manual row">
<div class="col-md-10 col-md-offset-1">
  <h1><?= $model->name ?></h1>
  <?= $model->body ?>
</div>
</div>