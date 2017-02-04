<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

$operator = (new \yii\db\Query())
                ->select(['email'])
                ->from('operators')
                ->one();
?>
<div class="site-error text-center">

   <h1>
        <?= nl2br(Html::encode($message)) ?>
    </h1>
    
    <h3><?= Html::encode($this->title) ?></h3>


    <p>
        Во время обработки вашего запроса произошла ошибка.
    </p>
    <p>
        Если ошибка повторится, свяжитесь с оператором, <a href="mailto:<?= $operator['email'] ?>"><?= $operator['email'] ?></a>. Спасибо.
    </p>

</div>
