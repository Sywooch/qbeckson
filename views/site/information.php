<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $result \app\models\CertificateInformation */

$this->title = 'Информация о получении сертификата';
?>
<?php if (null === $result) : ?>
    <p class="lead">К сожалению информация о получении сертификата в Вашем муниципалитете временно отсутствует</p>
<?php else : ?>
    <?php $labels = $result->attributeLabels(); ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <p>
                <strong><?= $labels['children_category']; ?>:</strong><br>
                <?= $result->children_category ?>
            </p>
            <p>
                <strong><?= $labels['organization_name']; ?>:</strong><br>
                <?= $result->organization_name ?>
            </p>
            <p>
                <strong><?= $labels['work_time']; ?>:</strong><br>
                <?= $result->work_time ?>
            </p>
            <p>
                <strong><?= $labels['full_name']; ?>:</strong><br>
                <?= $result->full_name ?>
            </p>
            <p>
                <strong><?= $labels['rules']; ?>:</strong><br>
                <?= nl2br($result->rules) ?>
            </p>
            <p>
                <strong><?= $labels['statementFile']; ?>:</strong>
                <?= Html::a('Скачать', $result->getStatementFile()) ?>
            </p>
        </div>
    </div>
<?php endif; ?>
