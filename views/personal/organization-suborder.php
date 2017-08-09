<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\OrganizationPayerAssignment;

/* @var $this yii\web\View */

$this->title = 'Подведомственность организации';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-md-10 col-md-offset-1">
    <div class="container-fluid">
        <div class="row">
            <?php
            $suborder = $model->organizationPayerAssignment;
            ?>
            <?php if (!empty($suborder) && $suborder->status == OrganizationPayerAssignment::STATUS_PENDING): ?>
                <h3>Подвердите подведомственность региону <b><?= $suborder->organization->municipality->name ?></b></h3>
                <a href="<?= Url::to(['organization-set-suborder-status']) ?>" class="btn btn-success">Подтвердить</a> <a href="<?= Url::to(['organization-set-suborder-status', 'refuse' => 1]) ?>" class="btn btn-danger">Отклонить</a>
            <?php elseif (!empty($suborder) && $suborder->status == OrganizationPayerAssignment::STATUS_ACTIVE): ?>
                <h3>Ваш регион <b><?= $suborder->organization->municipality->name ?>.</h3>
            <?php else: ?>
                <h4>Ни один плательщик еще не указал вас как свою подведомственную организацию.</h4>
            <?php endif; ?>
        </div>
    </div>
</div>