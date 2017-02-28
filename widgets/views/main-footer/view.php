<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>
<footer>
  <div class="container-fluid footers">
     <div class="row">
         <div class="col-md-2 col-md-offset-2 text-center">Сопровождение Портала:<br>
             <?= Html::a($operator['name'], Url::to(['operators/view', 'id' => $operator['id']])) ?></div>
         <div class="col-md-2 text-center">Контактный телефон:<br><div class="phone"><?= $operator['phone'] ?></div></div>
         <div class="col-md-2 text-center">E-mail:<br><a href="mailto:<?= $operator['email'] ?>"><?= $operator['email'] ?></a>
                                                 <br><a href="mailto:<?= Yii::$app->params['adminEmail']; ?>"><?= Yii::$app->params['adminEmail']; ?></a></div>
         <div class="col-md-2 text-center">Адрес:<br><?= $operator['address_actual'] ?></div>
     </div>
  </div>
</footer>
