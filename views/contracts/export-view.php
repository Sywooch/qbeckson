<?php
use yii\helpers\Html;

/**
 * @var string $icon
 * @var string $file
 * @var string $href
 */

\app\models\ContractDocument::createInstance($file);
?>
<div class="alert alert-success alert-dismissible" role="alert" style="margin:10px 0">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>Готовый файл: </strong>
    <span class="h4" data-toggle="tooltip" title="Загрузите файл">
        <?= Html::a("<i class='{$icon}'></i> {$file}", $href, ['class' => 'label label-success']) ?>
    </span>
</div>