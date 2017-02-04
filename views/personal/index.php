<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<h1>Личный кабинет администратора</h1>

<p>
    <?= Html::a('Правила доступа', ['/permit/access/permission'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Управление ролями', ['/permit/access/role'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Список пользователей', ['/user'], ['class' => 'btn btn-success']) ?>
</p>
<p>
    <?= Html::a('GII', ['/gii'], ['class' => 'btn btn-success']) ?>
</p>

<?php
    // echo "<pre>";
    //$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    //var_dump($roles);
    //echo "</pre>";
?>
