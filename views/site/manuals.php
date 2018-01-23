<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models \app\models\Help[] */

$this->title = 'Основные правила работы';
?>
<div class="site-manual row">
    <div class="col-md-10 col-md-offset-1">
        <?php if ($models): ?>
            <h3><?= $this->title ?></h3><br/>
            <ul>
                <?php foreach ($models as $index => $model): ?>
                    <li>Раздел «<a target="_blank" href="<?= \yii\helpers\Url::to(['site/manual', 'id' => $model->id]) ?>"><?= $model->name ?></a>».</li>
                <?php endforeach; ?>
            </ul>
            <?= Html::a('Выгрузить в формате pdf', '/site/download-manuals', ['class' => 'btn btn-primary']) ?>
        <?php else: ?>
            <h3>Инструкции по работе в личном кабинете не найдено.</h3>
        <?php endif; ?>
    </div>
</div>