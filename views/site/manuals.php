<?php

use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $models \app\models\Help[] */

$this->title = 'Основные правила работы';
?>
<div class="site-manual row">
    <div class="col-md-10 col-md-offset-1">
        <h3><?= $this->title ?></h3><br/>
        <ul>
            <?php foreach ($models as $index => $model): ?>
                <li><?= $model->getAttributeLabel('checked') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>