<?php
use yii\helpers\Html;
use app\models\Contracts;
use app\models\Certificates;

/* @var $this yii\web\View */

$this->title = 'Информация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-1 well">
            <p><label class="control-label">Наименование организации</label> - <?= $payer['name'] ?></p>
            <p><label class="control-label">ИНН</label> - <?= $payer['INN'] ?></p>
            <p><label class="control-label">КПП</label> - <?= $payer['KPP'] ?></p>
            <p><label class="control-label">ОГРН</label> - <?= $payer['OGRN'] ?></p>
            <p><label class="control-label">ОКПО</label> - <?= $payer['OKPO'] ?></p>
            <p><label class="control-label">Юридический адрес</label> - <?= $payer['address_legal'] ?></p>
            <p><label class="control-label">Фактический адрес</label> - <?= $payer['address_actual'] ?></p>
            <p><label class="control-label">Представитель организации</label> - <?= $payer['fio'] ?></p>
            <p><label class="control-label">Номер телефона</label> - <?= $payer['phone'] ?></p>
            <p><label class="control-label">E-mail</label> - <?= $payer['email'] ?></p>
            <p>
              <?= Html::a('Редактировать', ['/payers/edit', 'id' => $payer['id']], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <?php if ($this->beginCache('payer-statistic-' . $payer->id, ['duration' => 3600])): ?>
        <div class="col-md-5  col-md-offset-1 well">
            <p>Количество выданных сертификатов - <?= Certificates::getCountCertificates($payer->id) ?></p>
            <p>Общая сумма выданных сертификатов - <?= Certificates::getSumCertificates($payer->id) ?></p>
            <p>Количество выданных сертификатов по которым заключены договора на обучение - <?= Contracts::getCountUsedCertificates(null, ['payerId' => $payer->id]) ?></p>
            <p>Количество детей обучающихся по одной образовательной программе с использованием выданных сертификатов - <?= Contracts::getCountUsedCertificates('=1', ['payerId' => $payer->id]) ?></p>
            <p>Количество детей обучающихся по двум образовательным программам с использованием выданных сертификатов - <?= Contracts::getCountUsedCertificates('=2', ['payerId' => $payer->id]) ?></p>
            <p>Количество детей обучающихся по трем и более образовательным программам с использованием выданных сертификатов - <?= Contracts::getCountUsedCertificates('>2', ['payerId' => $payer->id]) ?></p>
            <p>Общее количество договоров обучения заключенных с использованием выданных сертификатов - <?= Contracts::getCountContracts(['payerId' => $payer->id]) ?></p>
        </div>
        <?php $this->endCache(); ?>
        <?php endif; ?>
    </div>
</div>