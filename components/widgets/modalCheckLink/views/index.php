<?php
/**
 *
 * @var  $link          string
 * @var  $title         string
 * @var  $content       string
 * @var  $label       string
 * @var  $buttonOptions array
 *
 */

use yii\bootstrap\Modal;

Modal::begin([
    'header'       => '<h2>' . $title . '</h2>',
    'toggleButton' => $buttonOptions,
]);
?>
    <div ng-app>
        <p><?= $content ?></p>
        <div class="form-group">
            <?= \yii\helpers\Html::checkbox('', false, ['ng-model' => 'checkedEnshure', 'checked-enshure']) ?>
            <?= \yii\helpers\Html::label($label, '#checked-enshure') ?>
        </div>


        <div ng-show="checkedEnshure"><?= $link ?></div>


    </div>
<?php

Modal::end();